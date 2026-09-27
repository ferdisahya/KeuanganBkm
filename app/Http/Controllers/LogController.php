<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Tampilkan halaman riwayat log aktivitas.
     */
    public function index(Request $request)
    {
        // Hanya Admin Keuangan dan Direktur Utama yang boleh mengakses
        if (!in_array(auth()->user()->role, ['Admin Keuangan', 'Direktur Utama'])) {
            abort(403, 'Akses ditolak. Anda tidak memiliki izin untuk melihat Log Aktivitas.');
        }

        $query = LogAktivitas::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('action', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('tanggal')) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $logs = $query->paginate(15)->withQueryString();
        return view('logs.index', compact('logs'));
    }
}
