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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_code', 50)->unique();
            $table->string('table_number', 20)->nullable(); // Nullable biar toko baju bisa ngosongin aja.
            $table->string('customer_name')->default('Pelanggan Umum');
            $table->string('customer_phone', 20)->nullable();
            $table->enum('order_type', ['retail', 'dinein', 'takeaway', 'online'])->default('retail');
            $table->enum('payment_method', ['cash', 'qris', 'transfer'])->default('cash');
            $table->decimal('subtotal', 10);
            $table->decimal('discount', 10)->default(0.00);
            $table->decimal('total_price', 10);
            $table->decimal('amount_paid', 10)->default(0.00);
            $table->decimal('change_amount', 10)->default(0.00);
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('paid');
            $table->foreignId('user_id')->nullable(); // Relasi ke kasir yang lagi login
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
