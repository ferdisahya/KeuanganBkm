<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KasBkm;
use App\Models\KasRaab;
use App\Models\PengajuanDanaBkm;
use App\Models\PengajuanDanaRaab;

class SyncKasKePengajuanDanaCommand extends Command
{
    protected $signature = 'kas:sync-pengajuan {--fresh : Kosongkan tabel pengajuan dana terlebih dahulu}';
    protected $description = 'Sinkronisasi data transaksi kredit Kas BKM & Kas RAAB ke Pengajuan Dana BKM & Pengajuan Dana RAAB';

    public function handle()
    {
        $fresh = $this->option('fresh');

        $this->info("=== MEMULAI SINKRONISASI KAS KE PENGAJUAN DANA ===");

        // 1. Kas BKM -> Pengajuan Dana BKM
        $this->info("\n1. Memproses Kas BKM -> Pengajuan Dana BKM...");
        if ($fresh) {
            $this->warn("   Mengosongkan tabel pengajuandana_bkm (--fresh)...");
            PengajuanDanaBkm::truncate();
        }

        $kasBkmList = KasBkm::whereNotNull('kredit')
            ->where('kredit', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $countBkm = 0;
        $skippedBkm = 0;

        foreach ($kasBkmList as $kb) {
            $exists = false;
            if (!$fresh) {
                $exists = PengajuanDanaBkm::where('tanggal', $kb->tanggal)
                    ->where('uraian', $kb->uraian)
                    ->where('total', $kb->kredit)
                    ->where('rekening_bkm_id', $kb->rekening_bkm_id)
                    ->exists();
            }

            if (!$exists) {
                PengajuanDanaBkm::create([
                    'tanggal' => $kb->tanggal,
                    'rekening_bkm_id' => $kb->rekening_bkm_id,
                    'uraian' => $kb->uraian,
                    'total' => $kb->kredit,
                ]);
                $countBkm++;
            } else {
                $skippedBkm++;
            }
        }
        $this->info("   -> Selesai: $countBkm data baru dimasukkan, $skippedBkm data dilewati (sudah ada).");

        // 2. Kas RAAB -> Pengajuan Dana RAAB
        $this->info("\n2. Memproses Kas RAAB -> Pengajuan Dana RAAB...");
        if ($fresh) {
            $this->warn("   Mengosongkan tabel pengajuandana_raab (--fresh)...");
            PengajuanDanaRaab::truncate();
        }

        $kasRaabList = KasRaab::whereNotNull('kredit')
            ->where('kredit', '>', 0)
            ->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $countRaab = 0;
        $skippedRaab = 0;

        foreach ($kasRaabList as $kr) {
            $exists = false;
            if (!$fresh) {
                $exists = PengajuanDanaRaab::where('tanggal', $kr->tanggal)
                    ->where('uraian', $kr->uraian)
                    ->where('total', $kr->kredit)
                    ->where('rekening_raab_id', $kr->rekening_raab_id)
                    ->exists();
            }

            if (!$exists) {
                PengajuanDanaRaab::create([
                    'tanggal' => $kr->tanggal,
                    'rekening_raab_id' => $kr->rekening_raab_id,
                    'uraian' => $kr->uraian,
                    'total' => $kr->kredit,
                ]);
                $countRaab++;
            } else {
                $skippedRaab++;
            }
        }
        $this->info("   -> Selesai: $countRaab data baru dimasukkan, $skippedRaab data dilewati (sudah ada).");

        $this->info("\n=== SINKRONISASI SELESAI DENGAN SUKSES ===");
        return 0;
    }
}
