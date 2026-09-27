<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyaluranPembayaranRaab extends Model
{
    use HasFactory;

    protected $table = 'penyaluranpembayaran_raab';

    protected $fillable = [
        'nama',
        'deskripsi',
        'nomer_rekening',
        'bank',
    ];
}
