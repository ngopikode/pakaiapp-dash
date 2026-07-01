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
        Schema::create('tenant_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('owner_name');
            $table->string('email');
            $table->string('password');
            $table->string('store_name');
            $table->string('store_type')->default('resto');
            $table->string('tenant_id')->unique(); // Subdomain/slug
            $table->string('whatsapp');
            $table->string('plan')->default('free'); // free, santai, premium
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('pending'); // pending, paid, failed, created
            $table->string('snap_token')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenant_registrations');
    }
};
