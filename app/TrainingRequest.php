<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    protected $table = 'training_req';
    protected $fillable = ['id_training','nama_training','desc_training','target_peserta','jumlah_peserta',
    'unit','tanggal_pelaksanaan','tempat_pelaksanaan','budget','status_pelatih','nama_pelatih','status_approval',
    'is_dell','created_by','updated_by','created_at','updated_at'];
}
