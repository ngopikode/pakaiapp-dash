<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

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
    public function handle()
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
            $this->error("Tenant '{$tenantId}' already exists.");
            return 1;
        }

        $this->info("Creating {$type} tenant: {$name} ({$tenantId})");

        $tenant = Tenant::create([
            'id' => $tenantId,
            'store_type' => $type,
        ]);

        $tenant->domains()->create(['domain' => $domain]);

        $this->info("Tenant created. Running core migrations...");
        
        // Ensure standard migrations run first
        \Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--path' => 'database/migrations/tenant/core'
        ]);

        $this->info("Running {$type} specific migrations...");
        \Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--path' => "database/migrations/tenant/{$type}"
        ]);

        $tenant->run(function () use ($name, $type) {
            // Create default store setting
            \App\Models\StoreSetting::create([
                'name' => $name,
                'store_type' => $type,
                'is_active' => true,
            ]);
            $this->info("Default StoreSetting initialized.");
        });

        $this->info("Tenant {$name} setup complete! Domain: {$domain}");

        return 0;
    }
}
