<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserTokenFCM extends Model
{
    protected $table = 'user_token';
    protected $fillable = ['id','user_id', 'token'];
}
