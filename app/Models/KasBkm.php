<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\KasBkmObserver;

#[ObservedBy([KasBkmObserver::class])]
class KasBkm extends Model
{
    use HasFactory;

    protected $table = 'kas_bkm';

    protected $fillable = [
        'tanggal',
        'uraian',
        'jenis_transaksi',
        'nominal',
        'debet',
        'kredit',
        'saldo_kas',
        'keterangan',
        'rekening_bkm_id',
        'subrekening_bkm_id',
        'item_bkm_id',
    ];

    public function rekeningBkm()
    {
        return $this->belongsTo(RekeningBkm::class, 'rekening_bkm_id');
    }

    public function subrekeningBkm()
    {
        return $this->belongsTo(SubrekeningBkm::class, 'subrekening_bkm_id');
    }

    public function itemBkm()
    {
        return $this->belongsTo(ItemBkm::class, 'item_bkm_id');
    }
}
