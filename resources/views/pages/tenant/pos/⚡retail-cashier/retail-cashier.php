<?php

use App\Central\Services\BillingService;
use App\Central\Services\DuitkuService;
use App\Central\Services\MidtransService;
use App\Tenant\Data\ProcessOrderData;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\ProductVariant;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Models\Core\TenantUser;
use App\Tenant\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

new class extends Component
{
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

                // Gunakan OrderService untuk logika tersentralisasi
                $dto = new ProcessOrderData(
                    invoiceCode: 'INV-' . strtoupper(Str::random(6)),
                    customerName: $customerName ?: 'Pelanggan Umum',
                    customerPhone: $customerPhone ?: null,
                    orderType: 'retail',
                    paymentMethod: $paymentMethod,
                    globalDiscount: (float)$globalDiscount,
                    amountPaid: (float)$amountPaid,
                    changeAmount: max(0, (float)$amountPaid - (float)$finalTotal),
                    status: 'completed',
                    userId: Auth::id(),
                );

                $orderService = app(OrderService::class);
                $order = $orderService->processOrder($dto, $cart);

                $totalPrice = $order->total_price;
                $paid = (float)$amountPaid ?: $totalPrice;
                $change = max(0, $paid - $totalPrice);

                $order->update([
                    'amount_paid' => $paid,
                    'change_amount' => $change,
                ]);

                $invoiceCode = $order->invoice_code;

                // ==========================================
                // 5. POTONG SALDO KREDIT (WALLET MERCHANT)
                // ==========================================
                // Potong saldo dengan sistem dinamis
                app(BillingService::class)->chargeTransactionFee($order);
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
                'error' => $e->getMessage(),
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
                'variants' => $p->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'price' => (float)$v->price,
                    'active_discount_price' => $v->active_discount_price ? (float)$v->active_discount_price : null,
                    'active_discount_name' => $v->active_discount_name,
                    'stock' => (int)$v->stock,
                ])->toArray(),
            ];

            $formattedVariant = [
                'id' => $variant->id,
                'name' => $variant->name,
                'price' => (float)$variant->price,
                'active_discount_price' => $variant->active_discount_price ? (float)$variant->active_discount_price : null,
                'active_discount_name' => $variant->active_discount_name,
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
                app(BillingService::class)->chargeTransactionFee($order);

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
            $manager = TenantUser::where('role', 'manager')->first()
                ?? TenantUser::first();
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

                $duitkuService = new DuitkuService;
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
     * Generate Midtrans snap token untuk antrean pending.
     */
    public function generateMidtransPayment($orderId, $customerEmail)
    {
        if (!config('midtrans.server_key')) {
            return ['success' => false, 'error' => 'Pembayaran digital Midtrans sedang tidak aktif.'];
        }

        $resolvedEmail = trim($customerEmail ?? '');
        if (empty($resolvedEmail) || !filter_var($resolvedEmail, FILTER_VALIDATE_EMAIL)) {
            $manager = TenantUser::where('role', 'manager')->first()
                ?? TenantUser::first();
            $resolvedEmail = $manager?->email ?? 'noreply@pakaiapp.online';
        }

        try {
            return DB::transaction(function () use ($orderId, $resolvedEmail) {
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

                $midtransService = new MidtransService;
                $tenantId = tenant()->getTenantKey();

                $snapToken = $midtransService->createSnapToken(
                    $order,
                    $customerDetail,
                    $tenantId
                );

                $order->update([
                    'midtrans_snap_token' => $snapToken,
                    'payment_method' => 'transfer',
                    'midtrans_payment_type' => 'snap',
                ]);

                return [
                    'success' => true,
                    'snap_token' => $snapToken,
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

        if ($order->status === 'cancelled') {
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $this->js("window.showIslandToast('Pesanan sudah dibatalkan sebelumnya.', 'danger');");

            return;
        }

        if ($order->status !== 'pending') {
            if ($order->is_printed || Carbon::parse($order->created_at)->toDateString() !== today()->toDateString()) {
                $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
                $this->js("window.showIslandToast('Pesanan yang sudah dicetak struk atau lewat hari tidak bisa dibatalkan.', 'danger');");

                return;
            }
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

            if ($order->getOriginal('status') !== 'pending') {
                app(BillingService::class)->processVoidPenalty($order);
            }
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
