<?php

namespace App\Livewire\Central;

use App\Models\StoreSetting;
use App\Models\Tenant;
use App\Models\User;
use App\Services\TenantWalletService;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::central', ['title' => 'Gatekeeper | Pakaiapp'])]
class extends Component {

    public bool $isAuthenticated = false;
    public string $authPin = '';
    public string $activeTab = 'create_tenant';

    // Form: Create Tenant
    public ?string $userName = '';
    public ?string $userEmail = '';
    public ?string $password = '';
    public ?string $storeName = '';
    public string $storeType = 'resto';
    public ?string $tenantId = '';

    // Form: Topup
    public string $selectedTenant = '';
    public $topupAmount;
    public string $topupDescription = 'Top Up Saldo via Central';

    public function mount()
    {
        $this->isAuthenticated = session()->get('superadmin_auth', false);
    }

    public function login()
    {
        if ($this->authPin === '260501') {
            session()->put('superadmin_auth', true);
            $this->isAuthenticated = true;
            $this->authPin = '';
        } else {
            $this->addError('authPin', 'Akses Ditolak. Otoritas tidak dikenali.');
        }
    }

    public function logout()
    {
        session()->forget('superadmin_auth');
        $this->isAuthenticated = false;
        $this->activeTab = 'create_tenant';
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function createTenant()
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email',
            'password' => 'required|min:6',
            'storeName' => 'required|string|max:255',
            'storeType' => 'required|in:resto,retail',
            'tenantId' => 'required|string|unique:tenants,id|alpha_dash',
        ]);

        $domainUrl = $this->tenantId . '.' . config('tenancy.central_domains')[2];

        $exitCode = Artisan::call('tenant:create', [
            'name' => $this->storeName,
            '--type' => $this->storeType,
            '--domain' => $domainUrl,
        ]);

        if ($exitCode !== 0) {
            $this->dispatch('swal:error', message: 'Gagal meracik database tenant.');
            return;
        }

        $tenant = Tenant::find($this->tenantId);
        $tenant?->run(function () {
            User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->password),
                'role' => 'manager',
            ]);
            StoreSetting::first()?->update([
                'navbar_brand_text' => $this->storeName,
                'hero_headline' => 'Selamat datang di ' . $this->storeName,
            ]);
        });

        $this->dispatch('swal:success', title: 'Tenant Diseduh!', message: "Toko {$this->storeName} berhasil live di {$domainUrl}");
        $this->reset(['userName', 'userEmail', 'password', 'storeName', 'tenantId']);
    }

    public function updatedStoreName($value)
    {
        $this->tenantId = Str::slug($value);
    }

    public function processTopUp()
    {
        $this->validate([
            'selectedTenant' => 'required|exists:tenants,id',
            'topupAmount' => 'required|numeric|min:500',
            'topupDescription' => 'required|string|max:255',
        ]);

        try {
            $tenant = Tenant::findOrFail($this->selectedTenant);
            $tenant->run(function () {
                $walletService = app(TenantWalletService::class);
                $walletService->addBalance($this->topupAmount, $walletService->getWallet(), $this->topupDescription);
            });

            $this->dispatch('swal:success', title: 'Top Up Sukses!', message: "Saldo Rp " . number_format($this->topupAmount, 0, ',', '.') . " masuk ke {$this->selectedTenant}.");
            $this->reset(['selectedTenant', 'topupAmount']);
        } catch (Exception $e) {
            $this->dispatch('swal:error', message: $e->getMessage());
        }
    }

    public function with()
    {
        return [
            'tenants' => $this->isAuthenticated ? Tenant::orderBy('id')->get() : []
        ];
    }
};
