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
        Schema::table('tenant_registrations', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('amount');
            $table->string('duitku_payment_url')->nullable()->after('snap_token');
            $table->string('duitku_reference')->nullable()->after('duitku_payment_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenant_registrations', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'duitku_payment_url', 'duitku_reference']);
        });
    }
};
