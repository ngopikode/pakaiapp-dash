<?php

namespace App\Console\Commands;

use App\Central\Models\Tenant;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\TenantWalletService;
use DB;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Log;
use Throwable;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create {name} {--id=} {--type=resto} {--domain=} {--plan=free}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new tenant with specific type (resto or retail)';

    protected ?TenantWalletService $walletService = null;

    protected function walletService(): TenantWalletService
    {
        return $this->walletService ??= app(TenantWalletService::class);
    }

    /**
     * @throws Throwable
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $type = $this->option('type');
        $domain = $this->option('domain');
        $idOpt = $this->option('id');

        if (!in_array($type, ['resto', 'retail'])) {
            $this->error("Invalid tenant type. Must be 'resto' or 'retail'.");

            return self::FAILURE;
        }

        $tenantId = $idOpt ?: Str::slug($name);

        if (!$domain) $domain = $tenantId . '.' . parse_url(config('app.url'), PHP_URL_HOST);

        if (Tenant::find($tenantId)) {
            $this->error("Tenant '$tenantId' already exists.");

            return self::FAILURE;
        }

        $plan = strtolower($this->option('plan'));

        $this->info("Creating $type tenant: $name ($tenantId) with plan: $plan");

        $tenantData = [
            'id' => $tenantId,
            'store_type' => $type,
            'subscription_plan' => $plan,
        ];

        // Contoh implementasi dinamis override berdasarkan paket langganan saat register
        if ($plan === 'santai') {
            $tenantData['trx_fee'] = 250; // Harga khusus
            $tenantData['capping_limit'] = 100000;
        } elseif ($plan === 'premium') {
            $tenantData['trx_fee'] = 150; // Super murah
            $tenantData['capping_limit'] = 50000; // Cepat gratis
            $tenantData['product_slots'] = 100; // Kuota awal melimpah
        }

        // 1. Create Tenant Object
        $tenant = Tenant::create($tenantData);

        $tenant->domains()->create(['domain' => $domain]);

        $this->info('Tenant created. Running core migrations...');

        // 1. Paksa pakai absolute path dan --realpath
        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--path' => database_path('migrations/tenant/core'),
            '--realpath' => true,
        ]);

        // Cek outputnya kalau mau liat di log Laravel (storage/logs/laravel.log)
        Log::info('Migrate Core Output: ' . Artisan::output());

        $this->info("Running $type specific migrations...");

        // 2. Sama, pakai absolute path buat folder spesifik
        Artisan::call('tenants:migrate', [
            '--tenants' => [$tenantId],
            '--path' => database_path("migrations/tenant/$type"),
            '--realpath' => true,
        ]);

        Log::info('Migrate Type Output: ' . Artisan::output());

        // 3. Clear cache koneksi (TETAP WAJIB ADA)
        DB::purge('tenant');

        // 4. Masuk ke environment tenant untuk insert data
        $tenant->run(function () use ($name, $type, $plan) {
            StoreSetting::create([
                'name' => $name,
                'store_type' => $type,
                'is_active' => true,
            ]);

            // Initialize Wallet and Inject Initial Balance based on subscription plan
            try {
                $walletService = $this->walletService();
                $wallet = $walletService->getWallet();

                $initialBalance = 10000; // Default Free: Rp 10.000
                if ($plan === 'santai') {
                    $initialBalance = 50000;
                } elseif ($plan === 'premium') {
                    $initialBalance = 150000;
                }

                $walletService->addBalance(
                    amount: $initialBalance,
                    reference: $wallet,
                    description: 'Saldo awal pendaftaran Paket ' . ucfirst($plan)
                );
            } catch (Exception $e) {
                Log::error('Failed to initialize wallet balance for tenant: ' . $e->getMessage());
            }
        });

        $this->info('Default StoreSetting initialized.');
        
        $this->info("Creating framework directories and public symlink...");
        Artisan::call('tenants:symlink', ['tenant' => $tenantId]);
        $this->info(Artisan::output());

        $this->info("Tenant $name setup complete! Domain: $domain");

        return self::SUCCESS;
    }
}
