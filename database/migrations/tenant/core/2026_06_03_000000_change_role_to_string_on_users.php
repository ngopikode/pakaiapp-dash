<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Change the role column to string to support 'kitchen' and future roles
            $table->string('role')->default('cashier')->change();
        });
    }

    public function down(): void
    {
        // Reverting back to enum is risky, so we just leave it as string or do nothing.
    }
};
