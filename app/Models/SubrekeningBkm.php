<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubrekeningBkm extends Model
{
    use HasFactory;

    protected $table = 'subrekening_bkm';

    protected $fillable = [
        'nomer_subrekening',
        'nama_subrekening',
    ];

    public function kasBkm()
    {
        return $this->hasMany(KasBkm::class, 'subrekening_bkm_id');
    }
}
