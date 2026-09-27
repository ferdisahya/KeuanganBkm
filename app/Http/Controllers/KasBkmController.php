<?php

namespace App\Http\Controllers;

use App\Models\KasBkm;
use App\Models\RekeningBkm;
use App\Models\SubrekeningBkm;
use App\Models\ItemBkm;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class KasBkmController extends Controller
{
    private function catatLog($action, $description)
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    public function index(Request $request)
    {
        $query = KasBkm::with(['rekeningBkm', 'subrekeningBkm', 'itemBkm'])->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uraian', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%')
                  ->orWhereHas('rekeningBkm', function($rk) use ($search) {
                      $rk->where('nama_rekening', 'like', '%' . $search . '%')
                         ->orWhere('nomer_rekening', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('subrekeningBkm', function($sr) use ($search) {
                      $sr->where('nama_subrekening', 'like', '%' . $search . '%')
                         ->orWhere('nomer_subrekening', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('itemBkm', function($it) use ($search) {
                      $it->where('nama_item', 'like', '%' . $search . '%')
                         ->orWhere('nomer_item', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        } elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)->whereYear('tanggal', $request->tahun);
        } elseif ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
        }

        // Calculate Adaptive Totals based on active filters
        $totalDebet = (float)((clone $query)->sum('debet') ?? 0);
        $totalKredit = (float)((clone $query)->sum('kredit') ?? 0);
        $saldoAkhir = $totalDebet - $totalKredit;

        $transaksis = $query->paginate(15)->withQueryString();

        return view('kas_bkm.index', compact('transaksis', 'totalDebet', 'totalKredit', 'saldoAkhir'));
    }

    public function create()
    {
        $rekenings = RekeningBkm::all();
        $subrekenings = SubrekeningBkm::all();
        $items = ItemBkm::all();
        $today = now()->format('Y-m-d');

        return view('kas_bkm.create', compact('rekenings', 'subrekenings', 'items', 'today'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'rekening_bkm_id' => 'required|exists:rekening_bkm,id',
            'subrekening_bkm_id' => 'required|exists:subrekening_bkm,id',
            'item_bkm_id' => 'required|exists:item_bkm,id',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'uraian.required' => 'Uraian transaksi wajib diisi.',
            'jenis_transaksi.required' => 'Pilih jenis transaksi (Debit / Kredit).',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'rekening_bkm_id.required' => 'Pilih Rekening BKM.',
            'subrekening_bkm_id.required' => 'Pilih Subrekening BKM.',
            'item_bkm_id.required' => 'Pilih Item BKM.',
        ]);

        // Observer akan otomatis hitung debet, kredit, dan saldo_kas
        $kasBkm = KasBkm::create([
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
            'rekening_bkm_id' => $request->rekening_bkm_id,
            'subrekening_bkm_id' => $request->subrekening_bkm_id,
            'item_bkm_id' => $request->item_bkm_id,
        ]);

        $jenis = $request->jenis_transaksi === 'debit' ? 'Penerimaan' : 'Pengeluaran';
        $logMsg = 'Menambahkan entri Kas BKM: ' . $kasBkm->uraian . ' (' . $jenis . ' Rp ' . number_format($request->nominal, 0, ',', '.') . ', Saldo Otomatis: Rp ' . number_format($kasBkm->saldo_kas, 0, ',', '.') . ')';
        $successMsg = 'Data Kas BKM berhasil disimpan! Saldo dihitung otomatis.';
        
        if ($request->jenis_transaksi === 'kredit') {
            $logMsg .= ' (Otomatis masuk Pengajuan Dana BKM)';
            $successMsg .= ' Transaksi pengeluaran otomatis masuk ke Pengajuan Dana BKM.';
        }

        $this->catatLog('Tambah Kas BKM', $logMsg);

        return redirect()->route('kas_bkm.index')->with('success', $successMsg);
    }

    public function edit(KasBkm $kasBkm)
    {
        $rekenings = RekeningBkm::all();
        $subrekenings = SubrekeningBkm::all();
        $items = ItemBkm::all();

        return view('kas_bkm.edit', compact('kasBkm', 'rekenings', 'subrekenings', 'items'));
    }

    public function update(Request $request, KasBkm $kasBkm)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'nominal' => 'required|numeric|min:1',
            'keterangan' => 'nullable|string',
            'rekening_bkm_id' => 'required|exists:rekening_bkm,id',
            'subrekening_bkm_id' => 'required|exists:subrekening_bkm,id',
            'item_bkm_id' => 'required|exists:item_bkm,id',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'uraian.required' => 'Uraian transaksi wajib diisi.',
            'jenis_transaksi.required' => 'Pilih jenis transaksi (Debit / Kredit).',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'rekening_bkm_id.required' => 'Pilih Rekening BKM.',
            'subrekening_bkm_id.required' => 'Pilih Subrekening BKM.',
            'item_bkm_id.required' => 'Pilih Item BKM.',
        ]);

        // Observer akan otomatis hitung ulang debet, kredit, dan saldo_kas
        $kasBkm->update([
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'keterangan' => $request->keterangan,
            'rekening_bkm_id' => $request->rekening_bkm_id,
            'subrekening_bkm_id' => $request->subrekening_bkm_id,
            'item_bkm_id' => $request->item_bkm_id,
        ]);

        $this->catatLog('Ubah Kas BKM', 'Memperbarui entri Kas BKM ID #' . $kasBkm->id . ' — Saldo otomatis dihitung ulang');

        return redirect()->route('kas_bkm.index')->with('success', 'Data Kas BKM berhasil diperbarui! Saldo telah dihitung ulang otomatis.');
    }

    public function destroy(KasBkm $kasBkm)
    {
        $uraian = $kasBkm->uraian;
        $kasBkm->delete();

        $this->catatLog('Hapus Kas BKM', 'Menghapus entri Kas BKM: ' . $uraian);

        return redirect()->route('kas_bkm.index')->with('success', 'Data Kas BKM berhasil dihapus.');
    }
}
