<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiChatSession extends Model
{
    protected $fillable = [
        'table_number',
        'session_token',
        'turn_count',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class, 'ai_chat_session_id');
    }
}
