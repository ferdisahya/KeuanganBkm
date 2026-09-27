<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('keuangan:register-admin {email} {name=Admin} {role=Admin Keuangan}', function ($email, $name, $role) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $this->error('Format email tidak valid.');
        return;
    }

    $validRoles = ['Admin Keuangan', 'Project Manager', 'Direktur Operasional (Dir Ops)', 'Direktur Utama', 'Admin'];
    if (!in_array($role, $validRoles)) {
        $this->error('Role tidak valid. Pilih salah satu: ' . implode(', ', $validRoles));
        return;
    }

    $user = \App\Models\User::where('email', $email)->first();

    if ($user) {
        $user->update(['role' => $role]);
        $this->info("Email {$email} sudah terdaftar. Peran (role) telah diperbarui menjadi {$role}.");
        return;
    }

    \App\Models\User::create([
        'name' => $name,
        'email' => $email,
        'role' => $role,
        'password' => null, // null password for Google SSO users
    ]);

    $this->info("Berhasil mendaftarkan Pengguna Baru: {$name} ({$email}) sebagai {$role}.");
})->purpose('Mendaftarkan email Admin Keuangan yang diperbolehkan login via Google SSO');
