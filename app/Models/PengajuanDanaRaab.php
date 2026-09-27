<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDanaRaab extends Model
{
    use HasFactory;

    protected $table = 'pengajuandana_raab';

    protected $fillable = [
        'tanggal',
        'rekening_raab_id',
        'uraian',
        'total',
    ];

    public function rekeningRaab()
    {
        return $this->belongsTo(RekeningRaab::class, 'rekening_raab_id');
    }
}
