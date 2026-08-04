<?php

use App\Shared\Traits\ShowsToast;
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

    public string $type = 'out'; // 'in' atau 'out'

    public string $walletType = Wallet::TYPE_CASH;

    public string $amount = '';

    public string $description = '';

    public string $filterWallet = 'all';

    protected ?TenantWalletService $walletService = null;

    protected function walletService(): TenantWalletService
    {
        return $this->walletService ??= app(TenantWalletService::class);
    }

    public function mount(): void
    {
        // ...
    }

    public function saveEntry(): void
    {
        $this->validate([
            'type' => ['required', 'in:in,out'],
            'walletType' => ['required', 'in:' . Wallet::TYPE_CASH . ',' . Wallet::TYPE_BANK],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['required', 'string', 'max:255'],
        ]);

        try {
            DB::beginTransaction();

            $amountFloat = (float)$this->amount;

            if ($this->type === 'in') {
                $this->walletService()->addBalance(
                    amount: $amountFloat,
                    reference: null,
                    description: $this->description,
                    walletType: $this->walletType
                );
            } else {
                $this->walletService()->deductBalance(
                    amount: $amountFloat,
                    reference: null,
                    description: $this->description,
                    walletType: $this->walletType
                );
            }

            DB::commit();

            $this->amount = '';
            $this->description = '';
            $this->toast('Pencatatan kas berhasil disimpan.');
            $this->resetPage(); // Reset paginasi ke halaman pertama

            // Trigger alpine modal close
            $this->js("window.dispatchEvent(new CustomEvent('close-cashbook-modal'))");

        } catch (Throwable $e) {
            DB::rollBack();
            $this->toast($e->getMessage(), 'danger');
        }
    }

    #[Computed]
    public function wallets(): Collection
    {
        return Wallet::whereIn('type', [Wallet::TYPE_CASH, Wallet::TYPE_BANK])->get();
    }

    #[Computed]
    public function histories()
    {
        $query = WalletTransaction::with('wallet')
            ->whereHas('wallet', function ($q) {
                if ($this->filterWallet !== 'all') {
                    $q->where('type', $this->filterWallet);
                } else {
                    $q->whereIn('type', [Wallet::TYPE_CASH, Wallet::TYPE_BANK]);
                }
            })
            ->latest();

        return $query->paginate(15);
    }

    public function render()
    {
        return view('pages.tenant.finance.⚡buku-kas.buku-kas');
    }
};
