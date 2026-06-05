<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateTenantTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate-type 
                            {type : Tipe toko tenant (misal: retail, resto)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan migrasi hanya untuk tenant dengan tipe tertentu (retail atau resto)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');
        
        $this->info("Mencari tenant dengan tipe: {$type}...");

        $tenants = Tenant::where('store_type', $type)->get();

        if ($tenants->isEmpty()) {
            $this->warn("Tidak ada tenant yang ditemukan dengan tipe '{$type}'.");
            return;
        }

        $tenantIds = $tenants->pluck('id')->toArray();
        
        $this->info("Ditemukan " . count($tenantIds) . " tenant. Memulai migrasi khusus...");

        $options = [
            '--tenants' => $tenantIds,
        ];

        // 1. Jalankan migrasi core
        $this->info(">>> Menjalankan migrasi CORE untuk tipe '{$type}'...");
        $coreOptions = $options;
        $coreOptions['--path'] = 'database/migrations/tenant/core';
        Artisan::call('tenants:migrate', $coreOptions, $this->output);

        // 2. Jalankan migrasi spesifik (retail/resto)
        $this->info(">>> Menjalankan migrasi SPESIFIK ({$type})...");
        $specificOptions = $options;
        $specificOptions['--path'] = "database/migrations/tenant/{$type}";
        Artisan::call('tenants:migrate', $specificOptions, $this->output);

        $this->info("✅ Migrasi untuk tenant tipe '{$type}' selesai!");
    }
}
