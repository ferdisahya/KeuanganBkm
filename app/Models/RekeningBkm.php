<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekeningBkm extends Model
{
    use HasFactory;

    protected $table = 'rekening_bkm';

    protected $fillable = [
        'nomer_rekening',
        'nama_rekening',
    ];

    public function kasBkm()
    {
        return $this->hasMany(KasBkm::class, 'rekening_bkm_id');
    }
}
