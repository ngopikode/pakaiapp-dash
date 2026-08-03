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
        // 1. Rename tabel jika belum
        if (Schema::hasTable('variant_recipes') && !Schema::hasTable('recipes')) {
            Schema::rename('variant_recipes', 'recipes');
        } elseif (!Schema::hasTable('recipes')) {
            return; // tabel tidak ada, skip
        }

        // 2. Modify kolom
        Schema::table('recipes', function (Blueprint $table) {
            // Drop FK lama dengan nama asli (variant_recipes_variant_id_foreign) jika ada
            try {
                $table->dropForeign('variant_recipes_variant_id_foreign');
            } catch (Exception) {
                // FK tidak ada atau sudah dihapus
            }

            // Tambahkan struktur polymorphic jika belum ada
            if (!Schema::hasColumn('recipes', 'recipeable_type')) {
                $table->string('recipeable_type')->after('id')->nullable();
            }

            // Rename kolom variant_id ke recipeable_id jika masih bernama variant_id
            if (Schema::hasColumn('recipes', 'variant_id') && !Schema::hasColumn('recipes', 'recipeable_id')) {
                $table->renameColumn('variant_id', 'recipeable_id');
            }
        });

        // 3. Update existing data to point to ProductVariant
        DB::table('recipes')->whereNull('recipeable_type')->update(['recipeable_type' => 'App\\Tenant\\Models\\Core\\ProductVariant']);

        // 4. Buat nullable jadi NOT NULL dan tambahkan index
        Schema::table('recipes', function (Blueprint $table) {
            $table->string('recipeable_type')->nullable(false)->change();
            if (!$this->indexExists('recipes', 'recipes_recipeable_type_recipeable_id_index')) {
                $table->index(['recipeable_type', 'recipeable_id']);
            }
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        try {
            return collect(DB::select("SHOW INDEX FROM {$table}"))->contains('Key_name', $indexName);
        } catch (Exception) {
            return false;
        }
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
