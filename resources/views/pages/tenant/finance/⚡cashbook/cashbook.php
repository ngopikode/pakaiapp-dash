<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Data\CashbookTransactionData;
use App\Tenant\Models\Core\Wallet;
use App\Tenant\Models\Core\WalletTransaction;
use App\Tenant\Services\TenantWalletService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Buku Kas Operasional')] class extends Component
{
    use ShowsToast, WithPagination;

    public string $type = 'out'; // 'in' or 'out'

    public string $walletType = Wallet::TYPE_CASH;

    public string $amount = '';

    public string $description = '';

    public string $filterWallet = 'all';

    protected ?TenantWalletService $walletService = null;

    protected function walletService(): TenantWalletService
    {
        return $this->walletService ??= app(TenantWalletService::class);
    }

    public function saveEntry(): void
    {
        $this->validate([
            'type' => ['required', 'in:in,out'],
            'walletType' => ['required', 'in:' . Wallet::TYPE_CASH . ',bank'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $dto = new CashbookTransactionData(
                wallet_type: $this->walletType,
                transaction_type: $this->type === 'in' ? 'income' : 'expense',
                amount: (float)$this->amount,
                description: $this->description,
                transaction_date: now()
            );

            if ($dto->transaction_type === 'income') {
                $this->walletService()->addBalance(
                    amount: $dto->amount,
                    reference: null,
                    description: $dto->description,
                    walletType: $dto->wallet_type
                );
            } else {
                $this->walletService()->deductBalance(
                    amount: $dto->amount,
                    reference: null,
                    description: $dto->description,
                    walletType: $dto->wallet_type,
                    allowNegative: true // Kasus kasir ngeluarin uang walau tercatat kurang (opsional, disesuaikan)
                );
            }

            DB::commit();

            $this->amount = '';
            $this->description = '';
            $this->toast('Pencatatan kas berhasil disimpan.');
            $this->resetPage(); // Reset pagination to first page

            // Trigger alpine modal close
            $this->js("window.dispatchEvent(new CustomEvent('close-cashbook-modal'))");

        } catch (\Throwable $e) {
            DB::rollBack();
            $this->toast($e->getMessage(), 'danger');
        }
    }

    #[Computed]
    public function wallets(): Collection
    {
        return Wallet::whereIn('type', [Wallet::TYPE_CASH, 'bank'])->get();
    }

    #[Computed]
    public function histories()
    {
        $query = WalletTransaction::with('wallet')
            ->whereHas('wallet', function ($q) {
                if ($this->filterWallet !== 'all') {
                    $q->where('type', $this->filterWallet);
                } else {
                    $q->whereIn('type', [Wallet::TYPE_CASH, 'bank']);
                }
            })
            ->latest();

        return $query->paginate(15);
    }

    public function parseTransaction(WalletTransaction $tx): array
    {
        $desc = $tx->description ?? 'Transaksi';
        $title = $tx->type === 'DEBIT' ? 'Pengeluaran' : 'Pemasukan';
        $subtitle = $desc;

        // Consistent brand orange-red color from screenshot
        $iconBg = 'bg-[#f15a24]';

        $iconSvg = '<svg class="w-6 h-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>';
        
        if ($tx->type === 'DEBIT') {
            $iconSvg = '<svg class="w-6 h-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.5 19.5l15-15m0 0H8.25m11.25 0v11.25" /></svg>';
        } else {
            $iconSvg = '<svg class="w-6 h-6 text-white drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.5 4.5l-15 15m0 0h11.25m-11.25 0V8.25" /></svg>';
        }

        return [
            'title' => $title,
            'subtitle' => $subtitle,
            'iconSvg' => $iconSvg,
            'iconBg' => $iconBg,
        ];
    }
};
