<?php

use App\Tenant\Models\Core\Wallet;
use App\Tenant\Models\Core\WalletTransaction;
use App\Tenant\Services\TenantWalletService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Dompet')] class extends Component
{
    use WithPagination;

    // --- 1. Public Properties ---
    public string $filter = 'all';  // all | credit | debit

    public string $search = '';

    public string $sortOrder = 'desc'; // desc | asc

    // Static wallet data cache to avoid DB hits on subsequent requests
    public array $walletIds = [];

    public array $mockWalletState = [];

    // --- 2. Protected Properties ---
    protected string $paginationTheme = 'tailwind';

    protected ?TenantWalletService $walletService = null;

    // --- 3. Lifecycle Hooks ---
    public function mount(): void
    {
        // Force pagination to start at page 1 on full refresh to prevent infinite scroll stuck
        $this->resetPage();

        // Hit DB only once on initial page load
        $billingWallet = $this->walletService()->getWallet(Wallet::TYPE_BILLING);
        $gatewayWallet = $this->walletService()->getWallet(Wallet::TYPE_GATEWAY);

        $this->walletIds = [$billingWallet->id, $gatewayWallet->id];

        $this->mockWalletState = [
            'id' => $billingWallet->id,
            'balance' => $billingWallet->balance + $gatewayWallet->balance,
        ];
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSortOrder(): void
    {
        $this->resetPage();
    }

    // --- 4. Action Methods ---
    public function toggleSort(): void
    {
        $this->sortOrder = $this->sortOrder === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->filter = 'all';
        $this->search = '';
        $this->resetPage();
    }

    // --- 5. Helper Methods ---
    public function parseTransaction(WalletTransaction $tx): array
    {
        $desc = $tx->description ?? 'Transaksi';
        $title = $tx->type === 'DEBIT' ? 'Pembayaran' : 'Terima Dana';
        $subtitle = $desc;

        if (str_contains($desc, 'Biaya layanan')) {
            $title = 'Biaya Layanan';
            if (preg_match('/INV-[A-Z0-9]+/', $desc, $matches)) {
                $subtitle = 'Pesanan ' . $matches[0];
            } else {
                $subtitle = 'Layanan Aplikasi';
            }
        } elseif (str_contains($desc, 'Penerimaan dana')) {
            $title = 'Penerimaan Dana';
            if (preg_match('/INV-[A-Z0-9]+/', $desc, $matches)) {
                $subtitle = 'Pesanan ' . $matches[0];
            } else {
                $subtitle = 'Hasil Penjualan';
            }
        } elseif (str_contains($desc, 'Pencairan dana')) {
            $title = 'Pencairan Dana';
            $subtitle = 'Transfer Keluar';
        } elseif (str_contains($desc, 'Top up')) {
            $title = 'Top Up Saldo';
            $subtitle = 'Isi Saldo';
        }

        // Consistent brand orange-red color from screenshot
        $iconBg = 'bg-[#f15a24]';

        $descLower = strtolower($desc);
        if (str_contains($descLower, 'listrik') || str_contains($descLower, 'pln')) {
            $iconSvg = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>';
        } elseif (str_contains($descLower, 'cashback') || str_contains($descLower, 'bonus')) {
            $iconSvg = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5a2 2 0 10-2 2h2zm-9 4h18M5 11h14v10H5V11z" /></svg>';
        } elseif (str_contains($descLower, 'pesanan') || str_contains($descLower, 'toko') || str_contains($descLower, 'belanja')) {
            $iconSvg = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>';
        } else {
            $iconSvg = '<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>';
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'iconSvg' => $iconSvg,
            'iconBg' => $iconBg,
        ];
    }

    // --- 6. Services Getters ---
    protected function walletService(): TenantWalletService
    {
        return $this->walletService ??= app(TenantWalletService::class);
    }

    // --- 7. Computed Properties (Lazy Load Data) ---
    #[Computed]
    public function transactions()
    {
        return WalletTransaction::with('wallet')
            ->whereIn('wallet_id', $this->walletIds)
            ->when(in_array($this->filter, ['credit', 'debit']), fn ($q) => $q->where('type', strtoupper($this->filter)))
            ->when($this->search, fn ($q) => $q->where('description', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', $this->sortOrder)
            ->paginate(15);
    }

    #[Computed]
    public function aggregates()
    {
        return WalletTransaction::query()
            ->selectRaw("SUM(CASE WHEN type = 'CREDIT' THEN amount ELSE 0 END) as total_credit")
            ->selectRaw("SUM(CASE WHEN type = 'DEBIT' THEN amount ELSE 0 END) as total_debit")
            ->whereIn('wallet_id', $this->walletIds)
            ->first();
    }

    // --- 8. Render ---
    public function with(): array
    {
        $mockWallet = new stdClass;
        $mockWallet->id = $this->mockWalletState['id'] ?? 1;
        $mockWallet->balance = $this->mockWalletState['balance'] ?? 0;

        return [
            'wallet' => $mockWallet,
            'transactions' => $this->transactions,
            'totalCredit' => (float)($this->aggregates->total_credit ?? 0),
            'totalDebit' => (float)($this->aggregates->total_debit ?? 0),
        ];
    }
};
