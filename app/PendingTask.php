<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PendingTask extends Model
{
    protected $table = 'pending_task';
    protected $fillable = ['menu_code','user_id','laporan_code', 'judul','status','admin_id','created_at','updated_at'];

}
