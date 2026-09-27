<?php

namespace App\Observers;

use App\Models\KasBkm;
use App\Models\PengajuanDanaBkm;
use Illuminate\Support\Facades\DB;

class KasBkmObserver
{
    /**
     * Handle the KasBkm "creating" event.
     * Sebelum data disimpan, hitung saldo otomatis dan update debet/kredit
     */
    public function creating(KasBkm $kasBkm): void
    {
        // Set debet/kredit berdasarkan jenis_transaksi dan nominal
        if ($kasBkm->jenis_transaksi === 'debit') {
            $kasBkm->debet = $kasBkm->nominal;
            $kasBkm->kredit = 0;
        } else {
            $kasBkm->debet = 0;
            $kasBkm->kredit = $kasBkm->nominal;
        }

        // Hitung saldo dari transaksi terakhir sebelum tanggal ini
        $saldoSebelumnya = KasBkm::where('tanggal', '<', $kasBkm->tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->value('saldo_kas') ?? 0;

        // Hitung saldo baru
        if ($kasBkm->jenis_transaksi === 'debit') {
            $kasBkm->saldo_kas = $saldoSebelumnya + $kasBkm->nominal;
        } else {
            $kasBkm->saldo_kas = $saldoSebelumnya - $kasBkm->nominal;
        }
    }

    /**
     * Handle the KasBkm "created" event.
     * Setelah data tersimpan, hitung ulang semua saldo setelah tanggal ini
     */
    public function created(KasBkm $kasBkm): void
    {
        // Auto-create PengajuanDanaBkm jika kredit
        if ($kasBkm->jenis_transaksi === 'kredit' && $kasBkm->nominal > 0) {
            PengajuanDanaBkm::create([
                'tanggal' => $kasBkm->tanggal,
                'rekening_bkm_id' => $kasBkm->rekening_bkm_id,
                'uraian' => $kasBkm->uraian,
                'total' => $kasBkm->nominal,
            ]);
        }

        // Recalculate semua transaksi setelah tanggal ini
        $this->recalculateBalancesAfter($kasBkm->tanggal, $kasBkm->id);
    }

    /**
     * Handle the KasBkm "updating" event.
     */
    public function updating(KasBkm $kasBkm): void
    {
        // Update debet/kredit berdasarkan jenis_transaksi
        if ($kasBkm->isDirty(['jenis_transaksi', 'nominal'])) {
            if ($kasBkm->jenis_transaksi === 'debit') {
                $kasBkm->debet = $kasBkm->nominal;
                $kasBkm->kredit = 0;
            } else {
                $kasBkm->debet = 0;
                $kasBkm->kredit = $kasBkm->nominal;
            }

            // Hitung ulang saldo untuk transaksi ini
            $saldoSebelumnya = KasBkm::where('tanggal', '<', $kasBkm->tanggal)
                ->where('id', '!=', $kasBkm->id)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->value('saldo_kas') ?? 0;

            if ($kasBkm->jenis_transaksi === 'debit') {
                $kasBkm->saldo_kas = $saldoSebelumnya + $kasBkm->nominal;
            } else {
                $kasBkm->saldo_kas = $saldoSebelumnya - $kasBkm->nominal;
            }
        }
    }

    /**
     * Handle the KasBkm "updated" event.
     */
    public function updated(KasBkm $kasBkm): void
    {
        // Jika tanggal atau nominal berubah, recalculate semua setelahnya
        if ($kasBkm->wasChanged(['tanggal', 'nominal', 'jenis_transaksi'])) {
            $this->recalculateBalancesAfter($kasBkm->tanggal, $kasBkm->id);
        }
    }

    /**
     * Handle the KasBkm "deleted" event.
     */
    public function deleted(KasBkm $kasBkm): void
    {
        // Recalculate semua transaksi setelah tanggal ini
        $this->recalculateBalancesAfter($kasBkm->tanggal);
    }

    /**
     * Recalculate balances for all transactions after given date
     */
    private function recalculateBalancesAfter(string $tanggal, ?int $exceptId = null): void
    {
        // Ambil saldo terakhir sebelum tanggal ini (exclude current ID if updating)
        $query = KasBkm::where('tanggal', '<', $tanggal);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        $saldoSebelumnya = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->value('saldo_kas') ?? 0;

        // Ambil semua transaksi dari tanggal ini dan setelahnya
        $transaksiQuery = KasBkm::where('tanggal', '>=', $tanggal);
        if ($exceptId) {
            $transaksiQuery->where('id', '!=', $exceptId);
        }
        $transaksis = $transaksiQuery->orderBy('tanggal', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $saldoBerjalan = $saldoSebelumnya;

        foreach ($transaksis as $transaksi) {
            if ($transaksi->jenis_transaksi === 'debit') {
                $saldoBerjalan += $transaksi->nominal;
            } else {
                $saldoBerjalan -= $transaksi->nominal;
            }

            // Update tanpa trigger observer lagi (gunakan DB raw)
            DB::table('kas_bkm')
                ->where('id', $transaksi->id)
                ->update([
                    'saldo_kas' => $saldoBerjalan,
                    'updated_at' => now(),
                ]);
        }
    }
}
