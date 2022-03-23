<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    protected $table = 'user_profile';
    protected $fillable = ['code','nama','no_telp', 'alamat','dusun','desa','kecamatan','pekerjaan', 'foto','created_at','updated_at'];
}
