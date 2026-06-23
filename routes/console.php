<?php

use App\Models\AiChatSession;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('pakaiapp:run-ai-pricing')->everyMinute();

// Bersihkan data sampah (Garbage Collection): Hapus histori chat AI yang sudah kadaluarsa (di atas 24 jam)
Schedule::command('model:prune', [
    '--model' => [AiChatSession::class]
])->daily();
