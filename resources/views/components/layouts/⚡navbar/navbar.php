<?php

use Livewire\Component;
use App\Tenant\Models\Core\Order;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component {
    public string|array|null $header = 'Dashboard Overview';

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('dashboard');
    }

    #[On('echo:kitchen,.KitchenUpdated')]
    public function refreshNotification()
    {
        // Pancing render ulang
    }

    #[Computed]
    public function pendingOrdersCount(): int
    {
        try {
            return Order::where('status', 'pending')->count();
        } catch (\Exception $e) {
            return 0; // Fallback for when tenant DB is not fully resolved yet during global requests
        }
    }
};
