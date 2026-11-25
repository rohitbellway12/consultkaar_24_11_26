<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AITrendingQuestion extends Model
{
    use HasFactory;

    protected $table = 'ai_trending_questions';

    protected $fillable = [
        'category_id',
        'question',
        'status',
        'priority',
    ];

    protected $casts = [
        'category_id' => 'integer',
        'status' => 'integer',
        'priority' => 'integer',
    ];

    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(Speciality::class, 'category_id');
    }
}
