<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecruitmentJobSeekerMany extends Model
{
    protected $table = 'recr_job_seeker_many';
    protected $fillable = ['code_recruitment','code_job_seeker',
     'created_by','updated_by','created_at','updated_at'];
}
