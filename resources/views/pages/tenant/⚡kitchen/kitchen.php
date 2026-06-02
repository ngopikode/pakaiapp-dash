<?php

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

new class extends Component {
    public function markAsProcessing($orderId): void
    {
        $order = Order::find($orderId);
        if ($order && $order->kitchen_status === 'waiting') {
            $order->update(['kitchen_status' => 'processing']);
            $this->js("window.showIslandToast('Pesanan #{$order->invoice_code} mulai dimasak!', 'success');");
        }
    }

    public function markAsReady($orderId): void
    {
        $order = Order::find($orderId);
        if ($order && in_array($order->kitchen_status, ['waiting', 'processing'])) {
            $order->update(['kitchen_status' => 'ready']);
            $this->js("window.showIslandToast('Pesanan #{$order->invoice_code} siap disajikan!', 'success');");
        }
    }

    public function logout(): void
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirect('/', true);
    }

    public function with(): array
    {
        $orders = Order::with('items')
            ->whereIn('status', ['pending', 'paid', 'progress'])
            ->whereIn('kitchen_status', ['waiting', 'processing'])
            ->whereDate('created_at', today())
            ->orderBy('created_at', 'asc')
            ->get();

        return [
            'kitchenOrders' => $orders,
        ];
    }
};
