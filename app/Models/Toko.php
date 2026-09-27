<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $connection = 'mysql_logistik';
    protected $table = 'toko';

    protected $fillable = [
        'kode',
        'nama',
        'alamat',
        'pic',
        'telepon',
    ];
}
