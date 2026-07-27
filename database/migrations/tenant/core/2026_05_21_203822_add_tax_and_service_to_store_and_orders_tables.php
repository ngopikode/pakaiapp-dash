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
        if (!Schema::hasColumn('orders', 'tax_amount')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->decimal('tax_amount', 10, 2)->default(0.00)->after('subtotal');
                $table->decimal('service_charge_amount', 10, 2)->default(0.00)->after('tax_amount');
                $table->decimal('tax_percentage', 5, 2)->default(0.00)->after('service_charge_amount');
                $table->decimal('service_charge_percentage', 5, 2)->default(0.00)->after('tax_percentage');
            });
        }

        if (!Schema::hasColumn('store_settings', 'tax_rate')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->boolean('is_tax_active')->default(true)->after('is_delivery_active');
                $table->decimal('tax_rate', 5, 2)->default(10.00)->after('is_tax_active');
                $table->boolean('is_service_charge_active')->default(true)->after('tax_rate');
                $table->decimal('service_charge_rate', 5, 2)->default(5.00)->after('is_service_charge_active');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['tax_amount', 'service_charge_amount', 'tax_percentage', 'service_charge_percentage']);
            });
        }

        if (Schema::hasTable('store_settings')) {
            Schema::table('store_settings', function (Blueprint $table) {
                $table->dropColumn(['is_tax_active', 'tax_rate', 'is_service_charge_active', 'service_charge_rate']);
            });
        }
    }
};
