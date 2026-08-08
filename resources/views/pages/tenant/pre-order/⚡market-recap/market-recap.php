<?php

use App\Tenant\Services\PreOrderService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Rekap Belanja Pasar')]
class extends Component
{
    public string $selectedDate;

    protected ?PreOrderService $preOrderService = null;

    protected function preOrderService(): PreOrderService
    {
        return $this->preOrderService ??= app(PreOrderService::class);
    }

    public function mount(): void
    {
        // Default: tampilkan rekap untuk BESOK (barang yang perlu dibeli malam ini)
        $this->selectedDate = Carbon::tomorrow('Asia/Jakarta')->toDateString();
    }

    public function with(): array
    {
        $date = Carbon::parse($this->selectedDate, 'Asia/Jakarta');
        $recap = $this->preOrderService()->getMarketRecap($date);

        return ['recap' => $recap, 'date' => $date];
    }
};
