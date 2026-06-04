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
            'profit_today' => 0,
            'profit_month' => 0,
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
            
            $todayHpp = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
                ->whereDate('orders.created_at', today())
                ->whereIn('orders.status', ['paid', 'completed'])
                ->sum(DB::raw('order_items.quantity * product_variants.cost'));
            $stats['profit_today'] = $stats['revenue_today'] - $todayHpp;

            $monthHpp = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('product_variants', 'order_items.variant_id', '=', 'product_variants.id')
                ->whereMonth('orders.created_at', date('m'))
                ->whereYear('orders.created_at', date('Y'))
                ->whereIn('orders.status', ['paid', 'completed'])
                ->sum(DB::raw('order_items.quantity * product_variants.cost'));
            $stats['profit_month'] = $stats['revenue_month'] - $monthHpp;
            
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
            $sevenDaysAgo = today()->subDays(6)->startOfDay();
            
            // Standardizing date extraction to support different DB drivers
            $dailyRevenues = Order::where('created_at', '>=', $sevenDaysAgo)
                ->whereIn('status', ['paid', 'completed'])
                ->select(DB::raw('DATE(created_at) as date_string'), DB::raw('SUM(total_price) as total_revenue'))
                ->groupBy('date_string')
                ->pluck('total_revenue', 'date_string');

            $chartData = collect(range(6, 0))->map(function($daysAgo) use ($dailyRevenues) {
                $date = today()->subDays($daysAgo);
                return [
                    'date' => $date->format('d M'),
                    'revenue' => $dailyRevenues->get($date->format('Y-m-d'), 0)
                ];
            });

            // AMBIL SALDO WALLET
            $stats['wallet_balance'] = app(TenantWalletService::class)->getWallet()->balance;

            // 5 Pesanan Terbaru
            $recentOrders = Order::latest()->take(5)->get();

            // Produk Terlaris, Metode Pembayaran, & Tipe Pesanan
            $paymentMethods = collect([]);
            $orderTypes = collect([]);
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
                    
                $paymentMethods = DB::table('orders')
                    ->select('payment_method', DB::raw('COUNT(id) as total'), DB::raw('SUM(total_price) as total_amount'))
                    ->whereMonth('created_at', date('m'))
                    ->whereIn('status', ['paid', 'completed'])
                    ->groupBy('payment_method')
                    ->get();
                    
                $orderTypes = DB::table('orders')
                    ->select('order_type', DB::raw('COUNT(id) as total'))
                    ->whereMonth('created_at', date('m'))
                    ->whereIn('status', ['paid', 'completed'])
                    ->groupBy('order_type')
                    ->get();
            } catch (\Exception $e) {
                $topProducts = collect([]);
            }

            $peakSalesTimes = collect([]);
            $slowMovingProducts = collect([]);

            try {
                // Peak Sales Time (Top 3 Hours in the last 30 days)
                $peakSalesTimes = DB::table('orders')
                    ->select(DB::raw("STRFTIME('%H', created_at) as hour"), DB::raw("COUNT(id) as total_orders"))
                    ->whereIn('status', ['paid', 'completed'])
                    ->where('created_at', '>=', today()->subDays(30))
                    ->groupBy('hour')
                    ->orderByDesc('total_orders')
                    ->limit(3)
                    ->get()
                    ->map(function ($item) {
                        return (object) [
                            'time_range' => $item->hour . ':00 - ' . str_pad((int)$item->hour + 1, 2, '0', STR_PAD_LEFT) . ':00',
                            'orders' => $item->total_orders
                        ];
                    });

                // Slow-moving Items (Bottom 5 Active Products by quantity sold in the last 30 days)
                $slowMovingProducts = DB::table('products')
                    ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
                    ->leftJoin('orders', function ($join) {
                        $join->on('order_items.order_id', '=', 'orders.id')
                             ->whereIn('orders.status', ['paid', 'completed'])
                             ->where('orders.created_at', '>=', today()->subDays(30));
                    })
                    ->select('products.name', DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'))
                    ->where('products.is_active', true)
                    ->groupBy('products.id', 'products.name')
                    ->orderBy('total_sold', 'asc')
                    ->limit(5)
                    ->get();
            } catch (\Exception $e) {
            }

            $newOrderCount = Order::where('id', '>', $this->lastCheckedOrderId)->count();
        }

        return [
            'user' => $user,
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'paymentMethods' => $paymentMethods ?? collect([]),
            'orderTypes' => $orderTypes ?? collect([]),
            'peakSalesTimes' => $peakSalesTimes,
            'slowMovingProducts' => $slowMovingProducts,
            'newOrderCount' => $newOrderCount,
            'chartData' => collect($chartData)->toJson(),
        ];
    }
};
