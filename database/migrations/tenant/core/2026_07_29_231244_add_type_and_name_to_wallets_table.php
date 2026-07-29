<?php

use App\Tenant\Models\Core\Wallet;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->string('name')->default('Deposit Pakaiapp')->after('id');
            $table->enum('type', [Wallet::TYPE_BILLING, Wallet::TYPE_CASH, Wallet::TYPE_BANK, Wallet::TYPE_GATEWAY])
                ->default(Wallet::TYPE_BILLING)
                ->after('name');
        });

        DB::table('wallets')
            ->where('id', 1)
            ->update([
                'name' => 'Deposit Pakaiapp',
                'type' => Wallet::TYPE_BILLING,
            ]);

        Schema::table('wallets', function (Blueprint $table) {
            $table->unique('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wallets', function (Blueprint $table) {
            $table->dropUnique(['type']);
            $table->dropColumn(['name', 'type']);
        });
    }
};
