<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubrekeningRaab extends Model
{
    use HasFactory;

    protected $table = 'subrekening_raab';

    protected $fillable = [
        'nomer_subrekening',
        'nama_subrekening',
    ];

    public function kasRaab()
    {
        return $this->hasMany(KasRaab::class, 'subrekening_raab_id');
    }
}
