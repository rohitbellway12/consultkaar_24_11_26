<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Injury extends Model
{
    protected $fillable = ['name'];

    protected $casts = [
        'name' => 'string',
    ];
    
}
