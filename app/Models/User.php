<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'role',
    ];

    public const ROLES = [
        'Direktur',
        'Direktur Operasional',
        'Project Manager',
        'Admin Keuangan',
    ];

    public function isDirector(): bool
    {
        return in_array($this->role, ['Direktur', 'Direktur Utama']);
    }

    public function isProjectManager(): bool
    {
        return strcasecmp($this->role, 'Project Manager') === 0;
    }

    public function isDirekturOperasional(): bool
    {
        return strcasecmp($this->role, 'Direktur Operasional') === 0;
    }

    public function isAdminKeuangan(): bool
    {
        return strcasecmp($this->role, 'Admin Keuangan') === 0;
    }

    /**
     * Wewenang Mengelola Pengguna & Membuka Blokir Device
     * Diizinkan: Direktur, Direktur Operasional, Project Manager
     * Dilarang : Admin Keuangan
     */
    public function canManageUsersAndSecurity(): bool
    {
        $allowed = ['Direktur', 'Direktur Operasional', 'Project Manager'];
        return in_array($this->role, $allowed) || in_array(ucwords(strtolower($this->role)), $allowed);
    }
}
