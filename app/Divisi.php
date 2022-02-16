<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Divisi extends Model
{
    protected $table = 'ms_divisi';
    protected $fillable = ['divisi_code','nama','is_dell', 'created_at','updated_at'];
}
