<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokterFaskes extends Model
{
    protected $table = 'dokter_faskes';
    protected $fillable = ['admin_id','nama','jam_operasional', 'daftar_layanan','no_darurat','lokasi', 'created_at','updated_at'];

}
