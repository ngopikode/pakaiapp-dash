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

    public function processCheckout($cart, $customerName, $customerPhone, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        return DB::transaction(function () use ($cart, $customerName, $customerPhone, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid) {

            // Array sementara untuk menyimpan objek varian dari DB agar tidak query dua kali
            $dbVariants = [];

            // VALIDASI STOK (Hanya ke tabel ProductVariant)
            foreach ($cart as $index => $item) {
                // Semua item DARI FRONTEND PASTI punya variant_id sekarang
                $variant = ProductVariant::lockForUpdate()->find($item['variant_id']);

                if (!$variant || $variant->stock < $item['quantity']) {
                    return [
                        'success' => false,
                        'error' => "Gagal! Stok '{$item['name']}' tidak cukup. Sisa stok asli: " . ($variant ? $variant->stock : 0)
                    ];
                }

                // Simpan untuk dipanggil saat insert agar menghemat query
                $dbVariants[$index] = $variant;
            }

            // 2. KALKULASI & SIMPAN ORDER UTAMA
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
                // 'tenant_id' => Auth::user()->restaurant_id, // Sesuaikan dengan tenancy-mu
                'user_id' => Auth::id(),
            ]);

            // 3. SIMPAN DETAIL ITEM & POTONG STOK
            foreach ($cart as $index => $item) {
                $variant = $dbVariants[$index];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'product_variant_id' => $variant->id,
                    'product_name' => $item['name'],
                    'variant_name' => $item['variant_name'] ?? null,
                    'quantity' => $item['quantity'],

                    // SNAPSHOT HPP (Sangat penting untuk fitur Laba Rugi!)
                    'snapshot_cost' => $variant->cost,
                    'snapshot_price' => $variant->price,

                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'] ?? null,
                ]);

                // Kurangi stok (hanya perlu mengurangi dari tabel varian)
                $variant->decrement('stock', $item['quantity']);
            }

            // 4. BERHASIL! Kembalikan data untuk Struk WA
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
