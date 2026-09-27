<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KebutuhanLangsung extends Model
{
    protected $connection = 'mysql_logistik';
    protected $table = 'kebutuhan_langsung';
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paidByUser()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
