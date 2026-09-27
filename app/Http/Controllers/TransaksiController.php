<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    /**
     * Helper untuk mencatat log aktivitas
     */
    private function catatLog($action, $description)
    {
        LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'description' => $description,
        ]);
    }

    /**
     * Riwayat semua transaksi dengan filter search, jenis, tanggal, bulan, dan tahun.
     */
    public function index(Request $request)
    {
        $query = Transaksi::orderBy('tanggal', 'desc')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $query->where('keterangan', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('jenis') && in_array($request->jenis, ['pemasukan', 'pengeluaran'])) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal', $request->tanggal);
        }

        if ($request->filled('bulan') && $request->filled('tahun_filter')) {
            $query->whereMonth('tanggal', $request->bulan)
                  ->whereYear('tanggal', $request->tahun_filter);
        } elseif ($request->filled('tahun_filter')) {
            $query->whereYear('tanggal', $request->tahun_filter);
        }

        // Ambil daftar tahun untuk dropdown filter
        $tahunList = Transaksi::selectRaw('YEAR(tanggal) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();
        if (empty($tahunList)) {
            $tahunList = [now()->year];
        }

        $transaksis = $query->paginate(10)->withQueryString();

        return view('transaksi.index', compact('transaksis', 'tahunList'));
    }

    /**
     * Tampilkan form input transaksi baru.
     */
    public function create()
    {
        $today = now()->format('Y-m-d');
        return view('transaksi.create', compact('today'));
    }

    /**
     * Validasi dan simpan transaksi baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'jenis'      => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string|max:255',
            'nominal'    => 'required|numeric|min:1',
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'jenis.required'      => 'Jenis transaksi wajib dipilih.',
            'jenis.in'            => 'Jenis transaksi tidak valid.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'nominal.required'    => 'Nominal wajib diisi.',
            'nominal.numeric'     => 'Nominal harus berupa angka!',
            'nominal.min'         => 'Nominal harus lebih dari 0.',
        ]);

        $nominal = str_replace(',', '', $request->nominal);

        $transaksi = Transaksi::create([
            'tanggal'    => $request->tanggal,
            'jenis'      => $request->jenis,
            'keterangan' => $request->keterangan,
            'nominal'    => $nominal,
        ]);

        $this->catatLog('Tambah Transaksi', 'Menambahkan transaksi ' . $transaksi->jenis . ' sebesar Rp ' . number_format($transaksi->nominal, 0, ',', '.') . ' (' . $transaksi->keterangan . ')');

        return redirect()->route('transaksi.index')
            ->with('success', 'Data transaksi berhasil disimpan!');
    }

    /**
     * Tampilkan form edit transaksi.
     */
    public function edit(Transaksi $transaksi)
    {
        return view('transaksi.edit', compact('transaksi'));
    }

    /**
     * Validasi dan simpan perubahan transaksi ke database.
     */
    public function update(Request $request, Transaksi $transaksi)
    {
        $request->validate([
            'tanggal'    => 'required|date',
            'jenis'      => 'required|in:pemasukan,pengeluaran',
            'keterangan' => 'required|string|max:255',
            'nominal'    => 'required|numeric|min:1',
        ], [
            'tanggal.required'    => 'Tanggal wajib diisi.',
            'jenis.required'      => 'Jenis transaksi wajib dipilih.',
            'jenis.in'            => 'Jenis transaksi tidak valid.',
            'keterangan.required' => 'Keterangan wajib diisi.',
            'nominal.required'    => 'Nominal wajib diisi.',
            'nominal.numeric'     => 'Nominal harus berupa angka!',
            'nominal.min'         => 'Nominal harus lebih dari 0.',
        ]);

        $nominal = str_replace(',', '', $request->nominal);

        $transaksi->update([
            'tanggal'    => $request->tanggal,
            'jenis'      => $request->jenis,
            'keterangan' => $request->keterangan,
            'nominal'    => $nominal,
        ]);

        $this->catatLog('Ubah Transaksi', 'Mengubah transaksi menjadi ' . $transaksi->jenis . ' sebesar Rp ' . number_format($transaksi->nominal, 0, ',', '.') . ' (' . $transaksi->keterangan . ')');

        return redirect()->route('transaksi.index')
            ->with('success', 'Data transaksi berhasil diperbarui!');
    }

    /**
     * Hapus transaksi dari database.
     */
    public function destroy(Transaksi $transaksi)
    {
        $keterangan = $transaksi->keterangan;
        $nominal = $transaksi->nominal;
        $jenis = $transaksi->jenis;

        $transaksi->delete();

        $this->catatLog('Hapus Transaksi', 'Menghapus transaksi ' . $jenis . ' sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' (' . $keterangan . ')');

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
