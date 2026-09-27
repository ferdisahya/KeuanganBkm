<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDanaBkm;
use App\Models\RekeningBkm;
use App\Models\PenyaluranPembayaranBkm;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengajuanDanaBkmController extends Controller
{
    private function catatLog($action, $description)
    {
        if (auth()->check()) {
            LogAktivitas::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'description' => $description,
            ]);
        }
    }

    public function index(Request $request)
    {
        $query = PengajuanDanaBkm::with('rekeningBkm')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uraian', 'like', '%' . $search . '%')
                  ->orWhereHas('rekeningBkm', function($rk) use ($search) {
                      $rk->where('nama_rekening', 'like', '%' . $search . '%')
                         ->orWhere('nomer_rekening', 'like', '%' . $search . '%');
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

        $grandTotal = (clone $query)->sum('total') ?? 0;
        $pengajuans = $query->paginate(15)->withQueryString();
        $penyalurans = PenyaluranPembayaranBkm::orderBy('nama', 'asc')->get();

        return view('pengajuan_dana_bkm.index', compact('pengajuans', 'grandTotal', 'penyalurans'));
    }

    public function cetakPdf(Request $request)
    {
        $query = PengajuanDanaBkm::with('rekeningBkm')->orderBy('tanggal', 'asc');

        $periodeTitle = '';

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
            $tMulai = Carbon::parse($request->tanggal_mulai)->translatedFormat('d F Y');
            $tSelesai = Carbon::parse($request->tanggal_selesai)->translatedFormat('d F Y');
            $periodeTitle = "PERIODE TANGGAL " . strtoupper($tMulai) . " - " . strtoupper($tSelesai);
        } elseif ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
            $tMulai = Carbon::parse($request->tanggal)->translatedFormat('d F Y');
            $periodeTitle = "PERIODE TANGGAL " . strtoupper($tMulai);
        } elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('tanggal', $request->bulan)->whereYear('tanggal', $request->tahun);
            $mName = Carbon::createFromDate($request->tahun, $request->bulan, 1)->translatedFormat('F Y');
            $periodeTitle = "PERIODE " . strtoupper($mName);
        } elseif ($request->filled('tahun')) {
            $query->whereYear('tanggal', $request->tahun);
            $periodeTitle = "PERIODE TAHUN " . $request->tahun;
        } else {
            $periodeTitle = "PERIODE SELURUH TRANSAKSI";
        }

        $items = $query->get()->sort(function ($a, $b) {
            if ($a->tanggal != $b->tanggal) {
                return strcmp($a->tanggal, $b->tanggal);
            }
            $rekA = ($a->rekeningBkm->nomer_rekening ?? '') . ' ' . ($a->rekeningBkm->nama_rekening ?? '');
            $rekB = ($b->rekeningBkm->nomer_rekening ?? '') . ' ' . ($b->rekeningBkm->nama_rekening ?? '');
            $cmp = strnatcasecmp($rekA, $rekB);
            if ($cmp !== 0) return $cmp;
            return strnatcasecmp($a->uraian ?? '', $b->uraian ?? '');
        })->values();

        $penyaluran = null;
        if ($request->filled('penyaluran_id')) {
            $penyaluran = PenyaluranPembayaranBkm::find($request->penyaluran_id);
        }
        if (!$penyaluran) {
            $penyaluran = PenyaluranPembayaranBkm::first();
        }

        $grouped = $items->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
        });
        $grandTotal = $items->sum('total');

        return view('pengajuan_dana_bkm.pdf', compact('grouped', 'grandTotal', 'penyaluran', 'periodeTitle', 'items'));
    }

    public function create()
    {
        $rekenings = RekeningBkm::orderBy('nomer_rekening', 'asc')->get();
        return view('pengajuan_dana_bkm.create', compact('rekenings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'rekening_bkm_id' => 'required|exists:rekening_bkm,id',
            'uraian' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanDanaBkm::create($request->all());
        $this->catatLog('Tambah Pengajuan Dana BKM', 'Menambahkan Pengajuan Dana BKM: ' . $pengajuan->uraian . ' - Rp ' . number_format($pengajuan->total, 0, ',', '.'));

        return redirect()->route('pengajuan_dana_bkm.index')->with('success', 'Pengajuan Dana BKM berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengajuan = PengajuanDanaBkm::findOrFail($id);
        $rekenings = RekeningBkm::orderBy('nomer_rekening', 'asc')->get();
        return view('pengajuan_dana_bkm.edit', compact('pengajuan', 'rekenings'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'rekening_bkm_id' => 'required|exists:rekening_bkm,id',
            'uraian' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanDanaBkm::findOrFail($id);
        $pengajuan->update($request->all());
        $this->catatLog('Ubah Pengajuan Dana BKM', 'Mengubah Pengajuan Dana BKM: ' . $pengajuan->uraian);

        return redirect()->route('pengajuan_dana_bkm.index')->with('success', 'Pengajuan Dana BKM berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanDanaBkm::findOrFail($id);
        $info = $pengajuan->uraian;
        $pengajuan->delete();
        $this->catatLog('Hapus Pengajuan Dana BKM', 'Menghapus Pengajuan Dana BKM: ' . $info);

        return redirect()->route('pengajuan_dana_bkm.index')->with('success', 'Pengajuan Dana BKM berhasil dihapus!');
    }
}
