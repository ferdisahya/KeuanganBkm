<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SisaAnggaranBkm extends Model
{
    use HasFactory;

    protected $table = 'sisa_anggaran_bkm';

    protected $fillable = [
        'nama',
        'harga',
        'keterangan',
    ];
}
