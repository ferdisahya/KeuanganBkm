<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RekeningBkm;
use App\Models\RekeningRaab;
use App\Models\SubrekeningBkm;
use App\Models\SubrekeningRaab;
use App\Models\ItemBkm;
use App\Models\KasBkm;
use App\Models\KasRaab;
use App\Models\SisaAnggaranBkm;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@ptbkm.co.id'],
            [
                'name' => 'Admin Keuangan',
                'role' => 'Admin Keuangan',
            ]
        );

        // 2. Seed Rekening BKM
        $rekBkm1 = RekeningBkm::create(['nomer_rekening' => '110-01', 'nama_rekening' => 'Kas Utama PT BKM']);
        $rekBkm2 = RekeningBkm::create(['nomer_rekening' => '110-02', 'nama_rekening' => 'Bank Mandiri BKM Operational']);

        // 3. Seed Subrekening BKM
        $subBkm1 = SubrekeningBkm::create(['nomer_subrekening' => '110-01-01', 'nama_subrekening' => 'Penerimaan Proyek BKM']);
        $subBkm2 = SubrekeningBkm::create(['nomer_subrekening' => '110-01-02', 'nama_subrekening' => 'Pengeluaran Operasional']);

        // 4. Seed Item BKM
        $item1 = ItemBkm::create(['nomer_item' => 'ITM-001', 'nama_item' => 'Pembayaran Termin Proyek']);
        $item2 = ItemBkm::create(['nomer_item' => 'ITM-002', 'nama_item' => 'Pembelian Alat K3']);
        $item3 = ItemBkm::create(['nomer_item' => 'ITM-003', 'nama_item' => 'Biaya Logistik & Solar']);

        // 5. Seed Rekening RAAB
        $rekRaab1 = RekeningRaab::create(['nomer_rekening' => '210-01', 'nama_rekening' => 'Rekening RAAB Konstruksi']);
        $rekRaab2 = RekeningRaab::create(['nomer_rekening' => '210-02', 'nama_rekening' => 'Rekening RAAB Pemeliharaan']);

        // 6. Seed Subrekening RAAB
        $subRaab1 = SubrekeningRaab::create(['nomer_subrekening' => '210-01-01', 'nama_subrekening' => 'Anggaran Material Struktural']);
        $subRaab2 = SubrekeningRaab::create(['nomer_subrekening' => '210-01-02', 'nama_subrekening' => 'Anggaran Upah Tenaga Kerja']);

        // 7. Seed Kas BKM Data
        KasBkm::create([
            'tanggal' => '2026-08-01',
            'uraian' => 'Penerimaan Termin 1 Proyek Gedung BKM',
            'debet' => 150000000.00,
            'kredit' => null,
            'saldo_kas' => 150000000.00,
            'keterangan' => 'Transfer dari Owner Proyek PT Utama',
            'rekening_bkm_id' => $rekBkm1->id,
            'subrekening_bkm_id' => $subBkm1->id,
            'item_bkm_id' => $item1->id,
        ]);

        KasBkm::create([
            'tanggal' => '2026-08-10',
            'uraian' => 'Pembelian Perlengkapan APD & Helm Proyek',
            'debet' => null,
            'kredit' => 12500000.00,
            'saldo_kas' => 137500000.00,
            'keterangan' => 'Dibayarkan via Kasir Operasional',
            'rekening_bkm_id' => $rekBkm2->id,
            'subrekening_bkm_id' => $subBkm2->id,
            'item_bkm_id' => $item2->id,
        ]);

        // 8. Seed Kas RAAB Data
        KasRaab::create([
            'tanggal' => '2026-08-05',
            'uraian' => 'Pencairan Anggaran RAAB Tahap I',
            'debet' => 80000000.00,
            'kredit' => null,
            'saldo' => 80000000.00,
            'rekening_raab_id' => $rekRaab1->id,
            'subrekening_raab_id' => $subRaab1->id,
        ]);

        KasRaab::create([
            'tanggal' => '2026-08-15',
            'uraian' => 'Pembayaran Upah Mandor & Pekerja Minggu II',
            'debet' => null,
            'kredit' => 25000000.00,
            'saldo' => 55000000.00,
            'rekening_raab_id' => $rekRaab1->id,
            'subrekening_raab_id' => $subRaab2->id,
        ]);

        // 9. Seed Sisa Anggaran BKM
        SisaAnggaranBkm::create([
            'nama' => 'Sisa Anggaran Belanja Solar Generator',
            'harga' => 15000000.00,
            'keterangan' => 'Anggaran efisiensi pembelian solar bulan Juli',
        ]);

        SisaAnggaranBkm::create([
            'nama' => 'Sisa Anggaran Sewa Scaffolding',
            'harga' => 8500000.00,
            'keterangan' => 'Pengembalian deposit unit sewa',
        ]);
    }
}
