<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'ms_department';
    protected $fillable = ['divisi_code','department_code','nama','is_dell', 'created_at','updated_at'];
}
