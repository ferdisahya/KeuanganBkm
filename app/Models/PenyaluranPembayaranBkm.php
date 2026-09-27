<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyaluranPembayaranBkm extends Model
{
    use HasFactory;

    protected $table = 'penyaluranpembayaran_bkm';

    protected $fillable = [
        'nama',
        'deskripsi',
        'nomer_rekening',
        'bank',
    ];
}
