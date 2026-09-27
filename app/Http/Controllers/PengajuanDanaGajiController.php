<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDanaGaji;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PengajuanDanaGajiController extends Controller
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
        $query = PengajuanDanaGaji::orderBy('tanggal', 'desc')->orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('uraian', 'like', '%' . $search . '%')
                  ->orWhere('total', 'like', '%' . $search . '%');
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

        return view('pengajuan_dana_gaji.index', compact('pengajuans', 'grandTotal'));
    }

    public function cetakPdf(Request $request)
    {
        $query = PengajuanDanaGaji::orderBy('tanggal', 'asc')->orderBy('id', 'asc');

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
            return strnatcasecmp($a->uraian ?? '', $b->uraian ?? '');
        })->values();

        $grouped = $items->groupBy(function ($item) {
            return Carbon::parse($item->tanggal)->format('Y-m-d');
        });
        $grandTotal = $items->sum('total');

        return view('pengajuan_dana_gaji.pdf', compact('grouped', 'grandTotal', 'periodeTitle', 'items'));
    }

    public function create()
    {
        return view('pengajuan_dana_gaji.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanDanaGaji::create($request->all());
        $this->catatLog('Tambah Pengajuan Dana Gaji', 'Menambahkan Pengajuan Dana Gaji: ' . $pengajuan->uraian . ' - Rp ' . number_format($pengajuan->total, 0, ',', '.'));

        return redirect()->route('pengajuan_dana_gaji.index')->with('success', 'Pengajuan Dana Gaji berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pengajuan = PengajuanDanaGaji::findOrFail($id);
        return view('pengajuan_dana_gaji.edit', compact('pengajuan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'uraian' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $pengajuan = PengajuanDanaGaji::findOrFail($id);
        $pengajuan->update($request->all());
        $this->catatLog('Ubah Pengajuan Dana Gaji', 'Mengubah Pengajuan Dana Gaji: ' . $pengajuan->uraian);

        return redirect()->route('pengajuan_dana_gaji.index')->with('success', 'Pengajuan Dana Gaji berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $pengajuan = PengajuanDanaGaji::findOrFail($id);
        $info = $pengajuan->uraian;
        $pengajuan->delete();
        $this->catatLog('Hapus Pengajuan Dana Gaji', 'Menghapus Pengajuan Dana Gaji: ' . $info);

        return redirect()->route('pengajuan_dana_gaji.index')->with('success', 'Pengajuan Dana Gaji berhasil dihapus!');
    }
}
