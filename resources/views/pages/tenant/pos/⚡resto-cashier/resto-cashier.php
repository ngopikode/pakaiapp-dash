<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public string $activeTab = 'cashier';

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    /**
     * Buat pesanan baru (status: pending, belum bayar).
     * Kasir hanya input item + info pelanggan, bayar nanti dari tab Antrian.
     */
    public function createOrder($cart, $customerName, $tableNumber, $orderType)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        return DB::transaction(function () use ($cart, $customerName, $tableNumber, $orderType) {
            $dbVariants = [];

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

            $subtotal = collect($cart)->sum('subtotal');
            $invoiceCode = 'INV-' . strtoupper(Str::random(6));

            $order = Order::create([
                'invoice_code' => $invoiceCode,
                'table_number' => $orderType === 'dinein' ? $tableNumber : null,
                'customer_name' => $customerName ?: 'Pelanggan Umum',
                'order_type' => $orderType,
                'payment_method' => 'cash',
                'subtotal' => $subtotal,
                'discount' => 0,
                'total_price' => $subtotal,
                'amount_paid' => 0,
                'change_amount' => 0,
                'status' => 'pending', // Belum bayar!
                'user_id' => Auth::id(),
            ]);

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
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'] ?? null,
                ]);
                $variant->decrement('stock', $item['quantity']);
            }

            return ['success' => true, 'invoice_code' => $invoiceCode];
        });
    }

    /**
     * Checkout langsung bayar (gabungan create order + payment)
     */
    public function processDirectCheckout($cart, $customerName, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        return DB::transaction(function () use ($cart, $customerName, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid) {
            $dbVariants = [];

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

            $subtotal = collect($cart)->sum('subtotal');
            $discountAmount = (float)$discount;
            $totalPrice = max(0, $subtotal - $discountAmount);
            $paid = (float)$amountPaid ?: $totalPrice;
            $change = max(0, $paid - $totalPrice);
            $invoiceCode = 'INV-' . strtoupper(Str::random(6));

            $order = Order::create([
                'invoice_code' => $invoiceCode,
                'table_number' => $orderType === 'dinein' ? $tableNumber : null,
                'customer_name' => $customerName ?: 'Pelanggan Umum',
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
                    'discount' => 0,
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'] ?? null,
                ]);
                $variant->decrement('stock', $item['quantity']);
            }

            $storeName = StoreSetting::first()->name ?? 'Resto Kami';

            return [
                'success' => true,
                'invoice_code' => $order->invoice_code,
                'customer_name' => $order->customer_name,
                'customer_phone' => null,
                'store_name' => $storeName,
                'total_price' => $totalPrice,
            ];
        });
    }

    /**
     * Proses pembayaran untuk pesanan yang sudah pending.
     */
    public function processPayment($orderId, $paymentMethod, $discount, $amountPaid)
    {
        $order = Order::with('items')->find($orderId);
        if (!$order || $order->status !== 'pending') {
            return ['success' => false, 'error' => 'Pesanan tidak ditemukan atau sudah dibayar.'];
        }

        $discountAmount = (float)$discount;
        $totalPrice = max(0, $order->subtotal - $discountAmount);
        $paid = (float)$amountPaid ?: $totalPrice;
        $change = max(0, $paid - $totalPrice);

        $order->update([
            'payment_method' => $paymentMethod,
            'discount' => $discountAmount,
            'total_price' => $totalPrice,
            'amount_paid' => $paid,
            'change_amount' => $change,
            'status' => 'paid',
        ]);

        $storeName = StoreSetting::first()->name ?? 'Resto Kami';

        return [
            'success' => true,
            'invoice_code' => $order->invoice_code,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'store_name' => $storeName,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Batalkan pesanan pending (kembalikan stok).
     */
    #[On('process-cancel-order')]
    public function cancelOrder($data)
    {
        $orderId = $data['orderId'] ?? null;
        $note = $data['note'] ?? null;

        $order = Order::with('items')->find($orderId);
        if ($order && $order->status === 'pending') {
            DB::transaction(function () use ($order, $note) {
                // Restore stock
                foreach ($order->items as $item) {
                    ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                }

                $updateData = ['status' => 'cancelled'];
                if ($note) {
                    $updateData['cancellation_note'] = $note;
                }
                $order->update($updateData);
            });
            $this->dispatch('close-cancel-modal');
        }
    }

    public function updateCustomerPhone($invoiceCode, $phone): void
    {
        Order::where('invoice_code', $invoiceCode)->update(['customer_phone' => $phone]);
    }

    public function with(): array
    {
        $storeSetting = StoreSetting::first();
        $orderTypes = [];

        if ($storeSetting?->is_dinein_active) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];
        if ($storeSetting?->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        if ($storeSetting?->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
        if (empty($orderTypes)) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];

        // Ambil pesanan pending hari ini
        $pendingOrders = Order::with('items')
            ->where('status', 'pending')
            ->whereDate('created_at', today())
            ->orderByDesc('created_at')
            ->get();

        return [
            'restoOrderTypes' => $orderTypes,
            'pendingOrders' => $pendingOrders,
        ];
    }
};
