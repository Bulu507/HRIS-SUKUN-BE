<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    protected $table = 'attendance';
    protected $guarded = [];
    use SoftDeletes;
}
