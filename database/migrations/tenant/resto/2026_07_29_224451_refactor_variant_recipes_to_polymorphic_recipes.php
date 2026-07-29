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
        // 1. Rename tabel
        Schema::rename('variant_recipes', 'recipes');

        // 2. Modify kolom
        Schema::table('recipes', function (Blueprint $table) {
            // Drop constraint lama
            $table->dropForeign(['variant_id']);

            // Tambahkan struktur polymorphic
            $table->string('recipeable_type')->after('id')->nullable();

            // Rename kolom variant_id ke recipeable_id untuk re-use datanya
            $table->renameColumn('variant_id', 'recipeable_id');
        });

        // 3. Update existing data to point to ProductVariant
        DB::table('recipes')->update(['recipeable_type' => 'App\\Tenant\\Models\\Core\\ProductVariant']);

        // 4. Buat nullable jadi NOT NULL dan tambahkan index
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('recipeable_type')->nullable(false)->change();
            $table->index(['recipeable_type', 'recipeable_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert prosesnya
        Schema::table('recipes', function (Blueprint $table) {
            $table->dropIndex(['recipeable_type', 'recipeable_id']);
            $table->renameColumn('recipeable_id', 'variant_id');
            $table->dropColumn('recipeable_type');

            // Tambahkan kembali foreign key
            $table->foreign('variant_id')->references('id')->on('product_variants')->onDelete('cascade');
        });

        Schema::rename('recipes', 'variant_recipes');
    }
};
