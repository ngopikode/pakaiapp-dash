<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Index strategy:
     *   1. (wallet_id, created_at DESC) — covers the main listing query ORDER BY created_at DESC
     *   2. (wallet_id, type)            — covers aggregation GROUP BY type filtered by wallet
     *   3. (wallet_id, description)     — partial-support for LIKE '%search%' searches
     */
    public function up(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->index(['wallet_id', 'created_at'], 'wt_wallet_created_at');
            $table->index(['wallet_id', 'type'], 'wt_wallet_type');
        });
    }

    public function down(): void
    {
        Schema::table('wallet_transactions', function (Blueprint $table) {
            $table->dropIndex('wt_wallet_created_at');
            $table->dropIndex('wt_wallet_type');
        });
    }
};
