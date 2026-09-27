<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RekeningRaab extends Model
{
    use HasFactory;

    protected $table = 'rekening_raab';

    protected $fillable = [
        'nomer_rekening',
        'nama_rekening',
    ];

    public function kasRaab()
    {
        return $this->hasMany(KasRaab::class, 'rekening_raab_id');
    }
}
