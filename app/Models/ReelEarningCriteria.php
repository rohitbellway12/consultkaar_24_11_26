<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReelEarningCriteria extends Model
{
    protected $table = 'reel_earning_criteria';

    protected $fillable = [
        'minimum_views',
        'minimum_values',
        'is_active',
    ];

    protected $casts = [
        'minimum_views' => 'integer',
        'minimum_values' => 'integer',
        'is_active' => 'boolean',
    ];
}
