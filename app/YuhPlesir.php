<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class YuhPlesir extends Model
{
    protected $table = 'yuh_plesir';
    protected $fillable = ['id','user_id','nama_wisata','deskripsi', 'gambar1','gambar2','gambar3','tanggal',
    'syarat_masuk', 'hari','jam','harga_tiket','status_open',
    'alamat','status_task','created_at','updated_at'];
}
