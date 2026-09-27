<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lokasi extends Model
{
    use HasFactory;

    protected $connection = 'mysql_logistik';
    protected $table = 'lokasi';

    protected $fillable = [
        'nama',
        'kode',
        'alamat',
        'pengawas',
        'is_workshop',
        'is_project',
        'is_warehouse',
        'aktif',
    ];

    protected $casts = [
        'is_workshop' => 'boolean',
        'is_project' => 'boolean',
        'is_warehouse' => 'boolean',
        'aktif' => 'boolean',
    ];
}
