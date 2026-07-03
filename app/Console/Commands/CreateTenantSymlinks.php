<?php

namespace App\Console\Commands;

use App\Central\Models\Tenant;
use App\Shared\Jobs\CreateFrameworkDirectoriesForTenant;
use Illuminate\Console\Command;

class CreateTenantSymlinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:symlink {tenant?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create public symlinks and framework directories for all tenants or a specific tenant';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $tenantId = $this->argument('tenant');

        if ($tenantId) {
            $tenant = Tenant::find($tenantId);
            if (!$tenant) {
                $this->error("Tenant '{$tenantId}' not found.");
                return 1;
            }
            $tenants = collect([$tenant]);
        } else {
            $tenants = Tenant::all();
        }

        if ($tenants->isEmpty()) {
            $this->info("No tenants found.");
            return 0;
        }

        $this->info("Processing symlinks for " . $tenants->count() . " tenant(s)...");

        foreach ($tenants as $tenant) {
            $this->info("Creating directories and symlink for tenant: {$tenant->id}");
            try {
                $job = new CreateFrameworkDirectoriesForTenant($tenant);
                $job->handle();
                $this->info("Successfully processed tenant: {$tenant->id}");
            } catch (\Exception $e) {
                $this->error("Failed for tenant {$tenant->id}: " . $e->getMessage());
            }
        }

        $this->info("Symlink generation completed successfully!");
        return 0;
    }
}
