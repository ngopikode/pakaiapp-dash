<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use Livewire\Component;

new class extends Component {
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

        if ($store) {
            $stats['orders_today'] = Order::whereDate('created_at', today())->count();

            $stats['revenue_today'] = Order::whereDate('created_at', today())
                ->where('status', 'paid')
                ->sum('total_price');

            $stats['pending_orders'] = Order::where('status', 'pending')->count();

            $stats['active_products'] = Product::where('is_active', true)->count();

            $recentOrders = Order::latest()->take(5)->get();
        }

        // Data ini otomatis dilempar ke index.blade.php
        return [
            'user' => $user,
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ];
    }
};
