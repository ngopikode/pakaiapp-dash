<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('checkout_mode');
            $table->boolean('is_wa_checkout_active')->default(false)->after('is_shift_active');
            $table->boolean('is_preorder_active')->default(false)->after('is_wa_checkout_active');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['is_wa_checkout_active', 'is_preorder_active']);
            $table->enum('checkout_mode', ['pos', 'direct_wa'])->default('pos')->after('is_shift_active');
        });
    }
};
