<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\KasRaabObserver;

#[ObservedBy([KasRaabObserver::class])]
class KasRaab extends Model
{
    use HasFactory;

    protected $table = 'kas_raab';

    protected $fillable = [
        'tanggal',
        'uraian',
        'jenis_transaksi',
        'nominal',
        'debet',
        'kredit',
        'saldo',
        'rekening_raab_id',
        'subrekening_raab_id',
        'lokasi_id',
        'toko_id',
        'nomer_lokasi',
        'no_supplier',
    ];

    public function rekeningRaab()
    {
        return $this->belongsTo(RekeningRaab::class, 'rekening_raab_id');
    }

    public function subrekeningRaab()
    {
        return $this->belongsTo(SubrekeningRaab::class, 'subrekening_raab_id');
    }

    public function lokasi()
    {
        return $this->belongsTo(Lokasi::class, 'nomer_lokasi', 'kode');
    }

    public function toko()
    {
        return $this->belongsTo(Toko::class, 'no_supplier', 'kode');
    }
}
