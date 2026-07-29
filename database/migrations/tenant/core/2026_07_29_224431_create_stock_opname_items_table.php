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
        Schema::create('stock_opname_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_opname_id')->constrained()->cascadeOnDelete();

            // Polymorphic relation to allow opname for raw_materials or product_variants
            $table->morphs('opnameable');

            $table->decimal('system_stock', 10, 2);
            $table->decimal('physical_stock', 10, 2);
            $table->decimal('difference', 10, 2);

            $table->string('note')->nullable(); // Alasan selisih, cth: "tumpah", "basi"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opname_items');
    }
};
