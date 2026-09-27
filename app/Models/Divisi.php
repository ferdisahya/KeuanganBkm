<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $connection = 'mysql_logistik';
    protected $table = 'divisi';
    protected $guarded = [];
}
