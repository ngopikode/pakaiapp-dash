<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Prunable;

class AiChatSession extends Model
{
    protected $fillable = [
        'table_number',
        'session_token',
        'turn_count',
    ];

    use Prunable;

    /**
     * Tentukan kriteria model yang akan dihapus secara otomatis.
     */
    public function prunable()
    {
        // Hapus history chat yang usianya sudah lebih dari 24 jam dari update terakhir
        return static::where('updated_at', '<', now()->subHours(24));
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class, 'ai_chat_session_id');
    }
}
