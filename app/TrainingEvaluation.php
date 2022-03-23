<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingEvaluation extends Model
{
    protected $table = 'training_evaluation';
    protected $fillable = ['code_training','status_evaluation','desc_evaluation','created_by','updated_by','created_at','updated_at'];
}
