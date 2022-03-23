<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingGaleri extends Model
{
    protected $table = 'training_galeri';
    protected $fillable = ['code_training','foto','created_at','updated_at'];
}
