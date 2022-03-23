<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recruitment extends Model
{
    protected $table = 'recruitment';
    protected $fillable = ['id_recruitment','nama_recruitment','jabatan','unit','department',
    'lokasi_penempatan','jumlah_kebutuhan','source_pemenuhan','tanggal_permintaan','target_tanggal_pemenuhan','status','status_schedule',
    'is_dell','created_by','updated_by','created_at','updated_at'];
}
