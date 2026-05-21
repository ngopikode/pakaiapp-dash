<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Services\TenantWalletService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {

    public string $activeTab = 'cashier';

    public function changeTab($tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * Proses checkout retail: langsung bayar, support diskon per-item.
     */
    public function processCheckout($cart, $customerName, $customerPhone, $globalDiscount, $paymentMethod, $amountPaid)
    {
        if (empty($cart)) {
            return ['success' => false, 'error' => 'Keranjang kosong.'];
        }

        try {
            // Seluruh proses DB::transaction akan di-rollback otomatis jika ada Exception yang dilempar
            // Jika transaksi sukses, kembalikan response array ke frontend
            return DB::transaction(function () use ($cart, $customerName, $customerPhone, $globalDiscount, $paymentMethod, $amountPaid) {
                $dbVariants = [];

                // 1. VALIDASI STOK
                foreach ($cart as $index => $item) {
                    $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);

                    if (!$variant || $variant->stock < $item['quantity']) {
                        // LEMPAR EXCEPTION agar DB rollback!
                        throw new Exception("Stok '{$item['name']}' tidak cukup. Sisa: " . ($variant ? $variant->stock : 0));
                    }
                    $dbVariants[$index] = $variant;
                }

                // 2. KALKULASI
                $subtotal = collect($cart)->sum('subtotal');
                $totalItemDiscount = collect($cart)->sum(fn($i) => (float)($i['itemDiscount'] ?? 0));
                $globalDiscountAmount = (float)$globalDiscount;
                $totalDiscount = $totalItemDiscount + $globalDiscountAmount;
                $totalPrice = max(0, $subtotal - $globalDiscountAmount);
                $paid = (float)$amountPaid ?: $totalPrice;
                $change = max(0, $paid - $totalPrice);
                $invoiceCode = 'INV-' . strtoupper(Str::random(6));

                // 3. SIMPAN ORDER
                $order = Order::create([
                    'invoice_code' => $invoiceCode,
                    'customer_name' => $customerName ?: 'Pelanggan Umum',
                    'customer_phone' => $customerPhone ?: null,
                    'order_type' => 'retail',
                    'payment_method' => $paymentMethod,
                    'subtotal' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']),
                    'discount' => $totalDiscount,
                    'total_price' => $totalPrice,
                    'amount_paid' => $paid,
                    'change_amount' => $change,
                    'status' => 'paid',
                    'user_id' => Auth::id(),
                ]);

                // 4. SIMPAN DETAIL + POTONG STOK
                foreach ($cart as $index => $item) {
                    $variant = $dbVariants[$index];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'variant_id' => $variant->id,
                        'product_name' => $item['name'],
                        'variant_name' => $item['variant_name'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => $variant->price,
                        'discount' => (float)($item['itemDiscount'] ?? 0),
                        'subtotal' => $item['subtotal'],
                        'note' => null,
                    ]);
                    $variant->decrement('stock', $item['quantity']);
                }

                // ==========================================
                // 5. POTONG SALDO KREDIT (WALLET MERCHANT)
                // ==========================================
                $feePerTransaction = 300; // Contoh: Rp300 per transaksi
                $walletService = app(TenantWalletService::class);

                // Jika saldo tidak cukup, service ini akan melempar Exception.
                // Exception tersebut akan ditangkap di blok catch() bawah,
                // dan semua pembuatan Order + OrderItem di atas otomatis DIBATALKAN (Rollback).
                $walletService->deductBalance(
                    $feePerTransaction,
                    $order,
                    "Biaya layanan pakaiapp.online untuk Invoice: $invoiceCode"
                );
                // ==========================================

                $storeName = StoreSetting::first()->name ?? 'Toko Kami';

                return [
                    'success' => true,
                    'invoice_code' => $invoiceCode,
                    'customer_name' => $customerName ?: 'Pelanggan Umum',
                    'customer_phone' => $customerPhone,
                    'store_name' => $storeName,
                    'total_price' => $totalPrice,
                ];
            });

        } catch (Exception $e) {
            // Tangkap semua error: Stok Kurang, Saldo Dompet Habis, dll.
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateCustomerPhone($invoiceCode, $phone): void
    {
        Order::where('invoice_code', $invoiceCode)->update(['customer_phone' => $phone]);
    }

    public function with(): array
    {
        $todayOrders = Order::with('items')
            ->where('order_type', 'retail')
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        return [
            'todayOrders' => $todayOrders,
        ];
    }
};
