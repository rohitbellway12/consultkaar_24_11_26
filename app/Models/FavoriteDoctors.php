<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavoriteDoctors extends Model
{
    protected $fillable = ['user_id','doctor_id'];

    protected $casts = [
        'user_id' => 'integer',
        'doctor_id' => 'integer'
    ];
    
}
