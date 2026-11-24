<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateReview extends Model
{
    protected $fillable = ['item_id','doctor_id', 'rating', 'review','user_id','type'];

    protected $table = 'rating_reviews';

    protected $casts = [
        'doctor_id' => 'integer',
        'rating' => 'string',
        'review' => 'integer',
        'user_id' => 'integer',
        'rating' => 'string',
        'review' => 'string',
        'type' => 'string',
    ];
    
}
