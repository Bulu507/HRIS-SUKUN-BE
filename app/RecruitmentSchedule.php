<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecruitmentSchedule extends Model
{
    protected $table = 'recr_schedule';
    protected $fillable = ['id_schedule','code_recruitment','list_job_seeker','nama_schedule','tanggal_schedule',
    'is_dell', 'created_by','updated_by','created_at','updated_at'];
}
