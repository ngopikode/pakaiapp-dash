<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component {

    /**
     * Proses checkout retail: langsung bayar, support diskon per-item.
     */
    public function processCheckout($cart, $customerName, $customerPhone, $globalDiscount, $paymentMethod, $amountPaid)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        return DB::transaction(function () use ($cart, $customerName, $customerPhone, $globalDiscount, $paymentMethod, $amountPaid) {
            $dbVariants = [];

            // 1. VALIDASI STOK
            foreach ($cart as $index => $item) {
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                if (!$variant || $variant->stock < $item['quantity']) {
                    return [
                        'success' => false,
                        'error' => "Stok '{$item['name']}' tidak cukup. Sisa: " . ($variant ? $variant->stock : 0)
                    ];
                }
                $dbVariants[$index] = $variant;
            }

            // 2. KALKULASI (subtotal sudah memperhitungkan diskon per-item dari frontend)
            $subtotal = collect($cart)->sum('subtotal');
            $totalItemDiscount = collect($cart)->sum(fn($i) => (float) ($i['itemDiscount'] ?? 0));
            $globalDiscountAmount = (float) $globalDiscount;
            $totalDiscount = $totalItemDiscount + $globalDiscountAmount;
            $totalPrice = max(0, $subtotal - $globalDiscountAmount);
            $paid = (float) $amountPaid ?: $totalPrice;
            $change = max(0, $paid - $totalPrice);
            $invoiceCode = 'INV-' . strtoupper(Str::random(6));

            // 3. SIMPAN ORDER
            $order = Order::create([
                'invoice_code' => $invoiceCode,
                'customer_name' => $customerName ?: 'Pelanggan Umum',
                'customer_phone' => $customerPhone ?: null,
                'order_type' => 'retail',
                'payment_method' => $paymentMethod,
                'subtotal' => collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), // subtotal sebelum diskon item
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
                    'discount' => (float) ($item['itemDiscount'] ?? 0),
                    'subtotal' => $item['subtotal'],
                    'note' => null,
                ]);
                $variant->decrement('stock', $item['quantity']);
            }

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
    }

    public function updateCustomerPhone($invoiceCode, $phone): void
    {
        Order::where('invoice_code', $invoiceCode)->update(['customer_phone' => $phone]);
    }
};
