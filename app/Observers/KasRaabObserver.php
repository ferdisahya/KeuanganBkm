<?php

namespace App\Observers;

use App\Models\KasRaab;
use App\Models\PengajuanDanaRaab;
use Illuminate\Support\Facades\DB;

class KasRaabObserver
{
    /**
     * Handle the KasRaab "creating" event.
     * Sebelum data disimpan, hitung saldo otomatis dan update debet/kredit
     */
    public function creating(KasRaab $kasRaab): void
    {
        // Set debet/kredit berdasarkan jenis_transaksi dan nominal
        if ($kasRaab->jenis_transaksi === 'debit') {
            $kasRaab->debet = $kasRaab->nominal;
            $kasRaab->kredit = 0;
        } else {
            $kasRaab->debet = 0;
            $kasRaab->kredit = $kasRaab->nominal;
        }

        // Hitung saldo dari transaksi terakhir sebelum tanggal ini
        $saldoSebelumnya = KasRaab::where('tanggal', '<', $kasRaab->tanggal)
            ->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;

        // Hitung saldo baru
        if ($kasRaab->jenis_transaksi === 'debit') {
            $kasRaab->saldo = $saldoSebelumnya + $kasRaab->nominal;
        } else {
            $kasRaab->saldo = $saldoSebelumnya - $kasRaab->nominal;
        }
    }

    /**
     * Handle the KasRaab "created" event.
     * Setelah data tersimpan, hitung ulang semua saldo setelah tanggal ini
     */
    public function created(KasRaab $kasRaab): void
    {
        // Auto-create PengajuanDanaRaab jika kredit
        if ($kasRaab->jenis_transaksi === 'kredit' && $kasRaab->nominal > 0) {
            PengajuanDanaRaab::create([
                'tanggal' => $kasRaab->tanggal,
                'rekening_raab_id' => $kasRaab->rekening_raab_id,
                'uraian' => $kasRaab->uraian,
                'total' => $kasRaab->nominal,
            ]);
        }

        // Recalculate semua transaksi setelah tanggal ini
        $this->recalculateBalancesAfter($kasRaab->tanggal, $kasRaab->id);
    }

    /**
     * Handle the KasRaab "updating" event.
     */
    public function updating(KasRaab $kasRaab): void
    {
        // Update debet/kredit berdasarkan jenis_transaksi
        if ($kasRaab->isDirty(['jenis_transaksi', 'nominal'])) {
            if ($kasRaab->jenis_transaksi === 'debit') {
                $kasRaab->debet = $kasRaab->nominal;
                $kasRaab->kredit = 0;
            } else {
                $kasRaab->debet = 0;
                $kasRaab->kredit = $kasRaab->nominal;
            }

            // Hitung ulang saldo untuk transaksi ini
            $saldoSebelumnya = KasRaab::where('tanggal', '<', $kasRaab->tanggal)
                ->where('id', '!=', $kasRaab->id)
                ->orderBy('tanggal', 'desc')
                ->orderBy('id', 'desc')
                ->value('saldo') ?? 0;

            if ($kasRaab->jenis_transaksi === 'debit') {
                $kasRaab->saldo = $saldoSebelumnya + $kasRaab->nominal;
            } else {
                $kasRaab->saldo = $saldoSebelumnya - $kasRaab->nominal;
            }
        }
    }

    /**
     * Handle the KasRaab "updated" event.
     */
    public function updated(KasRaab $kasRaab): void
    {
        // Jika tanggal atau nominal berubah, recalculate semua setelahnya
        if ($kasRaab->wasChanged(['tanggal', 'nominal', 'jenis_transaksi'])) {
            $this->recalculateBalancesAfter($kasRaab->tanggal, $kasRaab->id);
        }
    }

    /**
     * Handle the KasRaab "deleted" event.
     */
    public function deleted(KasRaab $kasRaab): void
    {
        // Recalculate semua transaksi setelah tanggal ini
        $this->recalculateBalancesAfter($kasRaab->tanggal);
    }

    /**
     * Recalculate balances for all transactions after given date
     */
    private function recalculateBalancesAfter(string $tanggal, ?int $exceptId = null): void
    {
        // Ambil saldo terakhir sebelum tanggal ini (exclude current ID if updating)
        $query = KasRaab::where('tanggal', '<', $tanggal);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        $saldoSebelumnya = $query->orderBy('tanggal', 'desc')
            ->orderBy('id', 'desc')
            ->value('saldo') ?? 0;

        // Ambil semua transaksi dari tanggal ini dan setelahnya
        $transaksiQuery = KasRaab::where('tanggal', '>=', $tanggal);
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
            DB::table('kas_raab')
                ->where('id', $transaksi->id)
                ->update([
                    'saldo' => $saldoBerjalan,
                    'updated_at' => now(),
                ]);
        }
    }
}
