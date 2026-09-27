<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDanaGaji extends Model
{
    use HasFactory;

    protected $table = 'pengajuandana_gaji';

    protected $fillable = [
        'tanggal',
        'uraian',
        'total',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total' => 'decimal:2',
    ];
}
