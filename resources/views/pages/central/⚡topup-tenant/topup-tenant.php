<?php

namespace App\Livewire\Central;

use App\Central\Models\Tenant;
use App\Tenant\Services\TenantWalletService;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::central', ['title' => 'Top Up Kredit Tenant'])]
class extends Component {

    public string $tenantId = '';
    public $amount;
    public string $description = 'Top Up Saldo dari Admin Pusat';
    public string $pin = '';

    public function processTopUp(): void
    {
        $this->validate([
            'tenantId' => 'required|exists:tenants,id',
            'amount' => 'required|numeric|min:500',
            'description' => 'required|string|max:255',
            'pin' => 'required',
        ], [
            'tenantId.exists' => 'Tenant tidak ditemukan di sistem.',
            'amount.min' => 'Minimal top up adalah Rp 500.',
        ]);

        // Gatekeeper PIN
        if ($this->pin !== '260501') {
            $this->addError('pin', 'PIN Otoritas salah! Hubungi Admin.');
            return;
        }

        try {
            $tenant = Tenant::findOrFail($this->tenantId);

            // MASUK KE DATABASE TENANT
            $tenant->run(function () {
                $walletService = app(TenantWalletService::class);

                // Ambil instance dompet milik tenant ini
                $wallet = $walletService->getWallet();

                // Eksekusi penambahan saldo (CREDIT)
                // Kita gunakan $wallet itu sendiri sebagai model referensi transaksi
                $walletService->addBalance(
                    $this->amount,
                    $wallet,
                    $this->description
                );
            });


            // Beri notifikasi sukses ke frontend
            session()->flash('success', "Top up sebesar Rp " . number_format($this->amount, 0, ',', '.') . " berhasil dikirim ke tenant {$tenant->id}!");

            // Reset field setelah sukses
            $this->reset(['tenantId', 'amount', 'pin']);
        } catch (Exception $e) {
            $this->addError('tenantId', 'Gagal memproses top up: ' . $e->getMessage());
        }
    }

    public function with(): array
    {
        return [
            // Menampilkan daftar tenant untuk dropdown
            'tenants' => Tenant::orderBy('id')->get(),
        ];
    }
};
