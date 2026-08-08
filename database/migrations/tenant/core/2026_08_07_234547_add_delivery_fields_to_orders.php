<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->date('delivery_date')->nullable()->after('is_online');
            $table->foreignId('delivery_slot_id')->nullable()->after('delivery_date')->constrained()->nullOnDelete();
            $table->foreignId('delivery_zone_id')->nullable()->after('delivery_slot_id')->constrained()->nullOnDelete();
            $table->decimal('shipping_cost', 10, 2)->default(0)->after('delivery_zone_id');
            $table->text('customer_address')->nullable()->after('customer_email');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_slot_id']);
            $table->dropForeign(['delivery_zone_id']);
            $table->dropColumn([
                'delivery_date',
                'delivery_slot_id',
                'delivery_zone_id',
                'shipping_cost',
                'customer_address',
            ]);
        });
    }
};
