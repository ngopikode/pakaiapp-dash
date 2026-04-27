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
        Schema::create('product_extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // Contoh: Tambah Keju, Extra Shot
            $table->decimal('cost', 12, 2)->default(0);  // HPP Topping
            $table->decimal('price', 12, 2)->default(0); // Harga Jual Topping
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexing untuk optimasi
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_extras');
    }
};
