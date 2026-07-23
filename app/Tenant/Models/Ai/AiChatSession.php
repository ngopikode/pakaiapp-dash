<?php

namespace App\Tenant\Models\Ai;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Prunable;

#[Fillable(['table_number', 'session_token', 'turn_count'])]
class AiChatSession extends Model
{
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
