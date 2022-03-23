<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecruitmentResult extends Model
{
    protected $table = 'recr_result';
    protected $fillable = ['id_result','code_recruitment','list_job_seeker','status',
    'is_dell','created_by','updated_by','created_at','updated_at'];
}
