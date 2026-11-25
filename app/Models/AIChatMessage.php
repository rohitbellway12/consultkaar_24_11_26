<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIChatMessage extends Model
{
    use HasFactory;

    protected $table = 'ai_chat_messages';

    protected $fillable = [
        'session_id',
        'user_id',
        'sender',
        'message',
        'tokens_used',
    ];

    protected $casts = [
        'session_id' => 'integer',
        'user_id' => 'integer',
        'tokens_used' => 'integer',
    ];

    public function session()
    {
        return $this->belongsTo(AIChatSession::class, 'session_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
