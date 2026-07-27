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
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('current_billing_period')->nullable()->after('balance')->comment('Format: YYYY-MM');
            $table->integer('monthly_transaction_count')->default(0)->after('current_billing_period');
            $table->decimal('monthly_fee_paid', 15, 2)->default(0)->after('monthly_transaction_count');
            $table->integer('monthly_void_count')->default(0)->after('monthly_fee_paid');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_printed')->default(false)->after('status')->comment('To prevent arbitrary voids after receipt printing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropColumn([
                'current_billing_period',
                'monthly_transaction_count',
                'monthly_fee_paid',
                'monthly_void_count',
            ]);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_printed');
        });
    }
};
