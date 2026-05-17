<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use Livewire\Component;

new class extends Component {
    public int $lastCheckedOrderId = 0;

    public function mount(): void
    {
        // Track the latest order ID at page load
        $this->lastCheckedOrderId = Order::max('id') ?? 0;
    }

    public function acknowledgeOrders(): void
    {
        $this->lastCheckedOrderId = Order::max('id') ?? 0;
    }

    public function with(): array
    {
        $user = Auth::user();

        // Ambil pengaturan toko. 1 DB = 1 Toko.
        $store = StoreSetting::first();

        $stats = [
            'orders_today' => 0,
            'revenue_today' => 0,
            'pending_orders' => 0,
            'active_products' => 0,
        ];
        $recentOrders = [];
        $newOrderCount = 0;

        if ($store) {
            $stats['orders_today'] = Order::whereDate('created_at', today())->count();

            $stats['revenue_today'] = Order::whereDate('created_at', today())
                ->where('status', 'paid')
                ->sum('total_price');

            $stats['pending_orders'] = Order::where('status', 'pending')->count();

            $stats['active_products'] = Product::where('is_active', true)->count();

            $recentOrders = Order::latest()->take(5)->get();

            // Count orders that arrived after last check
            $newOrderCount = Order::where('id', '>', $this->lastCheckedOrderId)->count();
        }

        // Data ini otomatis dilempar ke index.blade.php
        return [
            'user' => $user,
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'newOrderCount' => $newOrderCount,
        ];
    }
};
