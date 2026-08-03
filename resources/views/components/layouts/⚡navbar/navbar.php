<?php

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\Shift;
use App\Shared\Traits\ShowsToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use ShowsToast;

    public string|array|null $header = 'Dashboard Overview';

    public function logout(): void
    {
        $hasActiveShift = Shift::where('user_id', Auth::id())
            ->where('status', Shift::STATUS_ACTIVE)
            ->exists();

        if ($hasActiveShift) {
            $this->toast('Tutup shift kasir terlebih dahulu sebelum logout.', 'warning');
            return;
        }

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
        } catch (Exception $e) {
            return 0; // Fallback for when tenant DB is not fully resolved yet during global requests
        }
    }
};
