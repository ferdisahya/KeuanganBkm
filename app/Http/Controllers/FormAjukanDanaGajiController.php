<?php

namespace App\Http\Controllers;

use App\Models\FormAjukanDanaGaji;
use App\Models\KasBkm;
use App\Models\LogAktivitas;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class FormAjukanDanaGajiController extends Controller
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
        $query = FormAjukanDanaGaji::with(['user', 'pmUser', 'doUser', 'dirUser', 'rejectedByUser', 'buktiUser'])
            ->orderBy('id', 'desc');

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('tanggal_mulai') && $request->filled('tanggal_selesai')) {
            $query->whereBetween('tanggal', [$request->tanggal_mulai, $request->tanggal_selesai]);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('keterangan', 'like', '%' . $search . '%')
                  ->orWhere('nominal', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        $allRecords = FormAjukanDanaGaji::all();
        $stats = [
            'total_pengajuan' => $allRecords->count(),
            'total_nominal' => $allRecords->where('status', 'Approved All')->sum('nominal'),
            'pending_count' => $allRecords->where('status', 'Pending')->count(),
            'approved_all_count' => $allRecords->where('status', 'Approved All')->count(),
            'bukti_count' => $allRecords->whereNotNull('bukti')->count(),
        ];

        $pengajuans = $query->paginate(15)->withQueryString();

        return view('form_ajukan_dana_gaji.index', compact('pengajuans', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nominal' => 'required',
            'file' => 'required|file|mimes:pdf|max:25600',
            'tanggal' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ], [
            'file.required' => 'File pengajuan PDF wajib diunggah.',
            'file.mimes' => 'File pengajuan harus berformat PDF.',
            'file.max' => 'Ukuran file pengajuan maksimal 25 MB.',
        ]);

        $cleanNominal = preg_replace('/[^0-9]/', '', (string)$request->nominal);
        if (!$cleanNominal || (float)$cleanNominal <= 0) {
            return redirect()->back()->withInput()->with('error', 'Nominal pengajuan tidak valid!');
        }

        $tanggal = $request->filled('tanggal') ? $request->tanggal : Carbon::today()->toDateString();

        // Upload PDF pengajuan
        $file = $request->file('file');
        $filename = 'diluar_ops_pengajuan_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('upload/pengajuan');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }
        $file->move($destinationPath, $filename);
        $filePath = 'upload/pengajuan/' . $filename;

        $pengajuan = FormAjukanDanaGaji::create([
            'user_id' => auth()->id(),
            'tanggal' => $tanggal,
            'file' => $filePath,
            'nominal' => $cleanNominal,
            'keterangan' => $request->keterangan,
            'status' => 'Pending',
        ]);

        $this->catatLog(
            'Tambah Form Ajukan Dana Gaji',
            'Mengajukan Dana Gaji: Rp ' . number_format($cleanNominal, 0, ',', '.') . ' (File: ' . $filename . ')'
        );

        return redirect()->route('form_ajukan_dana_gaji.index')->with('success', 'Form Pengajuan Dana Gaji berhasil diajukan!');
    }

    public function approve(Request $request, $id)
    {
        $pengajuan = FormAjukanDanaGaji::findOrFail($id);
        $user = auth()->user();

        if (!$pengajuan->canUserApprove($user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang atau telah menyetujui pengajuan ini!');
        }

        $roleApproved = '';
        if ($user->isProjectManager() && !$pengajuan->isPmApproved()) {
            $pengajuan->pm_approved_at = now();
            $pengajuan->pm_approved_by = $user->id;
            $roleApproved = 'Project Manager';
        } elseif ($user->isDirekturOperasional() && $pengajuan->needsDoApproval() && !$pengajuan->isDoApproved()) {
            $pengajuan->do_approved_at = now();
            $pengajuan->do_approved_by = $user->id;
            $roleApproved = 'Direktur Operasional';
        } elseif ($user->isDirector() && $pengajuan->needsDirApproval() && !$pengajuan->isDirApproved()) {
            $pengajuan->dir_approved_at = now();
            $pengajuan->dir_approved_by = $user->id;
            $roleApproved = 'Direktur';
        } else {
            return redirect()->back()->with('error', 'Persetujuan untuk role Anda tidak memenuhi syarat nominal.');
        }

        $pengajuan->checkAndUpdateStatus();

        $statusMsg = $pengajuan->status === 'Approved All' ? 'Semua tingkatan telah menyetujui (Approved All)!' : 'Persetujuan berhasil dicatat.';
        $this->catatLog(
            'Approval Form Ajukan Dana Gaji',
            "Pengajuan Dana Gaji ID #{$pengajuan->id} disetujui oleh {$roleApproved} ({$user->name})"
        );

        return redirect()->route('form_ajukan_dana_gaji.index')->with('success', "Persetujuan {$roleApproved} berhasil disimpan. {$statusMsg}");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ], [
            'rejection_reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $pengajuan = FormAjukanDanaGaji::findOrFail($id);
        $user = auth()->user();

        if (!$pengajuan->canUserReject($user)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki wewenang untuk menolak pengajuan ini!');
        }

        $pengajuan->status = 'Ditolak';
        $pengajuan->rejected_at = now();
        $pengajuan->rejected_by = $user->id;
        $pengajuan->rejection_reason = $request->rejection_reason;
        $pengajuan->save();

        $this->catatLog(
            'Penolakan Form Ajukan Dana Gaji',
            "Pengajuan Dana Gaji ID #{$pengajuan->id} ditolak oleh {$user->role} ({$user->name}). Alasan: {$request->rejection_reason}"
        );

        return redirect()->route('form_ajukan_dana_gaji.index')->with('success', 'Pengajuan Dana Gaji telah ditolak.');
    }

    public function uploadBukti(Request $request, $id)
    {
        $pengajuan = FormAjukanDanaGaji::findOrFail($id);

        if ($pengajuan->status !== 'Approved All') {
            return redirect()->back()->with('error', 'Bukti transfer hanya dapat diunggah jika pengajuan sudah berstatus Approved All!');
        }

        $request->validate([
            'bukti'         => 'required|file|mimes:pdf,jpg,jpeg,png,webp|max:20480',
            'tanggal_bayar' => 'required|date',
        ], [
            'bukti.required'         => 'Berkas bukti transfer wajib dipilih.',
            'bukti.mimes'            => 'Format bukti transfer harus berupa PDF, JPG, PNG, atau WEBP.',
            'bukti.max'              => 'Ukuran file bukti maksimal 20 MB.',
            'tanggal_bayar.required' => 'Tanggal pembayaran wajib diisi.',
        ]);

        $file = $request->file('bukti');
        $filename = 'gaji_bukti_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $destinationPath = public_path('upload/bukti');
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true, true);
        }
        $file->move($destinationPath, $filename);
        $filePath = 'upload/bukti/' . $filename;

        if ($pengajuan->bukti && File::exists(public_path($pengajuan->bukti))) {
            File::delete(public_path($pengajuan->bukti));
        }

        $tanggalBayar = $request->tanggal_bayar;

        $pengajuan->bukti             = $filePath;
        $pengajuan->bukti_uploaded_at = now();
        $pengajuan->bukti_uploaded_by = auth()->id();
        $pengajuan->save();

        // Gaji selalu potong dari Kas BKM — pakai tanggal pembayaran
        KasBkm::create([
            'tanggal'    => $tanggalBayar,
            'uraian'     => 'Pembayaran Gaji ID #' . $pengajuan->id . ' - ' . $pengajuan->keterangan,
            'debet'      => 0,
            'kredit'     => $pengajuan->nominal,
            'saldo_kas'  => 0,
            'keterangan' => 'Otomatis dari Form Ajukan Dana Gaji ID #' . $pengajuan->id,
        ]);

        $this->catatLog(
            'Unggah Bukti Form Ajukan Dana Gaji',
            "Mengunggah bukti & memotong Kas BKM untuk Gaji ID #{$pengajuan->id} ({$filename})"
        );

        return redirect()->route('form_ajukan_dana_gaji.index')->with('success', 'Bukti berhasil diunggah dan Kas BKM sudah dipotong!');
    }

    public function destroy($id)
    {
        $pengajuan = FormAjukanDanaGaji::findOrFail($id);
        $user = auth()->user();

        if ($pengajuan->status === 'Approved All' && !$user->canManageUsersAndSecurity()) {
            return redirect()->back()->with('error', 'Pengajuan yang sudah Approved All tidak dapat dihapus!');
        }

        if ($pengajuan->file && File::exists(public_path($pengajuan->file))) {
            File::delete(public_path($pengajuan->file));
        }
        if ($pengajuan->bukti && File::exists(public_path($pengajuan->bukti))) {
            File::delete(public_path($pengajuan->bukti));
        }

        $idDeleted = $pengajuan->id;
        $pengajuan->delete();

        $this->catatLog('Hapus Form Ajukan Dana Gaji', "Menghapus Pengajuan Dana Gaji ID #{$idDeleted}");

        return redirect()->route('form_ajukan_dana_gaji.index')->with('success', 'Data pengajuan berhasil dihapus!');
    }
}
