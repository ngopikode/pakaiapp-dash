<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Services\TenantWalletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

new class extends Component {
    public int $lastCheckedOrderId = 0;

    // Tarif per transaksi, disamakan dengan kasir
    private int $feePerTransaction = 300;

    public function mount(): void
    {
        $this->lastCheckedOrderId = Order::max('id') ?? 0;
    }

    public function acknowledgeOrders(): void
    {
        $this->lastCheckedOrderId = Order::max('id') ?? 0;
    }

    public function exportLaporan(): ?StreamedResponse
    {
        $store = StoreSetting::first();
        if (!$store) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Toko belum di-setup!']);
            return null;
        }

        $orders = Order::whereMonth('created_at', date('m'))
            ->whereIn('status', ['paid', 'completed'])
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = "Laporan_Penjualan_" . Str::slug($store->name) . "_" . date('Y_m') . ".csv";

        $csvData = "Tanggal,No. Invoice,Nama Pelanggan,Tipe Pesanan,Total Belanja (Rp)\n";
        foreach ($orders as $order) {
            $date = $order->created_at->format('Y-m-d H:i');
            $csvData .= "{$date},{$order->invoice_code},{$order->customer_name},{$order->order_type},{$order->total_price}\n";
        }

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $filename);
    }

    public function with(): array
    {
        $user = Auth::user();
        $store = StoreSetting::first();

        $stats = [
            'orders_today' => 0,
            'revenue_today' => 0,
            'revenue_month' => 0,
            'pending_orders' => 0,
            'active_products' => 0,
            'wallet_balance' => 0, // Tambahkan ini
            'fee_per_trx' => $this->feePerTransaction, // Tambahkan ini untuk kalkulasi UI
            'revenue_trend_today' => 0,
            'revenue_trend_month' => 0,
        ];

        $recentOrders = [];
        $topProducts = [];
        $newOrderCount = 0;
        $chartData = [];

        if ($store) {
            // Stats Hari Ini & Bulanan
            $stats['orders_today'] = Order::whereDate('created_at', today())->count();
            $stats['revenue_today'] = Order::whereDate('created_at', today())->whereIn('status', ['paid', 'completed'])->sum('total_price');
            $stats['revenue_month'] = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->whereIn('status', ['paid', 'completed'])->sum('total_price');
            $stats['pending_orders'] = Order::where('status', 'pending')->count();
            $stats['active_products'] = Product::where('is_active', true)->count();

            // Calculate Trends
            $yesterdayRevenue = Order::whereDate('created_at', today()->subDay())->whereIn('status', ['paid', 'completed'])->sum('total_price');
            if ($yesterdayRevenue > 0) {
                $stats['revenue_trend_today'] = round((($stats['revenue_today'] - $yesterdayRevenue) / $yesterdayRevenue) * 100);
            } else {
                $stats['revenue_trend_today'] = $stats['revenue_today'] > 0 ? 100 : 0;
            }

            $lastMonthRevenue = Order::whereMonth('created_at', today()->subMonth()->format('m'))
                ->whereYear('created_at', today()->subMonth()->format('Y'))
                ->whereIn('status', ['paid', 'completed'])
                ->sum('total_price');
                
            if ($lastMonthRevenue > 0) {
                $stats['revenue_trend_month'] = round((($stats['revenue_month'] - $lastMonthRevenue) / $lastMonthRevenue) * 100);
            } else {
                $stats['revenue_trend_month'] = $stats['revenue_month'] > 0 ? 100 : 0;
            }

            // 7 Days Chart Data
            $chartData = collect(range(6, 0))->map(function($daysAgo) {
                $date = today()->subDays($daysAgo);
                return [
                    'date' => $date->format('d M'),
                    'revenue' => Order::whereDate('created_at', $date)->whereIn('status', ['paid', 'completed'])->sum('total_price')
                ];
            });

            // AMBIL SALDO WALLET
            $stats['wallet_balance'] = app(TenantWalletService::class)->getWallet()->balance;

            // 5 Pesanan Terbaru
            $recentOrders = Order::latest()->take(5)->get();

            // Produk Terlaris
            try {
                $topProducts = DB::table('order_items')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
                    ->whereMonth('orders.created_at', date('m'))
                    ->whereIn('orders.status', ['paid', 'completed'])
                    ->groupBy('order_items.product_name')
                    ->orderByDesc('total_sold')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
                $topProducts = collect([]);
            }

            $newOrderCount = Order::where('id', '>', $this->lastCheckedOrderId)->count();
        }

        return [
            'user' => $user,
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'newOrderCount' => $newOrderCount,
            'chartData' => collect($chartData)->toJson(),
        ];
    }
};
