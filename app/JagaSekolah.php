<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JagaSekolah extends Model
{
    protected $table = 'jaga_sekolah';
    protected $fillable = ['admin_id','judul','isi_berita', 'gambar1','gambar2','gambar3','tanggal','lampiran_file', 'created_at','updated_at'];
}
