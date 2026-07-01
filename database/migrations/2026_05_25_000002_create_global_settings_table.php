<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('global_settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, float, boolean, json
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Insert initial default values for billing and quota
        DB::table('global_settings')->insert([
            [
                'key' => 'default_trx_fee',
                'value' => '300',
                'type' => 'integer',
                'description' => 'Biaya per transaksi sukses',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_capping_limit',
                'value' => '150000',
                'type' => 'integer',
                'description' => 'Batas maksimal tagihan bulanan (Gratis setelah limit tercapai)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_fup_limit',
                'value' => '5000',
                'type' => 'integer',
                'description' => 'Batas jumlah pesanan (FUP) sebelum dicharge kembali',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_void_penalty_fee',
                'value' => '300',
                'type' => 'integer',
                'description' => 'Denda biaya per void berlebih',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_void_allowance_percentage',
                'value' => '0.05',
                'type' => 'float',
                'description' => 'Persentase maksimal void bulanan dari total order (contoh: 0.05 = 5%)',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_min_free_voids',
                'value' => '10',
                'type' => 'integer',
                'description' => 'Batas jumlah aman void per bulan meskipun kurang dari persentase allowance',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'default_product_slots',
                'value' => '12',
                'type' => 'integer',
                'description' => 'Jumlah default slot produk (menu) untuk pengguna baru',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('global_settings');
    }
};
