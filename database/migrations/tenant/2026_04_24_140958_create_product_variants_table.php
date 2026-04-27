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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku', 100)->nullable();
            $table->string('name'); // Contoh: M - Hitam

            // Finansial & Stok Varian
            $table->decimal('base_cost', 12, 2)->default(0); // Ganti dari base_hpp
            $table->decimal('base_price', 12, 2)->default(0);
            $table->decimal('price', 12)->default(0);
            $table->integer('stock')->default(0);
            $table->integer('min_stock')->default(0);

            $table->timestamps();

            // Indexing
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
