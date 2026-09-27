<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KasRaab;
use App\Models\RekeningRaab;
use App\Models\SubrekeningRaab;
use App\Models\Lokasi;
use App\Models\Toko;
use Illuminate\Support\Facades\DB;

class ImportKasRaabCommand extends Command
{
    protected $signature = 'import:kas-raab {--file=extracted_raab_rows.json}';
    protected $description = 'Import data transaksi Kas RAAB dan master data (rekening, subrekening, lokasi, toko)';

    public function handle()
    {
        $file = base_path($this->option('file'));
        if (!file_exists($file)) {
            $this->error("File tidak ditemukan: $file");
            return 1;
        }

        $rows = json_decode(file_get_contents($file), true);
        if (!$rows) {
            $this->error("Format JSON tidak valid atau kosong.");
            return 1;
        }

        $this->info("Memproses " . count($rows) . " baris transaksi Kas RAAB...");

        // 1. Sync Lokasi
        $this->info("Sinkronisasi Master Lokasi ke logistik...");
        $lokasiMap = [];
        foreach ($rows as $r) {
            $kode = trim($r['no_lokasi'] ?? '');
            $nama = trim($r['lokasi'] ?? '');
            if ($kode && $kode !== '#N/A' && !isset($lokasiMap[$kode])) {
                $lokasi = Lokasi::firstOrCreate(
                    ['kode' => $kode],
                    [
                        'nama' => $nama ?: $kode,
                        'is_workshop' => stripos($nama, 'workshop') !== false ? 1 : 0,
                        'aktif' => 1,
                    ]
                );
                $lokasiMap[$kode] = $lokasi->id;
            }
        }
        $this->info("Total Master Lokasi dipetakan: " . count($lokasiMap));

        // 2. Sync Toko / Supplier
        $this->info("Sinkronisasi Master Toko / Supplier ke logistik...");
        $tokoMap = [];
        foreach ($rows as $r) {
            $kode = trim($r['no_supplier'] ?? '');
            $nama = trim($r['supplier'] ?? '');
            if ($kode && $kode !== '#N/A' && !isset($tokoMap[$kode])) {
                $toko = Toko::firstOrCreate(
                    ['kode' => $kode],
                    ['nama' => $nama ?: $kode]
                );
                $tokoMap[$kode] = $toko->id;
            }
        }
        $this->info("Total Master Toko dipetakan: " . count($tokoMap));

        // 3. Sync Rekening RAAB
        $this->info("Sinkronisasi Master Rekening RAAB...");
        $rekMap = [];
        foreach ($rows as $r) {
            $noRek = trim($r['no_rek'] ?? '');
            $namaRek = trim($r['nama_rek'] ?? '');
            if ($noRek && !isset($rekMap[$noRek])) {
                $rek = RekeningRaab::firstOrCreate(
                    ['nomer_rekening' => $noRek],
                    ['nama_rekening' => $namaRek ?: null]
                );
                $rekMap[$noRek] = $rek->id;
            }
        }
        $this->info("Total Master Rekening RAAB dipetakan: " . count($rekMap));

        // 4. Sync Subrekening RAAB
        $this->info("Sinkronisasi Master Subrekening RAAB...");
        $subrekMap = [];
        foreach ($rows as $r) {
            $noSub = trim($r['no_sub_rek'] ?? '');
            $namaSub = trim($r['nama_sub_rek'] ?? '');
            if ($noSub && !isset($subrekMap[$noSub])) {
                $subrek = SubrekeningRaab::firstOrCreate(
                    ['nomer_subrekening' => $noSub],
                    ['nama_subrekening' => $namaSub ?: null]
                );
                $subrekMap[$noSub] = $subrek->id;
            }
        }
        $this->info("Total Master Subrekening RAAB dipetakan: " . count($subrekMap));

        // 5. Insert Transaksi Kas RAAB
        $this->info("Menyimpan transaksi ke kas_raab...");
        $inserted = 0;
        
        DB::beginTransaction();
        try {
            foreach ($rows as $r) {
                $dParts = explode('/', $r['tanggal']);
                $tgl = count($dParts) === 3 ? "{$dParts[2]}-{$dParts[1]}-{$dParts[0]}" : $r['tanggal'];

                $noRek = trim($r['no_rek'] ?? '');
                $noSub = trim($r['no_sub_rek'] ?? '');
                $noLok = trim($r['no_lokasi'] ?? '');
                $noSup = trim($r['no_supplier'] ?? '');

                $rekId = $rekMap[$noRek] ?? null;
                $subrekId = $subrekMap[$noSub] ?? null;
                $lokId = ($noLok && $noLok !== '#N/A') ? ($lokasiMap[$noLok] ?? null) : null;
                $tokoId = ($noSup && $noSup !== '#N/A') ? ($tokoMap[$noSup] ?? null) : null;

                KasRaab::create([
                    'tanggal' => $tgl,
                    'uraian' => $r['uraian'],
                    'debet' => (float)($r['debet'] ?? 0) > 0 ? (float)$r['debet'] : null,
                    'kredit' => (float)($r['kredit'] ?? 0) > 0 ? (float)$r['kredit'] : null,
                    'saldo' => (float)($r['saldo'] ?? 0),
                    'rekening_raab_id' => $rekId,
                    'subrekening_raab_id' => $subrekId,
                    'lokasi_id' => $lokId,
                    'toko_id' => $tokoId,
                ]);
                $inserted++;
            }
            DB::commit();
            $this->info("BERHASIL! $inserted baris transaksi Kas RAAB berhasil diimpor.");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Gagal saat import transaksi: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
