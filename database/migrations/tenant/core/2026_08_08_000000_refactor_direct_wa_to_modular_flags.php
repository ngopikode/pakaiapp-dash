<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            if (Schema::hasColumn('store_settings', 'checkout_mode')) {
                $table->dropColumn('checkout_mode');
            }
            if (!Schema::hasColumn('store_settings', 'is_wa_checkout_active')) {
                $table->boolean('is_wa_checkout_active')->default(false)->after('is_shift_active');
            }
            if (!Schema::hasColumn('store_settings', 'is_preorder_active')) {
                $table->boolean('is_preorder_active')->default(false)->after('is_wa_checkout_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            if (Schema::hasColumn('store_settings', 'is_wa_checkout_active')) {
                $table->dropColumn('is_wa_checkout_active');
            }
            if (Schema::hasColumn('store_settings', 'is_preorder_active')) {
                $table->dropColumn('is_preorder_active');
            }
            if (!Schema::hasColumn('store_settings', 'checkout_mode')) {
                $table->enum('checkout_mode', ['pos', 'direct_wa'])->default('pos')->after('is_shift_active');
            }
        });
    }
};
