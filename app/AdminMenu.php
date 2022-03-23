<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminMenu extends Model
{
    protected $table = 'admin_menu_access';
    protected $fillable = ['admin_code','list_menu','created_at','updated_at'];
}
