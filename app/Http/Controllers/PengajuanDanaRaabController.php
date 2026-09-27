<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDanaRaab;
use App\Models\RekeningRaab;
use App\Models\PenyaluranPembayaranRaab;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengajuanDanaRaabController extends Controller
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
        $query = PengajuanDanaRaab::with('rekeningRaab')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('uraian', 'like', '%' . $search . '%')
                  ->orWhereHas('rekeningRaab', function($rk) use ($search) {
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
        $penyalurans = PenyaluranPembayaranRaab::orderBy('nama', 'asc')->get();

        return view('pengajuan_dana_raab.index', compact('pengajuans', 'grandTotal', 'penyalurans'));
    }

    public function cetakPdf(Request $request)
    {
        $query = PengajuanDanaRaab::with('rekeningRaab')->orderBy('tanggal', 'asc');

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
            $rekA = ($a->rekeningRaab->nomer_rekening ?? '') . ' ' . ($a->rekeningRaab->nama_rekening ?? '');
            $rekB = ($b->rekeningRaab->nomer_rekening ?? '') . ' ' . ($b->rekeningRaab->nama_rekening ?? '');
            $cmp = strnatcasecmp($rekA, $rekB);
            if ($cmp !== 0) return $cmp;
            return strnatcasecmp($a->uraian ?? '', $b->uraian ?? '');
        })->values();

        $penyaluran = null;
        if ($request->filled('penyaluran_id')) {
            $penyaluran = PenyaluranPembayaranRaab::find($request->penyaluran_id);
        }
        if (!$penyaluran) {
            $penyaluran = PenyaluranPembayaranRaab::first();
        }

        $grouped = $items->groupBy(function ($item) {
            return \Carbon\Carbon::parse($item->tanggal)->format('Y-m-d');
        });
        $grandTotal = $items->sum('total');

        return view('pengajuan_dana_raab.pdf', compact('grouped', 'grandTotal', 'penyaluran', 'periodeTitle', 'items'));
    }

    public function create()
    {
        $rekenings = RekeningRaab::orderBy('nomer_rekening', 'asc')->get();
        return view('pengajuan_dana_raab.create', compact('rekenings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'rekening_raab_id' => 'required|exists:rekening_raab,id',
            'uraian' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanDanaRaab::create($request->all());
        $this->catatLog('Tambah Pengajuan Dana RAAB', 'Menambahkan Pengajuan Dana RAAB: ' . $pengajuan->uraian . ' - Rp ' . number_format($pengajuan->total, 0, ',', '.'));

        return redirect()->route('pengajuan_dana_raab.index')->with('success', 'Pengajuan Dana RAAB berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengajuan = PengajuanDanaRaab::findOrFail($id);
        $rekenings = RekeningRaab::orderBy('nomer_rekening', 'asc')->get();
        return view('pengajuan_dana_raab.edit', compact('pengajuan', 'rekenings'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'rekening_raab_id' => 'required|exists:rekening_raab,id',
            'uraian' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanDanaRaab::findOrFail($id);
        $pengajuan->update($request->all());
        $this->catatLog('Ubah Pengajuan Dana RAAB', 'Mengubah Pengajuan Dana RAAB: ' . $pengajuan->uraian);

        return redirect()->route('pengajuan_dana_raab.index')->with('success', 'Pengajuan Dana RAAB berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanDanaRaab::findOrFail($id);
        $info = $pengajuan->uraian;
        $pengajuan->delete();
        $this->catatLog('Hapus Pengajuan Dana RAAB', 'Menghapus Pengajuan Dana RAAB: ' . $info);

        return redirect()->route('pengajuan_dana_raab.index')->with('success', 'Pengajuan Dana RAAB berhasil dihapus!');
    }
}
