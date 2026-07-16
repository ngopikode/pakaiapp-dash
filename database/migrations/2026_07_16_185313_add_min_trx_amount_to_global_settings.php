<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('global_settings')->insertOrIgnore([
            'key'         => 'default_min_trx_amount',
            'value'       => '1000',
            'type'        => 'integer',
            'description' => 'Nilai minimum total pesanan (Rp) agar dihitung sebagai transaksi tagihan sah',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('global_settings')->where('key', 'default_min_trx_amount')->delete();
    }
};
