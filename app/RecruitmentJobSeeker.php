<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RecruitmentJobSeeker extends Model
{
    protected $table = 'recr_job_seeker';
    protected $fillable = ['id_job_seeker','no_ktp','nama','telp',
    'alamat','berkas_1','berkas_2','berkas_3','email',
    'is_dell','created_by','updated_by','created_at','updated_at'];
}
