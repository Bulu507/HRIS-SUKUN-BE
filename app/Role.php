<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{

    protected $table = 'role';
    protected $fillable = [
        'id', 'name', 'created_at', 'updated_at',
    ];
}
