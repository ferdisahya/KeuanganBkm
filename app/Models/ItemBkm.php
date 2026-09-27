<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemBkm extends Model
{
    use HasFactory;

    protected $table = 'item_bkm';

    protected $fillable = [
        'nomer_item',
        'nama_item',
    ];

    public function kasBkm()
    {
        return $this->hasMany(KasBkm::class, 'item_bkm_id');
    }
}
