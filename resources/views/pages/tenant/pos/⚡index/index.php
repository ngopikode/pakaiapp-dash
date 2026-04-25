<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

new  class extends Component {

    public function processCheckout($cart, $customerName, $customerPhone, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        // BUNGKUS PAKAI DB TRANSACTION
        return DB::transaction(function () use ($cart, $customerName, $customerPhone, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid) {

            // 1. VALIDASI STOK REAL-TIME (SEBELUM BIKIN ORDER)
            foreach ($cart as $item) {
                if (!empty($item['variant_id'])) {
                    // lockForUpdate() mencegah race condition kalau ada 2 kasir checkout barang yang sama bersamaan
                    $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);
                    if (!$variant || $variant->stock < $item['quantity']) {
                        return [
                            'success' => false,
                            'error' => "Gagal! Stok varian '{$item['name']} - {$item['variant_name']}' tidak cukup. Sisa stok asli: " . ($variant ? $variant->stock : 0)
                        ];
                    }
                } else {
                    $product = Product::lockForUpdate()->find($item['id']);
                    if (!$product || $product->stock < $item['quantity']) {
                        return [
                            'success' => false,
                            'error' => "Gagal! Stok produk '{$item['name']}' tidak cukup. Sisa stok asli: " . ($product ? $product->stock : 0)
                        ];
                    }
                }
            }

            // 2. KALAU STOK AMAN, LANJUT HITUNG & SIMPAN
            $subtotal = collect($cart)->sum('subtotal');
            $discountAmount = (float)$discount;
            $totalPrice = max(0, $subtotal - $discountAmount);
            $paid = (float)$amountPaid ?: $totalPrice;
            $change = max(0, $paid - $totalPrice);

            $invoiceCode = 'INV-' . strtoupper(Str::random(6));

            $order = Order::create([
                'invoice_code' => $invoiceCode,
                'table_number' => $tableNumber ?: null,
                'customer_name' => $customerName ?: 'Pelanggan Umum',
                'customer_phone' => $customerPhone ?: null,
                'order_type' => $orderType,
                'payment_method' => $paymentMethod,
                'subtotal' => $subtotal,
                'discount' => $discountAmount,
                'total_price' => $totalPrice,
                'amount_paid' => $paid,
                'change_amount' => $change,
                'status' => 'paid',
                'user_id' => Auth::id(),
            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'variant_name' => $item['variant_name'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'] ?? null,
                ]);

                // Pengurangan stok (sekarang aman 100% karena udah divalidasi di atas)
                if (!empty($item['variant_id'])) {
                    ProductVariant::where('id', $item['variant_id'])->decrement('stock', $item['quantity']);
                } else {
                    Product::where('id', $item['id'])->decrement('stock', $item['quantity']);
                }
            }

            $storeName = StoreSetting::first()->name ?? 'Toko Kami';

            return [
                'success' => true,
                'invoice_code' => $invoiceCode,
                'customer_name' => $customerName ?: 'Pelanggan Umum',
                'customer_phone' => $customerPhone,
                'store_name' => $storeName,
                'total_price' => $totalPrice
            ];

        }); // End Transaction
    }
};
