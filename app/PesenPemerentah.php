<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PesenPemerentah extends Model
{
    protected $table = 'pesen_pemerentah';
    protected $fillable = ['admin_id','judul','isi_berita', 'gambar','tanggal','kutipan','created_at','updated_at'];

}
