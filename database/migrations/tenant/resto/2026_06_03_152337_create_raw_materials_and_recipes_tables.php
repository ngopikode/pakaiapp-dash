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
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('pcs'); // e.g., gram, ml, pcs
            $table->decimal('stock', 10, 2)->default(0);
            $table->decimal('cost_per_unit', 15, 2)->default(0); // For COGS calculation
            $table->decimal('min_stock_alert', 10, 2)->default(0); // Notification threshold
            $table->timestamps();
        });

        Schema::create('variant_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('raw_material_id')->constrained('raw_materials')->onDelete('cascade');
            $table->decimal('quantity_used', 10, 2)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_recipes');
        Schema::dropIfExists('raw_materials');
    }
};
