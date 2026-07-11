<?php

use App\Tenant\Models\Core\WalletTransaction;
use App\Tenant\Services\TenantWalletService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title("Dompet")]
class extends Component {
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public string $filter    = 'all';  // all | credit | debit
    public string $search    = '';
    public string $sortOrder = 'desc'; // desc | asc

    public function updatedFilter(): void    { $this->resetPage(); }
    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedSortOrder(): void { $this->resetPage(); }

    public function toggleSort(): void
    {
        $this->sortOrder = $this->sortOrder === 'desc' ? 'asc' : 'desc';
        $this->resetPage();
    }

    public function with(): array
    {
        $wallet = app(TenantWalletService::class)->getWallet();

        /*
         * Single query: fetch paginated rows AND both SUM aggregates together.
         * We use a correlated subquery approach — two DB::raw SUM subqueries
         * added as extra select columns on the same base table scan.
         * This avoids a second round-trip to the DB entirely.
         */
        $walletId = $wallet->id;

        $transactions = WalletTransaction::query()
            ->select([
                'id', 'type', 'amount', 'description',
                'reference_id', 'wallet_id', 'created_at',
                // Aggregate sums as extra columns — computed once per query execution
                DB::raw("(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_id = {$walletId} AND type = 'CREDIT') as total_credit"),
                DB::raw("(SELECT SUM(amount) FROM wallet_transactions WHERE wallet_id = {$walletId} AND type = 'DEBIT')  as total_debit"),
            ])
            ->where('wallet_id', $walletId)
            ->when($this->filter === 'credit', fn($q) => $q->where('type', 'CREDIT'))
            ->when($this->filter === 'debit',  fn($q) => $q->where('type', 'DEBIT'))
            ->when($this->search, fn($q) => $q->where('description', 'like', '%' . $this->search . '%'))
            ->orderBy('created_at', $this->sortOrder)
            ->paginate(15);

        // Pull aggregates from the first row (they're the same on every row)
        $first = $transactions->first();

        return [
            'wallet'       => $wallet,
            'transactions' => $transactions,
            'totalCredit'  => (float) ($first?->total_credit ?? 0),
            'totalDebit'   => (float) ($first?->total_debit  ?? 0),
        ];
    }
};
