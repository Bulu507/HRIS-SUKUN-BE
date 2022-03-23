<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JagaAwak extends Model
{
    protected $table = 'jaga_awak';
    protected $fillable = ['admin_id','judul','isi_artikel', 'gambar','tanggal','sumber', 'created_at','updated_at'];

}
