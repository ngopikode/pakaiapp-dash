<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            // use_same_hours: true = satu jadwal berlaku semua hari
            //                 false = tiap hari punya jadwal sendiri
            $table->boolean('use_same_hours')->default(true)->after('is_active');

            // JSON structure:
            // {
            //   "default":   { "open": "08:00", "close": "22:00", "is_closed": false },
            //   "monday":    { "open": "08:00", "close": "22:00", "is_closed": false },
            //   "tuesday":   { ... },
            //   "wednesday": { ... },
            //   "thursday":  { ... },
            //   "friday":    { ... },
            //   "saturday":  { ... },
            //   "sunday":    { ... }
            // }
            $table->json('operating_hours')->nullable()->after('use_same_hours');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['use_same_hours', 'operating_hours']);
        });
    }
};
