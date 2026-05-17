<?php

namespace App\Console\Commands;

use App\Models\StoreSetting;
use App\Models\Tenant;
use DB;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Log;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {--type=resto} {--domain=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with specific type (resto or retail)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $type = $this->option('type');
        $domain = $this->option('domain');

        if (!in_array($type, ['resto', 'retail'])) {
            $this->error("Invalid tenant type. Must be 'resto' or 'retail'.");
            return 1;
        }

        $tenantId = Str::slug($name);

        if (!$domain) {
            $domain = $tenantId . '.' . parse_url(config('app.url'), PHP_URL_HOST);
        }

        if (Tenant::find($tenantId)) {
            $this->error("Tenant '$tenantId' already exists.");
            return 1;
        }

        $this->info("Creating $type tenant: $name ($tenantId)");

        // 1. Create Tenant Object (Ini biasanya otomatis trigger job CreateDatabase)
        $tenant = Tenant::create([
            'id' => $tenantId,
            'store_type' => $type,
        ]);

        $tenant->domains()->create(['domain' => $domain]);

        $this->info("Tenant created. Running core migrations...");

        // 1. Paksa pakai absolute path dan --realpath
        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--path' => database_path('migrations/tenant/core'),
            '--realpath' => true,
        ]);

        // Cek outputnya kalau mau liat di log Laravel (storage/logs/laravel.log)
        Log::info("Migrate Core Output: " . Artisan::output());

        $this->info("Running $type specific migrations...");

        // 2. Sama, pakai absolute path buat folder spesifik
        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--path' => database_path("migrations/tenant/$type"),
            '--realpath' => true,
        ]);

        Log::info("Migrate Type Output: " . Artisan::output());

        // 3. Clear cache koneksi (TETAP WAJIB ADA)
        DB::purge('tenant');

        // 4. Masuk ke environment tenant untuk insert data
        $tenant->run(function () use ($name, $type) {
            StoreSetting::create([
                'name' => $name,
                'store_type' => $type,
                'is_active' => true,
            ]);
        });

        $this->info("Default StoreSetting initialized.");
        $this->info("Tenant $name setup complete! Domain: $domain");

        return 0;
    }
}
