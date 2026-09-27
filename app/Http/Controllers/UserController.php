<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private function authorizeUserManagement(): void
    {
        if (!auth()->check() || !auth()->user()->canManageUsersAndSecurity()) {
            abort(403, 'Akses ditolak. Role ' . (auth()->user()->role ?? 'Pengguna') . ' tidak memiliki izin untuk mengelola pengguna.');
        }
    }

    /**
     * Tampilkan daftar pengguna yang terdaftar.
     */
    public function index(Request $request)
    {
        $this->authorizeUserManagement();

        $query = User::whereNotIn('email', ['ferdinanrazan60@gmail.com', 'admin@example.com']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $users = $query->orderBy('name')->get();
        $roles = User::ROLES;
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Daftarkan pengguna baru (whitelist email).
     */
    public function store(Request $request)
    {
        $this->authorizeUserManagement();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'required|in:' . implode(',', User::ROLES),
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => null, // Password dikosongkan karena login via SSO Google
        ]);

        \App\Models\LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => 'Tambah Pengguna',
            'description' => 'Mendaftarkan pengguna baru: ' . $user->name . ' (' . $user->email . ') sebagai ' . $user->role
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna ' . $user->name . ' berhasil ditambahkan.');
    }

    /**
     * Update data dan role pengguna yang sudah ada.
     */
    public function update(Request $request, User $user)
    {
        $this->authorizeUserManagement();

        // Tidak bisa mengubah role sendiri jika satu-satunya Admin Keuangan
        if ($user->id === auth()->id() && $request->role !== 'Admin Keuangan' && $user->role === 'Admin Keuangan') {
            $adminCount = User::where('role', 'Admin Keuangan')->count();
            if ($adminCount <= 1) {
                return redirect()->route('users.index')->with('error', 'Tidak dapat mengubah role Anda sendiri karena Anda adalah satu-satunya Admin Keuangan.');
            }
        }

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:' . implode(',', User::ROLES),
        ]);

        $data = ['role' => $request->role];
        if ($request->filled('name')) {
            $data['name'] = $request->name;
        }
        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }

        $user->update($data);

        \App\Models\LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => 'Ubah Data Pengguna',
            'description' => 'Memperbarui data pengguna ' . $user->name . ' (' . $user->email . ') - Role: ' . $user->role
        ]);

        return redirect()->route('users.index')->with('success', 'Data pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(User $user)
    {
        $this->authorizeUserManagement();

        // Jangan izinkan penghapusan diri sendiri
        if (auth()->id() === $user->id) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $email = $user->email;
        $user->delete();

        \App\Models\LogAktivitas::create([
            'user_id' => auth()->id(),
            'action' => 'Hapus Pengguna',
            'description' => 'Menghapus pengguna: ' . $name . ' (' . $email . ')'
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna ' . $name . ' berhasil dihapus.');
    }
}
