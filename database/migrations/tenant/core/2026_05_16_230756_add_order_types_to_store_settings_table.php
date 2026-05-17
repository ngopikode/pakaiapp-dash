<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->enum('store_type', ['resto', 'retail', 'service'])->default('resto')->after('name');
            $table->boolean('is_dinein_active')->default(true)->after('hero_promo_text');
            $table->boolean('is_takeaway_active')->default(true)->after('is_dinein_active');
            $table->boolean('is_delivery_active')->default(true)->after('is_takeaway_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['store_type', 'is_dinein_active', 'is_takeaway_active', 'is_delivery_active']);
        });
    }
};
