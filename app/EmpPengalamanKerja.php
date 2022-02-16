<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpPengalamanKerja extends Model
{
    
    protected $table = 'emp_pengalaman_kerja';
    protected $fillable = ['emp_nik','perusahaan','jabatan','alasan_kepindahan', 'created_at','updated_at'];
}
