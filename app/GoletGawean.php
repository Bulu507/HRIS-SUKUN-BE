<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GoletGawean extends Model
{
    protected $table = 'golet_gawean';
    protected $fillable = ['admin_id','judul','kategori', 'gambar1','gambar2','gambar3','tanggal',
    'nama_perusahaan', 'posisi_pekerjaan','deskripsi','syarat','kontak_person',
    'info_tambahan','status','lampiran_file','created_at','updated_at'];

}
