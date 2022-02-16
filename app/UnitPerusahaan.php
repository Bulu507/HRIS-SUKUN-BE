<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UnitPerusahaan extends Model
{
    protected $table = 'ms_unit_perusahaan';
    protected $fillable = ['unit_perusahaan_code','nama','is_dell', 'created_at','updated_at'];

}
