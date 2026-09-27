<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\KebutuhanLangsung;
use App\Models\KasBkm;
use App\Models\LogAktivitas;
use App\Helpers\LogistikStorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LogistikController extends Controller
{
    /**
     * Pembelian Barang
     */
    /**
     * Pembelian Barang
     */
    public function pembelianBarang(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');
        $tanggal = $request->get('tanggal');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $query = Pembelian::with(['lokasi', 'divisi', 'permintaan.divisi', 'permintaan.lokasi']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('no_pembelian', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%")
                  ->orWhere('dibuat_oleh', 'like', "%{$search}%")
                  ->orWhereHas('lokasi', function($lq) use ($search) {
                      $lq->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('permintaan.divisi', function($pdq) use ($search) {
                      $pdq->where('nama', 'like', "%{$search}%");
                  })
                  ->orWhereHas('permintaan.lokasi', function($plq) use ($search) {
                      $plq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($tanggal) {
            $query->whereDate('tanggal', $tanggal);
        } elseif ($bulan && $tahun) {
            $query->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
        } elseif ($tahun) {
            $query->whereYear('tanggal', $tahun);
        }

        // Adaptive totals based on active filters
        $totalNominal = (clone $query)->sum('total_harga') ?? 0;
        $totalItemCount = (clone $query)->count();
        $totalSelesai = (clone $query)->whereIn('status', ['selesai', 'paid', 'approved'])->count();

        $pembelianList = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('logistik.pembelian_barang', compact(
            'pembelianList', 'search', 'status', 'tanggal', 'bulan', 'tahun',
            'totalNominal', 'totalItemCount', 'totalSelesai'
        ));
    }

    /**
     * Upload Bukti Transfer Pembelian Barang (Khusus Admin Keuangan)
     */
    public function uploadBuktiTransferPembelian(Request $request, $id)
    {
        if (auth()->user()->role !== 'Admin Keuangan') {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Admin Keuangan.');
        }

        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'tujuan_kas' => 'required|in:BKM,RAAB',
            'nomer_rekening' => 'required|string',
            'nomer_subrekening' => 'required|string',
        ], [
            'bukti_transfer.required' => 'File bukti transfer wajib diunggah.',
            'bukti_transfer.mimes' => 'Format file bukti transfer harus berupa JPG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran file maksimal 10MB.',
            'tujuan_kas.required' => 'Tujuan kas wajib dipilih.',
            'nomer_rekening.required' => 'Nomor rekening wajib diisi.',
            'nomer_subrekening.required' => 'Nomor subrekening wajib diisi.',
        ]);

        $pembelian = Pembelian::findOrFail($id);

        // Jika sudah diisi sebelumnya, tidak bisa di-submit ulang
        if (!empty($pembelian->bukti_transfer)) {
            return redirect()->back()->with('error', 'Bukti transfer untuk transaksi ini sudah diisi.');
        }

        $file = $request->file('bukti_transfer');
        $path = LogistikStorageHelper::saveTransferFile($file, 'transfer');

        $now = now();
        $adminName = auth()->user()->name;

        // 1. Update tabel pembelian di Logistik
        $pembelian->update([
            'bukti_transfer' => $path,
            'paid_at' => $now,
            'paid_by' => $adminName,
            'status' => 'selesai',
        ]);

        // 2. Otomatis masukan data ke tabel kas_bkm atau kas_raab
        $kasData = [
            'tanggal'      => $now->toDateString(),
            'no_rek'       => $request->nomer_rekening,
            'no_sub_rek'   => $request->nomer_subrekening,
            'uraian'       => 'Pembelian Barang - ' . $pembelian->nama_barang,
            'debet'        => 0,
            'kredit'       => $pembelian->total_harga,
            'keterangan'   => 'Otomatis dari Integrasi Logistik Pembelian Barang #' . $pembelian->no_pembelian,
        ];

        if ($request->tujuan_kas === 'BKM') {
            $kasData['saldo_kas'] = 0;
            \App\Models\KasBkm::create($kasData);
        } else {
            $kasData['saldo'] = 0;
            \App\Models\KasRaab::create($kasData);
        }

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => 'Upload Bukti Transfer Pembelian',
            'description' => 'Mengunggah bukti transfer untuk Pembelian Barang #' . $pembelian->no_pembelian . ' dan menambahkan entri Kas ' . $request->tujuan_kas . '.',
        ]);

        return redirect()->back()->with('success', 'Bukti transfer berhasil diunggah dan otomatis dimasukkan ke Kas ' . $request->tujuan_kas . '.');
    }

    /**
     * Kebutuhan Langsung
     */
    public function kebutuhanLangsung(Request $request)
    {
        $search = $request->get('search');
        $jenis  = $request->get('jenis');
        $tanggal = $request->get('tanggal');
        $bulan = $request->get('bulan');
        $tahun = $request->get('tahun');

        $query = KebutuhanLangsung::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('keperluan', 'like', "%{$search}%")
                  ->orWhere('dibuat_oleh', 'like', "%{$search}%");
            });
        }

        if ($jenis) {
            $query->where('jenis', $jenis);
        }

        if ($tanggal) {
            $query->whereDate('tanggal_pengajuan', $tanggal);
        } elseif ($bulan && $tahun) {
            $query->whereMonth('tanggal_pengajuan', $bulan)->whereYear('tanggal_pengajuan', $tahun);
        } elseif ($tahun) {
            $query->whereYear('tanggal_pengajuan', $tahun);
        }

        // Adaptive totals based on active filters
        $totalNominal = (clone $query)->sum('nominal_aktual') ?? 0;
        $totalItemCount = (clone $query)->count();
        $totalSelesai = (clone $query)->whereNotNull('bukti_transfer')->count();

        $kebutuhanList = $query->orderBy('tanggal_pengajuan', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('logistik.kebutuhan_langsung', compact(
            'kebutuhanList', 'search', 'jenis', 'tanggal', 'bulan', 'tahun',
            'totalNominal', 'totalItemCount', 'totalSelesai'
        ));
    }

    /**
     * Upload Bukti Transfer Kebutuhan Langsung (Khusus Admin Keuangan)
     */
    public function uploadBuktiTransferKebutuhanLangsung(Request $request, $id)
    {
        if (auth()->user()->role !== 'Admin Keuangan') {
            abort(403, 'Aksi ini hanya dapat dilakukan oleh Admin Keuangan.');
        }

        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'tujuan_kas' => 'required|in:BKM,RAAB',
            'nomer_rekening' => 'required|string',
            'nomer_subrekening' => 'required|string',
        ], [
            'bukti_transfer.required' => 'File bukti transfer wajib diunggah.',
            'bukti_transfer.mimes' => 'Format file bukti transfer harus berupa JPG, PNG, atau PDF.',
            'bukti_transfer.max' => 'Ukuran file maksimal 10MB.',
            'tujuan_kas.required' => 'Tujuan kas wajib dipilih.',
            'nomer_rekening.required' => 'Nomor rekening wajib diisi.',
            'nomer_subrekening.required' => 'Nomor subrekening wajib diisi.',
        ]);

        $item = KebutuhanLangsung::findOrFail($id);

        // Jika sudah diisi sebelumnya, tidak bisa di-submit ulang
        if (!empty($item->bukti_transfer)) {
            return redirect()->back()->with('error', 'Bukti transfer untuk transaksi ini sudah diisi.');
        }

        $file = $request->file('bukti_transfer');
        $path = LogistikStorageHelper::saveTransferFile($file, 'transfer');

        $now = now();
        $adminName = auth()->user()->name;

        // 1. Update tabel kebutuhan_langsung di Logistik
        $item->update([
            'bukti_transfer' => $path,
            'paid_at' => $now,
            'paid_by' => $adminName,
            'status' => 'paid',
        ]);

        // 2. Otomatis masukan data ke tabel kas_bkm atau kas_raab di Keuangan
        $item->refresh();
        $nominalKredit = ($item->nominal_aktual && floatval($item->nominal_aktual) > 0)
            ? $item->nominal_aktual
            : ($item->nominal_pengajuan ?? $item->nominal ?? 0);
        $uraianGabungan = ucfirst($item->jenis) . ' - ' . $item->keperluan;

        $kasData = [
            'tanggal'      => $now->toDateString(),
            'no_rek'       => $request->nomer_rekening,
            'no_sub_rek'   => $request->nomer_subrekening,
            'uraian'       => $uraianGabungan,
            'debet'        => 0,
            'kredit'       => $nominalKredit,
            'keterangan'   => 'Otomatis dari Integrasi Logistik Kebutuhan Langsung ID #' . $item->id,
        ];

        if ($request->tujuan_kas === 'BKM') {
            $kasData['saldo_kas'] = 0;
            \App\Models\KasBkm::create($kasData);
        } else {
            $kasData['saldo'] = 0;
            \App\Models\KasRaab::create($kasData);
        }

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => 'Upload Bukti Transfer Kebutuhan Langsung',
            'description' => 'Mengunggah bukti transfer Kebutuhan Langsung ID #' . $item->id . ' dan menambahkan entri Kas ' . $request->tujuan_kas . '.',
        ]);

        return redirect()->back()->with('success', 'Bukti transfer berhasil diunggah dan otomatis dimasukkan ke Kas ' . $request->tujuan_kas . '.');
    }
}
