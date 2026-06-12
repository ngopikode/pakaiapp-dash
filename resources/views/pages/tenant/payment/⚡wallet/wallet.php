<?php

use App\Models\WalletTransaction;
use App\Services\TenantWalletService;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title("Dompet")]
class extends Component {
    use WithPagination;

    public string $filter = 'all';   // all | credit | debit
    public string $search = '';

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $wallet = app(TenantWalletService::class)->getWallet();

        $txQuery = WalletTransaction::query()
            ->when($this->filter === 'credit', fn($q) => $q->where('type', 'CREDIT'))
            ->when($this->filter === 'debit', fn($q) => $q->where('type', 'DEBIT'))
            ->when($this->search, fn($q) => $q->where('description', 'like', '%' . $this->search . '%'))
            ->latest();

        return [
            'wallet' => $wallet,
            'transactions' => $txQuery->paginate(15),
            'totalCredit' => WalletTransaction::where('type', 'CREDIT')->sum('amount'),
            'totalDebit' => WalletTransaction::where('type', 'DEBIT')->sum('amount'),
        ];
    }
};
