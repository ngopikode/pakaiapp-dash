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
        Schema::table('orders', function (Blueprint $table) {
            // Reference ID dari Duitku (untuk tracking & callback)
            $table->string('duitku_reference', 100)->nullable()->after('cancellation_note');

            // URL payment page yang diberikan Duitku ke customer
            $table->text('duitku_payment_url')->nullable()->after('duitku_reference');

            // Nomor Virtual Account (jika metode pembayaran VA)
            $table->string('duitku_va_number', 50)->nullable()->after('duitku_payment_url');

            // Kode metode pembayaran Duitku (misal: QRIS, BT, BV, dll)
            $table->string('duitku_payment_method', 20)->nullable()->after('duitku_va_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'duitku_reference',
                'duitku_payment_url',
                'duitku_va_number',
                'duitku_payment_method',
            ]);
        });
    }
};
