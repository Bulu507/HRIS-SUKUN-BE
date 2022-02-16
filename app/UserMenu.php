<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserMenu extends Model
{
    protected $table = 'user_menu_access';
    protected $fillable = [
        'id', 'user_code', 'list_menu', 'created_at', 'updated_at',
    ];
}
