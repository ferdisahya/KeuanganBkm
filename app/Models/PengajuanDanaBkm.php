<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanDanaBkm extends Model
{
    use HasFactory;

    protected $table = 'pengajuandana_bkm';

    protected $fillable = [
        'tanggal',
        'rekening_bkm_id',
        'uraian',
        'total',
    ];

    public function rekeningBkm()
    {
        return $this->belongsTo(RekeningBkm::class, 'rekening_bkm_id');
    }
}
