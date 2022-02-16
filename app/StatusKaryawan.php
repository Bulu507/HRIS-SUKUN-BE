<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StatusKaryawan extends Model
{
    protected $table = 'ms_status_karyawan';
    protected $fillable = ['nama','is_dell', 'created_at','updated_at'];
}
