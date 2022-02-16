<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpHistoryKedisiplinan extends Model
{    
    protected $table = 'emp_history_kedisiplinan';
    protected $fillable = ['emp_nik','nama', 'created_at','updated_at'];
}
