<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $table = 'admin';
    protected $fillable = ['code','nama','no_telp', 'alamat','pekerjaan', 'status_admin','created_at','updated_at'];
}
