<?php

namespace App\Http\Controllers;

use App\Models\KasRaab;
use App\Models\RekeningRaab;
use App\Models\SubrekeningRaab;
use App\Models\Lokasi;
use App\Models\Toko;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class KasRaabController extends Controller
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
        $query = KasRaab::with(['rekeningRaab', 'subrekeningRaab', 'lokasi', 'toko'])->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            
            // Search across cross-connection models safely
            $matchingLokasiKodes = Lokasi::where('nama', 'like', '%' . $search . '%')
                ->orWhere('kode', 'like', '%' . $search . '%')
                ->pluck('kode');
            $matchingTokoKodes = Toko::where('nama', 'like', '%' . $search . '%')
                ->orWhere('kode', 'like', '%' . $search . '%')
                ->pluck('kode');

            $query->where(function($q) use ($search, $matchingLokasiKodes, $matchingTokoKodes) {
                $q->where('uraian', 'like', '%' . $search . '%')
                  ->orWhere('nomer_lokasi', 'like', '%' . $search . '%')
                  ->orWhere('no_supplier', 'like', '%' . $search . '%')
                  ->orWhereHas('rekeningRaab', function($rk) use ($search) {
                      $rk->where('nama_rekening', 'like', '%' . $search . '%')
                         ->orWhere('nomer_rekening', 'like', '%' . $search . '%');
                  })
                  ->orWhereHas('subrekeningRaab', function($sr) use ($search) {
                      $sr->where('nama_subrekening', 'like', '%' . $search . '%')
                         ->orWhere('nomer_subrekening', 'like', '%' . $search . '%');
                  });

                if ($matchingLokasiKodes->isNotEmpty()) {
                    $q->orWhereIn('nomer_lokasi', $matchingLokasiKodes);
                }
                if ($matchingTokoKodes->isNotEmpty()) {
                    $q->orWhereIn('no_supplier', $matchingTokoKodes);
                }
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

        return view('kas_raab.index', compact('transaksis', 'totalDebet', 'totalKredit', 'saldoAkhir'));
    }

    public function create()
    {
        $rekenings = RekeningRaab::orderBy('nomer_rekening')->get();
        $subrekenings = SubrekeningRaab::orderBy('nomer_subrekening')->get();
        $lokasis = Lokasi::orderBy('kode')->get();
        $tokos = Toko::orderBy('kode')->get();
        $today = now()->format('Y-m-d');

        return view('kas_raab.create', compact('rekenings', 'subrekenings', 'lokasis', 'tokos', 'today'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'nominal' => 'required|numeric|min:1',
            'rekening_raab_id' => 'required|exists:rekening_raab,id',
            'subrekening_raab_id' => 'required|exists:subrekening_raab,id',
            'nomer_lokasi' => 'nullable',
            'no_supplier' => 'nullable',
            'lokasi_id' => 'nullable',
            'toko_id' => 'nullable',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'uraian.required' => 'Uraian transaksi RAAB wajib diisi.',
            'jenis_transaksi.required' => 'Pilih jenis transaksi (Debit / Kredit).',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'rekening_raab_id.required' => 'Pilih Rekening RAAB.',
            'subrekening_raab_id.required' => 'Pilih Subrekening RAAB.',
        ]);

        $nomerLokasi = $request->nomer_lokasi ?: null;
        $noSupplier = $request->no_supplier ?: null;
        $lokasiId = $request->lokasi_id ?: ($nomerLokasi ? Lokasi::where('kode', $nomerLokasi)->value('id') : null);
        $tokoId = $request->toko_id ?: ($noSupplier ? Toko::where('kode', $noSupplier)->value('id') : null);

        // Observer akan otomatis hitung debet, kredit, dan saldo
        $kasRaab = KasRaab::create([
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'rekening_raab_id' => $request->rekening_raab_id,
            'subrekening_raab_id' => $request->subrekening_raab_id,
            'nomer_lokasi' => $nomerLokasi,
            'no_supplier' => $noSupplier,
            'lokasi_id' => $lokasiId,
            'toko_id' => $tokoId,
        ]);

        $jenis = $request->jenis_transaksi === 'debit' ? 'Penerimaan' : 'Pengeluaran';
        $logMsg = 'Menambahkan entri Kas RAAB: ' . $kasRaab->uraian . ' (' . $jenis . ' Rp ' . number_format($request->nominal, 0, ',', '.') . ', Saldo Otomatis: Rp ' . number_format($kasRaab->saldo, 0, ',', '.') . ')';
        $successMsg = 'Data Kas RAAB berhasil disimpan! Saldo dihitung otomatis.';
        
        if ($request->jenis_transaksi === 'kredit') {
            $logMsg .= ' (Otomatis masuk Pengajuan Dana RAAB)';
            $successMsg .= ' Transaksi pengeluaran otomatis masuk ke Pengajuan Dana RAAB.';
        }

        $this->catatLog('Tambah Kas RAAB', $logMsg);

        return redirect()->route('kas_raab.index')->with('success', $successMsg);
    }

    public function edit(KasRaab $kasRaab)
    {
        $rekenings = RekeningRaab::orderBy('nomer_rekening')->get();
        $subrekenings = SubrekeningRaab::orderBy('nomer_subrekening')->get();
        $lokasis = Lokasi::orderBy('kode')->get();
        $tokos = Toko::orderBy('kode')->get();

        return view('kas_raab.edit', compact('kasRaab', 'rekenings', 'subrekenings', 'lokasis', 'tokos'));
    }

    public function update(Request $request, KasRaab $kasRaab)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'jenis_transaksi' => 'required|in:debit,kredit',
            'nominal' => 'required|numeric|min:1',
            'rekening_raab_id' => 'required|exists:rekening_raab,id',
            'subrekening_raab_id' => 'required|exists:subrekening_raab,id',
            'nomer_lokasi' => 'nullable',
            'no_supplier' => 'nullable',
            'lokasi_id' => 'nullable',
            'toko_id' => 'nullable',
        ], [
            'tanggal.required' => 'Tanggal wajib diisi.',
            'uraian.required' => 'Uraian transaksi RAAB wajib diisi.',
            'jenis_transaksi.required' => 'Pilih jenis transaksi (Debit / Kredit).',
            'nominal.required' => 'Nominal wajib diisi.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'rekening_raab_id.required' => 'Pilih Rekening RAAB.',
            'subrekening_raab_id.required' => 'Pilih Subrekening RAAB.',
        ]);

        $nomerLokasi = $request->nomer_lokasi ?: null;
        $noSupplier = $request->no_supplier ?: null;
        $lokasiId = $request->lokasi_id ?: ($nomerLokasi ? Lokasi::where('kode', $nomerLokasi)->value('id') : null);
        $tokoId = $request->toko_id ?: ($noSupplier ? Toko::where('kode', $noSupplier)->value('id') : null);

        // Observer akan otomatis hitung ulang debet, kredit, dan saldo
        $kasRaab->update([
            'tanggal' => $request->tanggal,
            'uraian' => $request->uraian,
            'jenis_transaksi' => $request->jenis_transaksi,
            'nominal' => $request->nominal,
            'rekening_raab_id' => $request->rekening_raab_id,
            'subrekening_raab_id' => $request->subrekening_raab_id,
            'nomer_lokasi' => $nomerLokasi,
            'no_supplier' => $noSupplier,
            'lokasi_id' => $lokasiId,
            'toko_id' => $tokoId,
        ]);

        $this->catatLog('Ubah Kas RAAB', 'Memperbarui entri Kas RAAB ID #' . $kasRaab->id . ' — Saldo otomatis dihitung ulang');

        return redirect()->route('kas_raab.index')->with('success', 'Data Kas RAAB berhasil diperbarui! Saldo telah dihitung ulang otomatis.');
    }

    public function destroy(KasRaab $kasRaab)
    {
        $uraian = $kasRaab->uraian;
        $kasRaab->delete();

        $this->catatLog('Hapus Kas RAAB', 'Menghapus entri Kas RAAB: ' . $uraian);

        return redirect()->route('kas_raab.index')->with('success', 'Data Kas RAAB berhasil dihapus.');
    }
}
