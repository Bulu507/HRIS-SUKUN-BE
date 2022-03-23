<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpTraining extends Model
{
    protected $table = 'emp_training_history';
    protected $fillable = ['emp_nik','code_training','image_sertifikat','description','created_at','updated_at'];
}
