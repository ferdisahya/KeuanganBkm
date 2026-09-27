<?php

namespace App\Http\Controllers;

use App\Models\SisaAnggaranBkm;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class SisaAnggaranBkmController extends Controller
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
        $query = SisaAnggaranBkm::orderBy('id', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('keterangan', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        } elseif ($request->filled('bulan') && $request->filled('tahun')) {
            $query->whereMonth('created_at', $request->bulan)->whereYear('created_at', $request->tahun);
        } elseif ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $totalSisa = (clone $query)->sum('harga') ?? 0;
        $items = $query->paginate(15)->withQueryString();

        return view('sisa_anggaran.index', compact('items', 'totalSisa'));
    }

    public function create()
    {
        return view('sisa_anggaran.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ], [
            'nama.required' => 'Nama anggaran wajib diisi.',
            'harga.required' => 'Harga / Nominal sisa anggaran wajib diisi.',
        ]);

        $item = SisaAnggaranBkm::create([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'keterangan' => $request->keterangan,
        ]);

        $this->catatLog('Tambah Sisa Anggaran BKM', 'Menambahkan data sisa anggaran BKM: ' . $item->nama . ' (Rp ' . number_format($item->harga, 0, ',', '.') . ')');

        return redirect()->route('sisa_anggaran.index')->with('success', 'Data Sisa Anggaran BKM berhasil disimpan!');
    }

    public function edit(SisaAnggaranBkm $sisaAnggaran)
    {
        return view('sisa_anggaran.edit', compact('sisaAnggaran'));
    }

    public function update(Request $request, SisaAnggaranBkm $sisaAnggaran)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $sisaAnggaran->update([
            'nama' => $request->nama,
            'harga' => $request->harga,
            'keterangan' => $request->keterangan,
        ]);

        $this->catatLog('Ubah Sisa Anggaran BKM', 'Memperbarui sisa anggaran BKM ID #' . $sisaAnggaran->id);

        return redirect()->route('sisa_anggaran.index')->with('success', 'Data Sisa Anggaran BKM berhasil diperbarui!');
    }

    public function destroy(SisaAnggaranBkm $sisaAnggaran)
    {
        $nama = $sisaAnggaran->nama;
        $sisaAnggaran->delete();

        $this->catatLog('Hapus Sisa Anggaran BKM', 'Menghapus data sisa anggaran BKM: ' . $nama);

        return redirect()->route('sisa_anggaran.index')->with('success', 'Data Sisa Anggaran BKM berhasil dihapus.');
    }
}
