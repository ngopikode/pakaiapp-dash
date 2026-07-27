<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('is_application_fee_passed')->default(false)->after('service_charge_rate');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('application_fee', 10, 2)->default(0)->after('service_charge_percentage');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('application_fee');
        });

        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('is_application_fee_passed');
        });
    }
};
