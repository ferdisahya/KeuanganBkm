<?php

namespace App\Http\Controllers;

use App\Models\DeviceLockout;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class DeviceLockoutController extends Controller
{
    /**
     * Pastikan hanya Direktur, Direktur Operasional, dan Project Manager yang dapat mengakses.
     */
    private function authorizeSecurityManagement(): void
    {
        if (!auth()->check() || !auth()->user()->canManageUsersAndSecurity()) {
            abort(403, 'Akses ditolak. Role ' . (auth()->user()->role ?? 'Pengguna') . ' tidak memiliki izin untuk membuka blokir device.');
        }
    }

    /**
     * Tampilkan daftar device IP yang diblokir / dilacak.
     */
    public function index(Request $request)
    {
        $this->authorizeSecurityManagement();

        $query = DeviceLockout::with('unblocker');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ip_address', 'like', '%' . $search . '%')
                  ->orWhere('block_reason', 'like', '%' . $search . '%');
        }

        $lockouts = $query->orderBy('updated_at', 'desc')->get();

        return view('device_lockouts.index', compact('lockouts'));
    }

    /**
     * Buka blokir IP device tertentu.
     */
    public function unblock($id)
    {
        $this->authorizeSecurityManagement();

        $lockout = DeviceLockout::findOrFail($id);
        $ip = $lockout->ip_address;

        $lockout->update([
            'failed_attempts' => 0,
            'blocked_until' => null,
            'unblocked_at' => now(),
            'unblocked_by' => auth()->id(),
        ]);

        LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => 'Buka Blokir Device',
            'description' => auth()->user()->role . ' ' . auth()->user()->name . ' membuka blokir device IP ' . $ip,
        ]);

        return redirect()->route('device_lockouts.index')
            ->with('success', "Blokir device IP {$ip} berhasil dibuka! Device tersebut kini dapat mencoba login kembali.");
    }
}
