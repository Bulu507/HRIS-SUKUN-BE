<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpRiwayatPekerjaan extends Model
{
    protected $table = 'emp_riwayat_pekerjaan';
    protected $fillable = ['emp_nik','unit_perusahaan','tgl','jabatan','alasan_kepindahan', 'created_at','updated_at'];

}
