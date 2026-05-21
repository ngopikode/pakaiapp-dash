<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Services\TenantWalletService;
use App\Services\DuitkuService;
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

    public function scanBarcode($sku)
    {
        $variant = ProductVariant::with(['product.variants', 'product.extras'])->where('sku', $sku)->first();
        if ($variant) {
            $p = $variant->product;
            $formattedProduct = [
                'id' => $p->id,
                'name' => $p->name,
                'category_id' => $p->category_id,
                'has_variants' => (bool)$p->has_variants,
                'price' => (float)$p->variants->min('price'),
                'stock' => (int)$p->variants->sum('stock'),
                'variants' => $p->variants->map(fn($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'price' => (float)$v->price,
                    'stock' => (int)$v->stock,
                ])->toArray(),
            ];

            $formattedVariant = [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => (float)$variant->price,
                'stock' => (int)$variant->stock,
            ];

            $this->dispatch('barcode-scanned', product: $formattedProduct, variant: $formattedVariant);
        } else {
            $this->dispatch('barcode-not-found', sku: $sku);
        }
    }

    public function processPayment($orderId, $paymentMethod, $discount, $amountPaid)
    {
        try {
            return DB::transaction(function () use ($orderId, $paymentMethod, $discount, $amountPaid) {
                $order = Order::with('items')->lockForUpdate()->find($orderId);

                if (!$order || $order->status !== 'pending') {
                    throw new Exception('Pesanan tidak ditemukan atau sudah dibayar.');
                }

                $discountAmount = (float)$discount;
                $totalPrice = max(0, (isset($order->total_price) ? (float)$order->total_price : (float)$order->subtotal) - $discountAmount);
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

                // --- POTONG SALDO WALLET ---
                $feePerTransaction = 300;
                app(TenantWalletService::class)->deductBalance(
                    $feePerTransaction,
                    $order,
                    "Biaya pelunasan transaksi retail pending {$order->invoice_code}"
                );

                $storeName = StoreSetting::first()->name ?? 'Toko Kami';

                return [
                    'success' => true,
                    'invoice_code' => $order->invoice_code,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'store_name' => $storeName,
                    'total_price' => $totalPrice,
                ];
            });
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function generateDuitkuPayment($orderId, $paymentMethod, $customerEmail)
    {
        if (!config('duitku.enabled')) {
            return ['success' => false, 'error' => 'Pembayaran digital Duitku sedang tidak aktif.'];
        }

        // Email opsional di kasir — fallback ke email manager jika tidak diisi
        $resolvedEmail = trim($customerEmail ?? '');
        if (empty($resolvedEmail) || !filter_var($resolvedEmail, FILTER_VALIDATE_EMAIL)) {
            $manager = \App\Models\TenantUser::where('role', 'manager')->first()
                ?? \App\Models\TenantUser::first();
            $resolvedEmail = $manager?->email ?? 'noreply@pakaiapp.online';
        }

        try {
            return DB::transaction(function () use ($orderId, $paymentMethod, $resolvedEmail) {
                $order = Order::with('items')->lockForUpdate()->find($orderId);

                if (!$order || $order->status !== 'pending') {
                    throw new Exception('Pesanan tidak ditemukan atau sudah dibayar.');
                }

                $customerDetail = [
                    'firstName' => $order->customer_name ?: 'Pelanggan',
                    'lastName' => '',
                    'email' => $resolvedEmail,
                    'phoneNumber' => $order->customer_phone ?: '',
                    'address' => 'Indonesia',
                    'city' => 'Jakarta',
                    'postalCode' => '00000',
                ];

                $duitkuService = new DuitkuService();
                $tenantId = tenant()->getTenantKey();

                $duitkuResult = $duitkuService->createInvoice(
                    $order,
                    $customerDetail,
                    $paymentMethod,
                    $tenantId
                );

                $order->update([
                    'duitku_reference' => $duitkuResult['reference'],
                    'duitku_payment_url' => $duitkuResult['payment_url'],
                    'duitku_va_number' => $duitkuResult['va_number'],
                    'duitku_payment_method' => $paymentMethod,
                ]);

                return [
                    'success' => true,
                    'payment_url' => $duitkuResult['payment_url'],
                ];
            });
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    /**
     * Batalkan pesanan pending (kembalikan stok).
     * Dipanggil dari blade via @cancel-confirmed.window
     */
    public function cancelOrder($data): void
    {
        $orderId = $data['orderId'] ?? null;
        $note = $data['note'] ?? null;

        $order = Order::with('items')->find($orderId);
        if (!$order) return;

        if ($order->status !== 'pending') {
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $this->js("window.showIslandToast('Pesanan sudah tidak bisa dibatalkan.', 'danger');");
            return;
        }

        DB::transaction(function () use ($order, $note) {
            foreach ($order->items as $item) {
                ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
            }
            $updateData = ['status' => 'cancelled'];
            if ($note) {
                $updateData['cancellation_note'] = $note;
            }
            $order->update($updateData);
        });

        $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
        $this->js("window.showIslandToast('Pesanan berhasil dibatalkan.', 'success');");
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
