<?php

namespace App\Console\Commands;

use App\Central\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MigrateTenantTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenants:migrate-type {type : Tipe toko tenant (misal: retail, resto, all)} {--force : Force the operation to run when in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan migrasi hanya untuk tenant dengan tipe tertentu (retail, resto, atau all)';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $type = $this->argument('type');

        $force = $this->option('force');

        if ($type === 'all') {
            $this->info("🚀 Menjalankan migrasi berjenjang untuk SEMUA tipe tenant (retail & resto)...");
            $this->call('tenants:migrate-type', ['type' => 'retail', '--force' => $force]);
            $this->call('tenants:migrate-type', ['type' => 'resto', '--force' => $force]);
            $this->info("🌟 Migrasi ALL (Semua Tenant) Selesai!");
            return;
        }

        $this->info("Mencari tenant dengan tipe: $type...");

        $tenants = Tenant::where('store_type', $type)->get();

        if ($tenants->isEmpty()) {
            $this->warn("Tidak ada tenant yang ditemukan dengan tipe '$type'.");
            return;
        }

        $tenantIds = $tenants->pluck('id')->toArray();

        $this->info("Ditemukan " . count($tenantIds) . " tenant. Memulai migrasi khusus...");

        $options = [
            '--tenants' => $tenantIds,
            '--force' => $force,
        ];

        // 1. Jalankan migrasi core
        $this->info(">>> Menjalankan migrasi CORE untuk tipe '$type'...");
        $coreOptions = $options;
        $coreOptions['--path'] = 'database/migrations/tenant/core';
        Artisan::call('tenants:migrate', $coreOptions, $this->output);

        // 2. Jalankan migrasi spesifik (retail/resto)
        $this->info(">>> Menjalankan migrasi SPESIFIK ($type)...");
        $specificOptions = $options;
        $specificOptions['--path'] = "database/migrations/tenant/$type";
        Artisan::call('tenants:migrate', $specificOptions, $this->output);

        $this->info("✅ Migrasi untuk tenant tipe '$type' selesai!");
    }
}
