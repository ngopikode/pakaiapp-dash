<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\StoreSetting;
use App\Services\TenantWalletService;
use App\Services\BillingService;
use App\Services\DuitkuService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public string $activeTab = 'cashier';
    public ?int $addToOrder = null;
    
    public ?Order $existingOrder = null;

    // Tarif potong kredit per transaksi sukses
    private int $feePerTransaction = 300;

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount(?int $addToOrder = null): void
    {
        $this->addToOrder = $addToOrder;
        if ($this->addToOrder) {
            $this->existingOrder = Order::find($this->addToOrder);
            // Allow editing if it's pending OR if it's progress but not fully paid
            if ($this->existingOrder) {
                $isEditable = $this->existingOrder->status === 'pending' || 
                             ($this->existingOrder->status === 'progress' && $this->existingOrder->amount_paid < $this->existingOrder->total_price);
                
                if (!$isEditable) {
                    $this->addToOrder = null;
                    $this->existingOrder = null;
                }
            }
        }
    }

    public function setEditOrder($orderId): void
    {
        $this->addToOrder = $orderId;
        $this->existingOrder = Order::find($orderId);
        
        if ($this->existingOrder) {
            $isEditable = $this->existingOrder->status === 'pending' || 
                         ($this->existingOrder->status === 'progress' && $this->existingOrder->amount_paid < $this->existingOrder->total_price);
            
            if (!$isEditable) {
                $this->addToOrder = null;
                $this->existingOrder = null;
                $this->js("window.showIslandToast('Pesanan tidak dapat diedit.', 'danger');");
                return;
            }
        }

        $this->activeTab = 'cashier';
        $customer = addslashes($this->existingOrder->customer_name ?? '');
        $table = addslashes($this->existingOrder->table_number ?? $this->existingOrder->notes ?? '');
        $type = addslashes($this->existingOrder->order_type ?? '');
        $invoice = addslashes($this->existingOrder->invoice_code ?? '');
        
        $this->js("window.dispatchEvent(new CustomEvent('start-editing-order', { detail: { invoice_code: '{$invoice}', customer: '{$customer}', table: '{$table}', type: '{$type}' } }));");
    }

    public function cancelEditOrder(): void
    {
        $this->addToOrder = null;
        $this->existingOrder = null;
    }

    public function voidItem($orderItemId): void
    {
        try {
            DB::transaction(function () use ($orderItemId) {
                $item = OrderItem::with('order')->lockForUpdate()->find($orderItemId);
                if (!$item) {
                    throw new Exception("Item tidak ditemukan.");
                }

                $order = $item->order;
                if (!$order || in_array($order->status, ['completed', 'cancelled'])) {
                    throw new Exception("Pesanan sudah selesai atau dibatalkan, tidak bisa void.");
                }

                if (in_array($item->kitchen_status, ['processing', 'ready', 'completed'])) {
                    throw new Exception("Item sedang/sudah diproses oleh dapur dan tidak bisa dibatalkan.");
                }

                // Increment stock
                if ($item->variant_id) {
                    $variant = ProductVariant::with('recipes.rawMaterial')->lockForUpdate()->find($item->variant_id);
                    if ($variant) {
                        $variant->increment('stock', $item->quantity);
                        
                        if (tenant('store_type') === 'resto') {
                            foreach ($variant->recipes as $recipe) {
                                if ($recipe->rawMaterial) {
                                    $recipe->rawMaterial->increment('stock', $recipe->quantity_used * $item->quantity);
                                }
                            }
                        }
                    }
                }

                // Delete item
                $subtotalToDeduct = $item->subtotal;
                $item->delete();

                // Recalculate order totals
                $taxRate = (float)$order->tax_percentage;
                $serviceRate = (float)$order->service_charge_percentage;

                $newSubtotal = max(0, $order->subtotal - $subtotalToDeduct);
                $newServiceCharge = round(($serviceRate / 100) * $newSubtotal);
                $newTaxAmount = round(($taxRate / 100) * ($newSubtotal + $newServiceCharge));
                $newTotalPrice = max(0, $newSubtotal + $newServiceCharge + $newTaxAmount - $order->discount);

                $order->update([
                    'subtotal' => $newSubtotal,
                    'service_charge_amount' => $newServiceCharge,
                    'tax_amount' => $newTaxAmount,
                    'total_price' => $newTotalPrice,
                ]);
                
                // Refresh existing order
                if ($this->existingOrder && $this->existingOrder->id === $order->id) {
                    $this->existingOrder->refresh();
                }

                $this->js("window.showIslandToast('Item berhasil dibatalkan dan stok dikembalikan.', 'success');");
            });
        } catch (Exception $e) {
            $this->js("window.showIslandToast('Gagal membatalkan item: " . addslashes($e->getMessage()) . "', 'danger');");
        }
    }

    /**
     * Buat pesanan baru (status: pending, belum bayar).
     * TIDAK ADA POTONGAN KREDIT DI SINI.
     */
    public function createOrder($cart, $customerName, $tableNumber, $orderType, $isTaxActive = true, $isServiceActive = true)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        try {
            return DB::transaction(function () use ($cart, $customerName, $tableNumber, $orderType, $isTaxActive, $isServiceActive) {
                $dbVariants = [];

                foreach ($cart as $index => $item) {
                    $variant = ProductVariant::with('recipes.rawMaterial')->lockForUpdate()->find($item['variant_id']);
                    if (!$variant || $variant->stock < $item['quantity']) {
                        throw new Exception("Stok '{$item['name']}' tidak cukup. Sisa: " . ($variant ? $variant->stock : 0));
                    }
                    $dbVariants[$index] = $variant;
                }

                $storeSetting = StoreSetting::first();
                $taxRate = $isTaxActive ? (isset($storeSetting->tax_rate) ? (float)$storeSetting->tax_rate : 10.00) : 0.00;
                $serviceRate = $isServiceActive ? (isset($storeSetting->service_charge_rate) ? (float)$storeSetting->service_charge_rate : 5.00) : 0.00;

                $subtotal = collect($cart)->sum('subtotal');
                $serviceChargeAmount = round(($serviceRate / 100) * $subtotal);
                $taxAmount = round(($taxRate / 100) * ($subtotal + $serviceChargeAmount));
                $totalPrice = $subtotal + $serviceChargeAmount + $taxAmount;

                if ($this->addToOrder && $this->existingOrder) {
                    $order = $this->existingOrder;
                    // Update total for the existing order
                    $newSubtotal = $order->subtotal + $subtotal;
                    $newServiceCharge = round(($serviceRate / 100) * $newSubtotal);
                    $newTaxAmount = round(($taxRate / 100) * ($newSubtotal + $newServiceCharge));
                    $newTotalPrice = $newSubtotal + $newServiceCharge + $newTaxAmount;

                    $order->update([
                        'subtotal' => $newSubtotal,
                        'service_charge_amount' => $newServiceCharge,
                        'tax_amount' => $newTaxAmount,
                        'total_price' => $newTotalPrice,
                        'kitchen_status' => 'waiting', // Wajib di-reset agar muncul di dapur
                    ]);
                    $invoiceCode = $order->invoice_code;
                } else {
                    $invoiceCode = 'INV-' . strtoupper(Str::random(6));

                    $order = Order::create([
                        'invoice_code' => $invoiceCode,
                        'table_number' => $orderType === 'dinein' ? $tableNumber : null,
                        'notes' => $orderType !== 'dinein' ? $tableNumber : null,
                        'customer_name' => $customerName ?: 'Pelanggan Umum',
                        'order_type' => $orderType,
                        'payment_method' => 'cash',
                        'subtotal' => $subtotal,
                        'tax_amount' => $taxAmount,
                        'service_charge_amount' => $serviceChargeAmount,
                        'tax_percentage' => $taxRate,
                        'service_charge_percentage' => $serviceRate,
                        'discount' => 0,
                        'total_price' => $totalPrice,
                        'amount_paid' => 0,
                        'change_amount' => 0,
                        'status' => 'pending', // Belum bayar!
                        'user_id' => Auth::id(),
                    ]);
                }

                foreach ($cart as $index => $item) {
                    $variant = $dbVariants[$index];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'variant_id' => $variant->id,
                        'product_name' => $item['name'],
                        'variant_name' => $item['variant_name'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => (float)$item['price'],
                        'discount' => 0,
                        'subtotal' => $item['subtotal'],
                        'note' => $item['note'] ?? null,
                        // Tandai item ini belum diproses (baru ditambahkan)
                        'kitchen_status' => 'waiting' // Asumsi ada field ini atau kalau tidak biarkan default
                    ]);
                    $variant->decrement('stock', $item['quantity']);

                    if (tenant('store_type') === 'resto') {
                        foreach ($variant->recipes as $recipe) {
                            if ($recipe->rawMaterial) {
                                $recipe->rawMaterial->decrement('stock', $recipe->quantity_used * $item['quantity']);
                            }
                        }
                    }
                }

                return ['success' => true, 'invoice_code' => $invoiceCode];
            });
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Checkout langsung bayar (gabungan create order + payment)
     * POTONG KREDIT DI SINI.
     */
    public function processDirectCheckout($cart, $customerName, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid, $isTaxActive = true, $isServiceActive = true)
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        try {
            return DB::transaction(function () use ($cart, $customerName, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid, $isTaxActive, $isServiceActive) {
                $dbVariants = [];

                foreach ($cart as $index => $item) {
                    $variant = ProductVariant::with('recipes.rawMaterial')->lockForUpdate()->find($item['variant_id']);
                    if (!$variant || $variant->stock < $item['quantity']) {
                        throw new Exception("Stok '{$item['name']}' tidak cukup. Sisa: " . ($variant ? $variant->stock : 0));
                    }
                    $dbVariants[$index] = $variant;
                }

                $storeSetting = StoreSetting::first();
                $storeName = $storeSetting?->name ?? 'Resto Kami';
                $taxRate = $isTaxActive ? (isset($storeSetting->tax_rate) ? (float)$storeSetting->tax_rate : 10.00) : 0.00;
                $serviceRate = $isServiceActive ? (isset($storeSetting->service_charge_rate) ? (float)$storeSetting->service_charge_rate : 5.00) : 0.00;

                $subtotal = collect($cart)->sum('subtotal');
                $serviceChargeAmount = round(($serviceRate / 100) * $subtotal);
                $taxAmount = round(($taxRate / 100) * ($subtotal + $serviceChargeAmount));

                $discountAmount = (float)$discount;

                if ($this->addToOrder && $this->existingOrder) {
                    $order = $this->existingOrder;
                    
                    $newSubtotal = $order->subtotal + $subtotal;
                    $newServiceCharge = round(($serviceRate / 100) * $newSubtotal);
                    $newTaxAmount = round(($taxRate / 100) * ($newSubtotal + $newServiceCharge));
                    $newTotalPrice = max(0, $newSubtotal + $newServiceCharge + $newTaxAmount - $discountAmount);
                    
                    $paid = (float)$amountPaid ?: $newTotalPrice;
                    $change = max(0, $paid - $newTotalPrice);
                    $invoiceCode = $order->invoice_code;

                    $order->update([
                        'payment_method' => $paymentMethod,
                        'subtotal' => $newSubtotal,
                        'service_charge_amount' => $newServiceCharge,
                        'tax_amount' => $newTaxAmount,
                        'discount' => $discountAmount,
                        'total_price' => $newTotalPrice,
                        'amount_paid' => $paid,
                        'change_amount' => $change,
                        'status' => 'paid',
                        'kitchen_status' => 'waiting', // Wajib di-reset agar muncul di dapur
                    ]);
                } else {
                    $totalPrice = max(0, $subtotal + $serviceChargeAmount + $taxAmount - $discountAmount);
                    $paid = (float)$amountPaid ?: $totalPrice;
                    $change = max(0, $paid - $totalPrice);
                    $invoiceCode = 'INV-' . strtoupper(Str::random(6));

                    $order = Order::create([
                        'invoice_code' => $invoiceCode,
                        'table_number' => $orderType === 'dinein' ? $tableNumber : null,
                        'notes' => $orderType !== 'dinein' ? $tableNumber : null,
                        'customer_name' => $customerName ?: 'Pelanggan Umum',
                        'order_type' => $orderType,
                        'payment_method' => $paymentMethod,
                        'subtotal' => $subtotal,
                        'tax_amount' => $taxAmount,
                        'service_charge_amount' => $serviceChargeAmount,
                        'tax_percentage' => $taxRate,
                        'service_charge_percentage' => $serviceRate,
                        'discount' => $discountAmount,
                        'total_price' => $totalPrice,
                        'amount_paid' => $paid,
                        'change_amount' => $change,
                        'status' => 'paid',
                        'kitchen_status' => 'waiting',
                        'user_id' => Auth::id(),
                    ]);
                }

                foreach ($cart as $index => $item) {
                    $variant = $dbVariants[$index];
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $item['id'],
                        'variant_id' => $variant->id,
                        'product_name' => $item['name'],
                        'variant_name' => $item['variant_name'] ?? null,
                        'quantity' => $item['quantity'],
                        'price' => (float)$item['price'],
                        'cost' => $variant->cost,
                        'discount' => 0,
                        'subtotal' => $item['subtotal'],
                        'note' => $item['note'] ?? null,
                    ]);
                    $variant->decrement('stock', $item['quantity']);

                    if (tenant('store_type') === 'resto') {
                        foreach ($variant->recipes as $recipe) {
                            if ($recipe->rawMaterial) {
                                $recipe->rawMaterial->decrement('stock', $recipe->quantity_used * $item['quantity']);
                            }
                        }
                    }
                }

                // --- POTONG SALDO WALLET ---
                app(BillingService::class)->chargeTransactionFee($order);

                return [
                    'success'       => true,
                    'invoice_code'  => $order->invoice_code,
                    'customer_name' => $order->customer_name,
                    'customer_phone'=> null,
                    'store_name'    => $storeName,
                    'total_price'   => $totalPrice,
                ];
            });
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Proses pembayaran untuk pesanan yang sudah pending.
     * POTONG KREDIT DI SINI.
     */
    public function processPayment($orderId, $paymentMethod, $discount, $amountPaid)
    {
        try {
            // Gunakan DB Transaction karena kita update tabel Order DAN tabel Wallet bersamaan
            return DB::transaction(function () use ($orderId, $paymentMethod, $discount, $amountPaid) {

                // lockForUpdate memastikan pesanan tidak dibayar 2 kali di waktu bersamaan
                $order = Order::with('items')->lockForUpdate()->find($orderId);

                $isPayable = $order->status === 'pending' || 
                             ($order->status === 'progress' && $order->amount_paid < $order->total_price);

                if (!$order || !$isPayable) {
                    throw new Exception('Pesanan tidak ditemukan atau sudah dibayar.');
                }

                $discountAmount = (float)$discount;
                // Hitung ulang dari subtotal awal pesanan + service + tax agar diskon tidak terpotong dobel
                // Tapi karena ini proses dinamis, kita asumsikan total_price saat ini adalah base
                $baseTotal = isset($order->total_price) ? (float)$order->total_price : (float)$order->subtotal;
                
                // Jika total_price masih sama dengan yang ada di DB, berarti diskon baru ditambahkan
                // Tapi mari kita ambil aman: total yang harus dibayar adalah total akhir
                $totalPrice = max(0, $baseTotal - $discountAmount);
                $paid = (float)$amountPaid ?: $totalPrice;
                
                $accumulatedPaid = $order->amount_paid + $paid;
                $change = max(0, $accumulatedPaid - $totalPrice);

                if ($accumulatedPaid >= $totalPrice) {
                    $newStatus = ($order->kitchen_status === 'ready' || $order->kitchen_status === 'completed') ? 'completed' : 'paid';
                } else {
                    $newStatus = 'progress'; // Masih nyicil (Partial)
                }

                $order->update([
                    'payment_method' => $paymentMethod,
                    'discount' => $order->discount + $discountAmount, // Akumulasi diskon jika ada beberapa pembayaran dengan diskon
                    'total_price' => $totalPrice,
                    'amount_paid' => $accumulatedPaid,
                    'change_amount' => $change,
                    'status' => $newStatus,
                ]);

                // --- POTONG SALDO WALLET ---
                app(BillingService::class)->chargeTransactionFee($order);

                $storeName = StoreSetting::first()->name ?? 'Resto Kami';

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

    /**
     * Generate payment link Duitku untuk antrean pending.
     */
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

                $isPayable = $order->status === 'pending' || 
                             ($order->status === 'progress' && $order->amount_paid < $order->total_price);

                if (!$order || !$isPayable) {
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
     * Generate Midtrans snap token untuk antrean pending.
     */
    public function generateMidtransPayment($orderId, $customerEmail)
    {
        if (!config('midtrans.server_key')) {
            return ['success' => false, 'error' => 'Pembayaran digital Midtrans sedang tidak aktif.'];
        }

        $resolvedEmail = trim($customerEmail ?? '');
        if (empty($resolvedEmail) || !filter_var($resolvedEmail, FILTER_VALIDATE_EMAIL)) {
            $manager = \App\Models\TenantUser::where('role', 'manager')->first()
                ?? \App\Models\TenantUser::first();
            $resolvedEmail = $manager?->email ?? 'noreply@pakaiapp.online';
        }

        try {
            return DB::transaction(function () use ($orderId, $resolvedEmail) {
                $order = Order::with('items')->lockForUpdate()->find($orderId);

                $isPayable = $order->status === 'pending' || 
                             ($order->status === 'progress' && $order->amount_paid < $order->total_price);

                if (!$order || !$isPayable) {
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

                $midtransService = new \App\Services\MidtransService();
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
     * Dipanggil langsung dari blade via @cancel-confirmed.window
     */
    public function cancelOrder($data)
    {
        $orderId = $data['orderId'] ?? null;
        $note = $data['note'] ?? null;

        $order = Order::with('items.variant.recipes.rawMaterial')->find($orderId);
        if (!$order) return;

        if ($order->status === 'cancelled') {
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $this->js("window.showIslandToast('Pesanan sudah dibatalkan sebelumnya.', 'danger');");
            return;
        }

        if ($order->status !== 'pending') {
            if ($order->is_printed || \Carbon\Carbon::parse($order->created_at)->toDateString() !== today()->toDateString()) {
                $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
                $this->js("window.showIslandToast('Pesanan yang sudah dicetak struk atau lewat hari tidak bisa dibatalkan.', 'danger');");
                return;
            }
        }

        // --- VALIDASI FRAUD DAPUR ---
        // Jangan izinkan cancel jika dapur sudah memproses (processing) atau sudah selesai (ready/completed)
        $hasProcessedItems = $order->items()->whereIn('kitchen_status', ['processing', 'ready', 'completed'])->exists();
        if ($hasProcessedItems) {
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $this->js("window.showIslandToast('Pesanan tidak dapat dibatalkan secara keseluruhan karena sebagian/seluruh item sudah diproses oleh dapur.', 'danger');");
            return;
        }

        DB::transaction(function () use ($order, $note) {
            // Restore stock
            foreach ($order->items as $item) {
                if ($item->variant) {
                    $item->variant->increment('stock', $item->quantity);

                    if (tenant('store_type') === 'resto') {
                        foreach ($item->variant->recipes as $recipe) {
                            if ($recipe->rawMaterial) {
                                $recipe->rawMaterial->increment('stock', $recipe->quantity_used * $item->quantity);
                            }
                        }
                    }
                }
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

        // Dispatch sebagai window event agar cancel-modal component bisa dengar
        $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
        $this->js("window.showIslandToast('Pesanan berhasil dibatalkan.', 'success');");
    }

    public function splitOrder($orderId, $itemsToSplitData)
    {
        $order = Order::with('items')->find($orderId);
        
        if (!$order || in_array($order->status, ['completed', 'cancelled', 'paid'])) {
            $this->js("window.showIslandToast('Pesanan yang sudah lunas/selesai tidak bisa dipisah.', 'danger');");
            return;
        }

        if ($order->amount_paid > 0) {
            $this->js("window.showIslandToast('Pesanan yang sudah dicicil bayar tidak bisa dipisah per item.', 'danger');");
            return;
        }

        if (empty($itemsToSplitData)) {
            $this->js("window.showIslandToast('Pilih minimal 1 item untuk dipisah.', 'danger');");
            return;
        }

        try {
            $newOrderId = DB::transaction(function () use ($order, $itemsToSplitData) {
                // 1. Create New Order
                $storeSetting = StoreSetting::first();
                $taxRate = $order->tax_percentage ?? 10.00;
                $serviceRate = $order->service_charge_percentage ?? 5.00;

                $newInvoiceCode = $order->invoice_code . '-' . strtoupper(Str::random(3));
                
                $newOrder = Order::create([
                    'invoice_code' => $newInvoiceCode,
                    'table_number' => $order->table_number,
                    'notes' => $order->notes,
                    'customer_name' => $order->customer_name . ' (Split)',
                    'order_type' => $order->order_type,
                    'payment_method' => $order->payment_method,
                    'subtotal' => 0,
                    'tax_amount' => 0,
                    'service_charge_amount' => 0,
                    'tax_percentage' => $taxRate,
                    'service_charge_percentage' => $serviceRate,
                    'discount' => 0,
                    'total_price' => 0,
                    'amount_paid' => 0,
                    'change_amount' => 0,
                    'status' => 'pending',
                    'kitchen_status' => $order->kitchen_status,
                    'user_id' => Auth::id(),
                ]);

                $newSubtotal = 0;

                // 2. Process Items
                foreach ($itemsToSplitData as $splitData) {
                    $itemId = $splitData['id'];
                    $splitQty = (int) $splitData['qty'];
                    
                    if ($splitQty <= 0) continue;

                    $item = $order->items->where('id', $itemId)->first();
                    if (!$item) continue;

                    if ($splitQty < $item->quantity) {
                        $newItemSubtotal = $item->price * $splitQty;
                        \App\Models\OrderItem::create([
                            'order_id' => $newOrder->id,
                            'product_id' => $item->product_id,
                            'variant_id' => $item->variant_id,
                            'product_name' => $item->product_name,
                            'variant_name' => $item->variant_name,
                            'quantity' => $splitQty,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'subtotal' => $newItemSubtotal,
                            'note' => $item->note,
                            'kitchen_status' => $item->kitchen_status,
                        ]);
                        $newSubtotal += $newItemSubtotal;

                        $remainQty = $item->quantity - $splitQty;
                        $oldItemSubtotal = $item->price * $remainQty;
                        $item->update([
                            'quantity' => $remainQty,
                            'subtotal' => $oldItemSubtotal,
                        ]);
                    } else {
                        $item->update(['order_id' => $newOrder->id]);
                        $newSubtotal += $item->subtotal;
                    }
                }

                // 3. Recalculate New Order Totals
                $newServiceCharge = round(($serviceRate / 100) * $newSubtotal);
                $newTaxAmount = round(($taxRate / 100) * ($newSubtotal + $newServiceCharge));
                $newOrder->update([
                    'subtotal' => $newSubtotal,
                    'service_charge_amount' => $newServiceCharge,
                    'tax_amount' => $newTaxAmount,
                    'total_price' => $newSubtotal + $newServiceCharge + $newTaxAmount
                ]);

                // 4. Recalculate Old Order Totals
                $order->refresh();
                $oldSubtotal = $order->items->sum('subtotal');
                
                if ($oldSubtotal == 0 && $order->items->count() == 0) {
                    $order->delete();
                } else {
                    $oldServiceCharge = round(($serviceRate / 100) * $oldSubtotal);
                    $oldTaxAmount = round(($taxRate / 100) * ($oldSubtotal + $oldServiceCharge));
                    $order->update([
                        'subtotal' => $oldSubtotal,
                        'service_charge_amount' => $oldServiceCharge,
                        'tax_amount' => $oldTaxAmount,
                        'total_price' => $oldSubtotal + $oldServiceCharge + $oldTaxAmount - $order->discount
                    ]);
                }

                return $newOrder;
            });

            // For resto-cashier, we want to open the payment modal automatically for the new order
            $this->js("bootstrap.Modal.getInstance(document.getElementById('splitBillModal'))?.hide();");
            $this->js("window.showIslandToast('Pesanan berhasil dipisah.', 'success');");
            
            // To auto-open payment modal, we can dispatch to Alpine
            $orderData = json_encode([
                'id' => $newOrderId->id,
                'invoice_code' => $newOrderId->invoice_code,
                'customer_name' => $newOrderId->customer_name,
                'subtotal' => $newOrderId->subtotal,
                'total_price' => $newOrderId->total_price,
            ]);
            $this->js("window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: $orderData }));");
            
        } catch (\Exception $e) {
            $this->js("window.showIslandToast('Gagal memisah pesanan: " . addslashes($e->getMessage()) . "', 'danger');");
        }
    }

    /**
     * Gabung Struk (Merge Bill)
     * Menggabungkan Order Source ke dalam Order Target.
     */
    public function mergeOrder($sourceOrderId, $targetOrderId)
    {
        if ($sourceOrderId == $targetOrderId) {
            $this->js("window.showIslandToast('Pilih pesanan yang berbeda untuk digabungkan.', 'warning');");
            return;
        }

        try {
            DB::transaction(function () use ($sourceOrderId, $targetOrderId) {
                $sourceOrder = Order::with('items')->lockForUpdate()->find($sourceOrderId);
                $targetOrder = Order::with('items')->lockForUpdate()->find($targetOrderId);

                if (!$sourceOrder || !$targetOrder) {
                    throw new \Exception("Pesanan tidak ditemukan.");
                }

                if (in_array($sourceOrder->status, ['completed', 'cancelled']) || in_array($targetOrder->status, ['completed', 'cancelled'])) {
                    throw new \Exception("Tidak bisa menggabungkan pesanan yang sudah selesai atau dibatalkan.");
                }

                if ($sourceOrder->amount_paid > 0 || $targetOrder->amount_paid > 0) {
                    throw new \Exception("Tidak bisa menggabungkan pesanan yang sudah dicicil/memiliki pembayaran masuk.");
                }

                // 1. Pindahkan semua OrderItem ke Target Order
                foreach ($sourceOrder->items as $item) {
                    $item->update(['order_id' => $targetOrder->id]);
                }

                // 2. Gabungkan catatan dan nama pelanggan
                $newNotes = trim(($targetOrder->notes ? $targetOrder->notes . ' | ' : '') . ($sourceOrder->notes ?? ''));
                $newCustomerName = trim(($targetOrder->customer_name ? $targetOrder->customer_name . ' & ' : '') . ($sourceOrder->customer_name ?? ''));

                $targetOrder->notes = substr($newNotes, 0, 255);
                $targetOrder->customer_name = substr($newCustomerName, 0, 100);

                // 3. Kalkulasi ulang Target Order
                $targetOrder->refresh();
                $taxRate = (float)$targetOrder->tax_percentage;
                $serviceRate = (float)$targetOrder->service_charge_percentage;

                $newSubtotal = $targetOrder->items->sum('subtotal');
                $newServiceCharge = round(($serviceRate / 100) * $newSubtotal);
                $newTaxAmount = round(($taxRate / 100) * ($newSubtotal + $newServiceCharge));
                
                $targetOrder->update([
                    'subtotal' => $newSubtotal,
                    'service_charge_amount' => $newServiceCharge,
                    'tax_amount' => $newTaxAmount,
                    'total_price' => $newSubtotal + $newServiceCharge + $newTaxAmount - $targetOrder->discount
                ]);

                // 4. Hapus Source Order
                $sourceOrder->delete();
            });

            $this->js("window.showIslandToast('Pesanan berhasil digabungkan.', 'success');");
            
            // Refresh current order if it was the target
            if ($this->existingOrder && $this->existingOrder->id == $targetOrderId) {
                $this->existingOrder->refresh();
            }

        } catch (\Exception $e) {
            $this->js("window.showIslandToast('Gagal menggabungkan pesanan: " . addslashes($e->getMessage()) . "', 'danger');");
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

        // Ambil pesanan pending hari ini atau yang sedang diproses tapi belum lunas
        $pendingOrders = Order::with('items')
            ->whereDate('created_at', today())
            ->where(function ($query) {
                $query->where('status', 'pending')
                      ->orWhere(function ($q) {
                          $q->where('status', 'progress')
                            ->whereColumn('amount_paid', '<', 'total_price');
                      });
            })
            ->orderByDesc('created_at')
            ->get();

        return [
            'restoOrderTypes' => $orderTypes,
            'pendingOrders' => $pendingOrders,
            'isTaxActive' => isset($storeSetting->is_tax_active) ? (bool)$storeSetting->is_tax_active : true,
            'taxRate' => isset($storeSetting->tax_rate) ? (float)$storeSetting->tax_rate : 10.00,
            'isServiceChargeActive' => isset($storeSetting->is_service_charge_active) ? (bool)$storeSetting->is_service_charge_active : true,
            'serviceChargeRate' => isset($storeSetting->service_charge_rate) ? (float)$storeSetting->service_charge_rate : 5.00,
        ];
    }
};
