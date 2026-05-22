<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('order_type');
            
            // In MySQL/MariaDB, to change an ENUM, we need to redefine it or use string.
            // A safer approach that works across databases without Doctrine DBAL is to just change it to string,
            // but we can also use raw DB statement if it's MySQL. Let's change it to string for better flexibility.
            $table->string('status')->default('paid')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_online');
        });
    }
};
