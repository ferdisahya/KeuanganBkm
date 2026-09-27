<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPageView extends Model
{
    protected $table = 'user_page_views';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'page_key',
        'last_viewed_at',
    ];

    protected $casts = [
        'last_viewed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
