<?php

namespace App\Livewire;

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\TenantWalletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Lazy;
use Livewire\Attributes\Title;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

new #[Title('Dashboard Overview')]
#[Lazy]
class extends Component {
    public int $lastCheckedOrderId = 0;
    public string $dateFilter = 'today';
    public string $customStartDate = '';
    public string $customEndDate = '';

    // Tarif per transaksi, disamakan dengan kasir
    private int $feePerTransaction = 300;

    public function mount(): void
    {
        $this->lastCheckedOrderId = Order::max('id') ?? 0;
    }

    public function setDateFilter($filter)
    {
        $this->dateFilter = $filter;
    }

    public function applyCustomDateFilter()
    {
        if ($this->customStartDate && $this->customEndDate) {
            $this->dateFilter = 'custom';
        }
    }

    public function placeholder()
    {
        return <<<'HTML'
        <main class="pb-10 min-vh-100 p-4">
            <div class="space-y-6">
                <!-- Header Skeleton -->
                <div class="flex justify-between">
                    <div class="space-y-2">
                        <div class="h-8 w-48 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></div>
                        <div class="h-4 w-32 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></div>
                    </div>
                    <div class="h-10 w-64 bg-slate-200 dark:bg-slate-800 rounded-xl animate-pulse"></div>
                </div>
                
                <!-- KPI Skeleton -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="h-28 bg-slate-200 dark:bg-slate-800 rounded-2xl animate-pulse"></div>
                    <div class="h-28 bg-slate-200 dark:bg-slate-800 rounded-2xl animate-pulse"></div>
                    <div class="h-28 bg-slate-200 dark:bg-slate-800 rounded-2xl animate-pulse"></div>
                    <div class="h-28 bg-slate-200 dark:bg-slate-800 rounded-2xl animate-pulse"></div>
                </div>

                <!-- Chart Skeleton -->
                <div class="h-72 bg-slate-200 dark:bg-slate-800 rounded-2xl animate-pulse"></div>
                
                <div class="flex flex-col items-center justify-center py-4">
                    <div class="w-8 h-8 border-4 border-slate-200 border-t-orange-500 rounded-full animate-spin mb-3"></div>
                    <p class="text-xs text-slate-500 font-bold animate-pulse">Menghitung Data F&B...</p>
                </div>
            </div>
        </main>
        HTML;
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
        $outOfStockCount = 0;
        $outOfStockFirst = null;

        if ($store) {
            $startDate = today()->startOfDay();
            $endDate = today()->endOfDay();

            if ($this->dateFilter === '7days') {
                $startDate = today()->subDays(6)->startOfDay();
            } elseif ($this->dateFilter === '30days') {
                $startDate = today()->subDays(29)->startOfDay();
            } elseif ($this->dateFilter === 'custom' && $this->customStartDate && $this->customEndDate) {
                $startDate = \Carbon\Carbon::parse($this->customStartDate)->startOfDay();
                $endDate = \Carbon\Carbon::parse($this->customEndDate)->endOfDay();
            }

            // Stats Terfilter (sebelumnya Hari Ini & Bulanan)
            $stats['orders_today'] = Order::whereBetween('created_at', [$startDate, $endDate])->count();
            $stats['revenue_today'] = Order::whereBetween('created_at', [$startDate, $endDate])->whereIn('status', ['paid', 'completed'])->sum('total_price');
            $stats['revenue_month'] = Order::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->whereIn('status', ['paid', 'completed'])->sum('total_price');

            $filteredHpp = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->whereBetween('orders.created_at', [$startDate, $endDate])
                ->whereIn('orders.status', ['paid', 'completed'])
                ->sum(DB::raw('order_items.quantity * order_items.cost'));
            $stats['profit_today'] = $stats['revenue_today'] - $filteredHpp;

            $monthHpp = DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->whereMonth('orders.created_at', date('m'))
                ->whereYear('orders.created_at', date('Y'))
                ->whereIn('orders.status', ['paid', 'completed'])
                ->sum(DB::raw('order_items.quantity * order_items.cost'));
            $stats['profit_month'] = $stats['revenue_month'] - $monthHpp;

            $stats['pending_orders'] = Order::where('status', 'pending')->count();
            $stats['active_products'] = Product::where('is_active', true)->count();

            // Calculate Trends (bandingkan dengan periode sebelumnya dengan durasi yang sama)
            if ($this->dateFilter === 'today') {
                $pastStartDate = today()->subDay()->startOfDay();
                $pastEndDate = today()->subDay()->endOfDay();
            } elseif ($this->dateFilter === '7days') {
                $pastStartDate = today()->subDays(13)->startOfDay();
                $pastEndDate = today()->subDays(7)->endOfDay();
            } elseif ($this->dateFilter === '30days') {
                $pastStartDate = today()->subDays(59)->startOfDay();
                $pastEndDate = today()->subDays(30)->endOfDay();
            } else {
                // Untuk custom date, bandingkan dengan periode sebelumnya yang berdurasi sama
                $diffInDays = $startDate->diffInDays($endDate);
                $pastEndDate = $startDate->copy()->subDay()->endOfDay();
                $pastStartDate = $pastEndDate->copy()->subDays($diffInDays)->startOfDay();
            }

            $pastRevenue = Order::whereBetween('created_at', [$pastStartDate, $pastEndDate])->whereIn('status', ['paid', 'completed'])->sum('total_price');
            if ($pastRevenue > 0) {
                $stats['revenue_trend_today'] = round((($stats['revenue_today'] - $pastRevenue) / $pastRevenue) * 100);
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

            // Chart Data Generation (Dynamic based on selected date range)
            if ($this->dateFilter === 'today') {
                // Hourly for today
                $hourlyRevenues = collect([]);
                try {
                    $hourlyRevenues = Order::whereBetween('created_at', [$startDate, $endDate])
                        ->whereIn('status', ['paid', 'completed'])
                        ->select(DB::raw('HOUR(created_at) as hour_string'), DB::raw('SUM(total_price) as total_revenue'))
                        ->groupBy('hour_string')
                        ->pluck('total_revenue', 'hour_string');
                } catch (\Exception $e) {}

                $chartData = collect(range(0, 23))->map(function ($hour) use ($hourlyRevenues) {
                    return [
                        'date' => sprintf('%02d:00', $hour),
                        'revenue' => $hourlyRevenues->get($hour, 0)
                    ];
                });
            } else {
                $diffInDays = $startDate->diffInDays($endDate);
                
                if ($diffInDays <= 31) {
                    $pointsCount = $diffInDays;
                    $chartStartDate = $startDate->copy()->startOfDay();

                    $dailyRevenues = Order::whereBetween('created_at', [$chartStartDate, $endDate])
                        ->whereIn('status', ['paid', 'completed'])
                        ->select(DB::raw('DATE(created_at) as date_string'), DB::raw('SUM(total_price) as total_revenue'))
                        ->groupBy('date_string')
                        ->pluck('total_revenue', 'date_string');

                    $chartData = collect(range($pointsCount, 0))->map(function ($daysAgo) use ($dailyRevenues, $endDate) {
                        $date = $endDate->copy()->subDays($daysAgo);
                        return [
                            'date' => $date->translatedFormat('d M'),
                            'revenue' => $dailyRevenues->get($date->format('Y-m-d'), 0)
                        ];
                    });
                } else {
                    $chartStartDate = $startDate->copy()->startOfDay();
                    
                    $monthlyRevenues = Order::whereBetween('created_at', [$chartStartDate, $endDate])
                        ->whereIn('status', ['paid', 'completed'])
                        ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month_string'), DB::raw('SUM(total_price) as total_revenue'))
                        ->groupBy('month_string')
                        ->pluck('total_revenue', 'month_string');

                    $diffInMonths = $startDate->copy()->startOfMonth()->diffInMonths($endDate->copy()->startOfMonth());
                    
                    $chartData = collect(range($diffInMonths, 0))->map(function ($monthsAgo) use ($monthlyRevenues, $endDate) {
                        $date = $endDate->copy()->startOfMonth()->subMonthsNoOverflow($monthsAgo);
                        return [
                            'date' => $date->translatedFormat('M Y'),
                            'revenue' => $monthlyRevenues->get($date->format('Y-m'), 0)
                        ];
                    });
                }
            }

            // AMBIL SALDO WALLET
            $stats['wallet_balance'] = app(TenantWalletService::class)->getWallet()->balance;

            // 5 Pesanan Terbaru
            $recentOrders = Order::latest()->take(5)->get();

            // Produk Terlaris, Metode Pembayaran, & Tipe Pesanan
            $paymentMethods = collect([]);
            $orderTypes = collect([]);
            $peakSalesTimes = collect([]);
            $slowMovingProducts = collect([]);
            $topProducts = collect([]);

            try {
                $cacheKeySuffix = $this->dateFilter . '_' . $startDate->format('Ymd') . '_' . $endDate->format('Ymd');
                $cacheTtl = 60; // 60 seconds (1 minute)

                $topProducts = collect(\Illuminate\Support\Facades\Cache::remember("dashboard_top_products_{$cacheKeySuffix}", $cacheTtl, function () use ($startDate, $endDate) {
                    return DB::table('order_items')
                        ->join('orders', 'order_items.order_id', '=', 'orders.id')
                        ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
                        ->whereBetween('orders.created_at', [$startDate, $endDate])
                        ->whereIn('orders.status', ['paid', 'completed'])
                        ->groupBy('order_items.product_name')
                        ->orderByDesc('total_sold')
                        ->limit(5)
                        ->get()
                        ->map(fn($item) => (array)$item)
                        ->toArray();
                }))->map(fn($item) => (object)$item);

                $paymentMethods = collect(\Illuminate\Support\Facades\Cache::remember("dashboard_payment_methods_{$cacheKeySuffix}", $cacheTtl, function () use ($startDate, $endDate) {
                    return DB::table('orders')
                        ->select('payment_method', DB::raw('COUNT(id) as total'), DB::raw('SUM(total_price) as total_amount'))
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->whereIn('status', ['paid', 'completed'])
                        ->groupBy('payment_method')
                        ->get()
                        ->map(fn($item) => (array)$item)
                        ->toArray();
                }))->map(fn($item) => (object)$item);

                $orderTypes = collect(\Illuminate\Support\Facades\Cache::remember("dashboard_order_types_{$cacheKeySuffix}", $cacheTtl, function () use ($startDate, $endDate) {
                    return DB::table('orders')
                        ->select('order_type', DB::raw('COUNT(id) as total'))
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->whereIn('status', ['paid', 'completed'])
                        ->groupBy('order_type')
                        ->get()
                        ->map(fn($item) => (array)$item)
                        ->toArray();
                }))->map(fn($item) => (object)$item);

                // Peak Hours F&B
                $peakSalesTimes = collect(\Illuminate\Support\Facades\Cache::remember("dashboard_peak_hours_{$cacheKeySuffix}", $cacheTtl, function () use ($startDate, $endDate) {
                    return DB::table('orders')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->whereIn('status', ['paid', 'completed'])
                        ->select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as orders'))
                        ->groupBy('hour')
                        ->orderByDesc('orders')
                        ->limit(3)
                        ->get()
                        ->map(function ($item) {
                            $item->time_range = sprintf('%02d:00 - %02d:00', $item->hour, $item->hour + 1);
                            return (array)$item;
                        })
                        ->toArray();
                }))->map(fn($item) => (object)$item);

                // Slow Moving Products (kurang laku)
                $slowMovingProducts = collect(\Illuminate\Support\Facades\Cache::remember("dashboard_slow_moving_{$cacheKeySuffix}", $cacheTtl, function () use ($startDate, $endDate) {
                    return DB::table('order_items')
                        ->join('orders', 'orders.id', '=', 'order_items.order_id')
                        ->whereBetween('orders.created_at', [$startDate, $endDate])
                        ->whereIn('orders.status', ['paid', 'completed'])
                        ->select('order_items.product_name', DB::raw('SUM(order_items.quantity) as total_sold'))
                        ->groupBy('order_items.product_name')
                        ->having('total_sold', '<', 5)
                        ->orderBy('total_sold')
                        ->limit(3)
                        ->get()
                        ->map(fn($item) => (array)$item)
                        ->toArray();
                }))->map(fn($item) => (object)$item);
            } catch (\Exception $e) {
                // Ignore jika ada fungsi SQL yang tidak di-support driver database tertentu
            }

            try {
                // Out of stock calculation via ProductVariant
                $outOfStockVariants = \App\Tenant\Models\Core\ProductVariant::with('product')
                    ->where('stock', '<=', 0)
                    ->whereHas('product', fn($q) => $q->where('is_active', true))
                    ->get();
                    
                $outOfStockCount = $outOfStockVariants->count();
                if ($outOfStockCount > 0) {
                    $outOfStockFirst = $outOfStockVariants->first()->product->name ?? $outOfStockVariants->first()->name;
                }
            } catch (\Exception $e) {}

            $newOrderCount = Order::where('id', '>', $this->lastCheckedOrderId)->count();
        }

        $totalPaymentVol = isset($paymentMethods) ? $paymentMethods->sum('total') : 0;
        $totalOrderTypes = isset($orderTypes) ? $orderTypes->sum('total') : 0;
        $maxPeak = isset($peakSalesTimes) ? ($peakSalesTimes->max('orders') ?: 1) : 1;

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
            'totalPaymentVol' => $totalPaymentVol,
            'totalOrderTypes' => $totalOrderTypes,
            'maxPeak' => $maxPeak,
            'newOrderCount' => $newOrderCount,
            'outOfStockCount' => $outOfStockCount,
            'outOfStockFirst' => $outOfStockFirst,
            'chartData' => collect($chartData)->toJson(),
        ];
    }
};
