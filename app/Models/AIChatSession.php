<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIChatSession extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_sessions';

    protected $fillable = [
        'session_uuid',
        'user_id',
        'category_id',
        'started_at',
        'expires_at',
        'status',
        'summary',
        'user_concern',
        'recommended_advisor_ids',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'recommended_advisor_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function speciality()
    {
        return $this->belongsTo(Speciality::class, 'category_id');
    }

    public function category()
    {
        return $this->belongsTo(Speciality::class, 'category_id');
    }

    public function messages()
    {
        return $this->hasMany(AIChatMessage::class, 'session_id')->orderBy('created_at', 'asc');
    }

    public function isExpired()
    {
        if ($this->status === 'expired' || $this->status === 'completed') {
            return true;
        }

        if ($this->expires_at && now()->greaterThan($this->expires_at)) {
            $this->update(['status' => 'expired']);
            return true;
        }

        return false;
    }

    public function remainingSeconds()
    {
        if ($this->isExpired()) {
            return 0;
        }

        return max(0, now()->diffInSeconds($this->expires_at, false));
    }
}
