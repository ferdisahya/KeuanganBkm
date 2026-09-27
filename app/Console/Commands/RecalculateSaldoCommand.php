<?php

namespace App\Console\Commands;

use App\Models\KasBkm;
use App\Models\KasRaab;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateSaldoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kas:recalculate-saldo 
                            {type? : Pilih type: bkm, raab, atau all (default: all)}
                            {--from-date= : Hitung ulang mulai dari tanggal ini (format: YYYY-MM-DD)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate saldo untuk semua transaksi Kas BKM dan/atau Kas RAAB secara manual';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type') ?? 'all';
        $fromDate = $this->option('from-date');

        $this->info('🔄 Memulai perhitungan ulang saldo...');
        $this->newLine();

        if (in_array($type, ['bkm', 'all'])) {
            $this->info('📊 Menghitung ulang Kas BKM...');
            $countBkm = $this->recalculateBkm($fromDate);
            $this->info("✅ Berhasil update {$countBkm} transaksi Kas BKM");
            $this->newLine();
        }

        if (in_array($type, ['raab', 'all'])) {
            $this->info('📊 Menghitung ulang Kas RAAB...');
            $countRaab = $this->recalculateRaab($fromDate);
            $this->info("✅ Berhasil update {$countRaab} transaksi Kas RAAB");
            $this->newLine();
        }

        $this->info('🎉 Perhitungan ulang saldo selesai!');

        return Command::SUCCESS;
    }

    /**
     * Recalculate Kas BKM balances
     */
    private function recalculateBkm(?string $fromDate): int
    {
        $query = KasBkm::query();
        
        if ($fromDate) {
            $query->where('tanggal', '>=', $fromDate);
            // Ambil saldo terakhir sebelum from_date
            $saldoAwal = KasBkm::where('tanggal', '<', $fromDate)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->value('saldo_kas') ?? 0;
        } else {
            $saldoAwal = 0;
        }

        $transaksis = $query->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $saldoBerjalan = $saldoAwal;
        $count = 0;

        foreach ($transaksis as $transaksi) {
            if ($transaksi->jenis_transaksi === 'debit') {
                $saldoBerjalan += $transaksi->nominal;
            } else {
                $saldoBerjalan -= $transaksi->nominal;
            }

            DB::table('kas_bkm')
                ->where('id', $transaksi->id)
                ->update([
                    'saldo_kas' => $saldoBerjalan,
                    'updated_at' => now(),
                ]);

            $count++;
        }

        return $count;
    }

    /**
     * Recalculate Kas RAAB balances
     */
    private function recalculateRaab(?string $fromDate): int
    {
        $query = KasRaab::query();
        
        if ($fromDate) {
            $query->where('tanggal', '>=', $fromDate);
            // Ambil saldo terakhir sebelum from_date
            $saldoAwal = KasRaab::where('tanggal', '<', $fromDate)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;
        } else {
            $saldoAwal = 0;
        }

        $transaksis = $query->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $saldoBerjalan = $saldoAwal;
        $count = 0;

        foreach ($transaksis as $transaksi) {
            if ($transaksi->jenis_transaksi === 'debit') {
                $saldoBerjalan += $transaksi->nominal;
            } else {
                $saldoBerjalan -= $transaksi->nominal;
            }

            DB::table('kas_raab')
                ->where('id', $transaksi->id)
                ->update([
                    'saldo' => $saldoBerjalan,
                    'updated_at' => now(),
                ]);

            $count++;
        }

        return $count;
    }
}
