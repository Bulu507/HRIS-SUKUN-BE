<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmpDaftarAnak extends Model
{
    protected $table = 'emp_daftar_anak';
    protected $fillable = ['emp_nik','nama','anak_ke','tempat_lahir','tanggal_lahir','status_nikah','status_status', 'created_at','updated_at'];

}
