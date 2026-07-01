<?php

namespace App\Livewire\Central;

use App\Tenant\Models\Core\StoreSetting;
use App\Central\Models\Tenant;
use App\Central\Models\User;
use App\Tenant\Services\TenantWalletService;
use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Central\Models\TenantRegistration;
use App\Shared\Mail\SystemEmail;

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
    public string $subscriptionPlan = 'free';

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
            'subscriptionPlan' => 'required|in:free,santai,premium',
            'tenantId' => 'required|string|unique:tenants,id|alpha_dash',
        ]);

        $domainUrl = $this->tenantId . '.' . config('tenancy.central_domains')[2];

        $exitCode = Artisan::call('tenant:create', [
            'name' => $this->storeName,
            '--id' => $this->tenantId,
            '--type' => $this->storeType,
            '--domain' => $domainUrl,
            '--plan' => $this->subscriptionPlan,
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

    public function retryCreateTenant($id)
    {
        $registration = TenantRegistration::findOrFail($id);

        if ($registration->status !== 'paid' && !($registration->status === 'pending' && $registration->payment_method === 'manual')) {
            $this->dispatch('swal:error', message: 'Hanya pendaftaran PAID (Gagal Setup) atau MANUAL yang bisa diproses.');
            return;
        }

        try {
            $slug = $registration->tenant_id;
            $domainUrl = $slug . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');

            // 1. Create Tenant Database & Subdomain
            Artisan::call('tenant:create', [
                'name' => $registration->store_name,
                '--id' => $slug,
                '--type' => $registration->store_type,
                '--domain' => $domainUrl,
                '--plan' => $registration->plan,
            ]);

            // 2. Initialize manager user
            $plainPassword = $registration->password; // Retrieve plain password stored temporarily
            $tenant = Tenant::find($slug);
            if (!$tenant) {
                throw new Exception("Tenant {$slug} tidak ditemukan setelah pembuatan database.");
            }

            $tenant->run(function () use ($registration, $plainPassword) {
                User::firstOrCreate(
                    ['email' => $registration->email],
                    [
                        'name' => $registration->owner_name,
                        'password' => $plainPassword, // Laravel handles the hashing automatically
                        'role' => 'manager'
                    ]
                );
                // Update StoreSetting brand text
                StoreSetting::first()?->update([
                    'navbar_brand_text' => $registration->store_name,
                    'hero_headline' => 'Selamat datang di ' . $registration->store_name,
                ]);
            });

            // 3. Securely hash the password inside the central DB
            $registration->update([
                'status' => 'created',
                'password' => Hash::make($plainPassword)
            ]);

            // 4. Send Welcome Email
            $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
            $emailBody = "Halo $registration->owner_name,\n\nTerima kasih atas pembayaran Anda! Sistem kasir toko Anda ($registration->store_name) telah selesai disiapkan dengan Paket " . ucfirst($registration->plan) . ".\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://$domainUrl/auth/login\nEmail: $registration->email\nPassword: $plainPassword\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";

            Mail::to($registration->email)->send(
                new SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://$domainUrl/auth/login")
            );

            $this->dispatch('swal:success', title: 'Aktivasi Berhasil!', message: "Toko {$registration->store_name} berhasil dibuat dan diaktifkan secara manual di {$domainUrl}. Email rincian akses telah dikirimkan ke pemilik.");
        } catch (Exception $e) {
            Log::error("Manual activation retry failed: " . $e->getMessage());
            $this->dispatch('swal:error', message: 'Gagal mengaktivasi toko: ' . $e->getMessage());
        }
    }

    public function with()
    {
        return [
            'tenants' => $this->isAuthenticated ? Tenant::orderBy('id')->get() : [],
            'pendingRegistrations' => $this->isAuthenticated
                ? TenantRegistration::where(function($q) {
                    $q->where('status', 'paid')
                      ->orWhere(function($q2) {
                          $q2->where('status', 'pending')->where('payment_method', 'manual');
                      });
                  })->orderBy('created_at', 'desc')->get()
                : []
        ];
    }
};
