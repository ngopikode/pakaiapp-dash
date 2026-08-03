<?php

namespace App\Tenant\Services;

use App\Central\Services\BillingService;
use App\Tenant\Data\ProcessOrderData;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\OrderItem;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\ProductVariant;
use App\Tenant\Models\Core\Shift;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Models\Core\Wallet;
use App\Tenant\Models\Resto\ProductExtra;
use App\Tenant\Models\Resto\RawMaterial;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OrderService
{
    public const string OPERATION_INCREMENT = 'increment';

    public const string OPERATION_DECREMENT = 'decrement';

    protected ?TenantWalletService $walletService = null;

    protected function walletService(): TenantWalletService
    {
        return $this->walletService ??= app(TenantWalletService::class);
    }

    private function getWalletTypeForPayment(string $paymentMethod): string
    {
        return match (strtolower($paymentMethod)) {
            'qris', 'transfer', 'manual' => Wallet::TYPE_BANK,
            'duitku', 'midtrans', 'digital' => Wallet::TYPE_GATEWAY,
            default => Wallet::TYPE_CASH,
        };
    }

    private function processRevenue(Order $order, float $netRevenue, string $paymentMethod, string $description): void
    {
        $walletType = $this->getWalletTypeForPayment($paymentMethod);

        if ($walletType === Wallet::TYPE_CASH) {
            $storeSetting = StoreSetting::cached();
            if ($storeSetting && $storeSetting->is_shift_active) {
                $activeShift = Shift::where('user_id', Auth::id())->where('status', Shift::STATUS_ACTIVE)->first();
                if (!$activeShift) throw new Exception(message: 'Harap buka shift terlebih dahulu untuk menerima pembayaran tunai.');

                $activeShift->increment('cash_sales', $netRevenue);

                return;
            }
        }

        $this->walletService()->addBalance(
            amount: $netRevenue,
            reference: $order,
            description: $description,
            walletType: $walletType
        );
    }

    private function processRefund(Order $order, float $netRefund, string $paymentMethod, string $description): void
    {
        $walletType = $this->getWalletTypeForPayment($paymentMethod);

        if ($walletType === Wallet::TYPE_CASH) {
            $storeSetting = StoreSetting::cached();
            if ($storeSetting && $storeSetting->is_shift_active) {
                $activeShift = Shift::where('user_id', Auth::id())->where('status', Shift::STATUS_ACTIVE)->first();
                if ($activeShift) {
                    $activeShift->decrement('cash_sales', $netRefund);
                }

                return;
            }
        }

        $this->walletService()->deductBalance(
            amount: $netRefund,
            reference: $order,
            description: $description,
            walletType: $walletType
        );
    }

    protected ?BillingService $billingService = null;

    protected function billingService(): BillingService
    {
        return $this->billingService ??= app(BillingService::class);
    }

    protected ?SettingService $settingService = null;

    protected function settingService(): SettingService
    {
        return $this->settingService ??= app(SettingService::class);
    }

    /**
     * Memproses dan membuat Order secara tersentralisasi.
     *
     * @throws Throwable
     */
    public function processOrder(ProcessOrderData $data, array $items, ?Order $existingOrder = null): Order
    {
        try {
            DB::beginTransaction();

            $storeSetting = StoreSetting::cached();
            $isTaxActive = $data->isTaxActive ?? ($storeSetting && $storeSetting->is_tax_active);
            $taxRate = $isTaxActive ? (float)$storeSetting->tax_rate : 0.00;
            $isServiceActive = $data->isServiceActive ?? ($storeSetting && $storeSetting->is_service_charge_active);
            $serviceRate = $isServiceActive ? (float)$storeSetting->service_charge_rate : 0.00;

            $isAppFeeActive = $data->isApplicationFeePassed ?? ($storeSetting && $storeSetting->is_application_fee_passed);
            $applicationFeeAmount = $isAppFeeActive ? (float)$this->settingService()->get('default_trx_fee', tenant(), 300) : 0;

            // ponytail: default true agar tenant yang belum punya kolom (retail) tidak terdampak
            $isKitchenActive = (bool)($storeSetting->is_kitchen_active ?? true);

            $recalculatedItems = [];
            $realSubtotal = 0;

            $allVariantIds = [];
            $allProductIds = [];
            $allExtraIds = [];
            foreach ($items as $item) {
                $vid = $item['variant_id'] ?? null;
                if ($vid) $allVariantIds[] = $vid;
                if (!empty($item['variant_ids'])) {
                    $allVariantIds = array_merge($allVariantIds, $item['variant_ids']);
                }

                $pid = $item['product_id'] ?? $item['id'] ?? null;
                if ($pid) $allProductIds[] = $pid;

                if (!empty($item['extra_ids'])) {
                    $allExtraIds = array_merge($allExtraIds, $item['extra_ids']);
                }
            }
            $allVariantIds = array_unique($allVariantIds);
            $allProductIds = array_unique($allProductIds);
            $allExtraIds = array_unique($allExtraIds);

            $dbVariants = !empty($allVariantIds) ? ProductVariant::with(['recipes.rawMaterial', 'product'])
                ->whereIn('id', $allVariantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id') : collect();

            $dbProducts = !empty($allProductIds) ? Product::with('variants')
                ->whereIn('id', $allProductIds)
                ->get()
                ->keyBy('id') : collect();

            $dbExtras = !empty($allExtraIds) ? ProductExtra::whereIn('id', $allExtraIds)
                ->get()
                ->keyBy('id') : collect();

            foreach ($items as $item) {
                $productId = $item['product_id'] ?? $item['id'];
                $qty = (int)($item['quantity'] ?? $item['qty'] ?? 1);
                if ($qty <= 0) continue;

                $variantIds = $item['variant_ids'] ?? [];
                if (!empty($item['variant_id']) && !in_array($item['variant_id'], $variantIds)) {
                    $variantIds[] = $item['variant_id'];
                }

                $validVariantsObjects = collect();
                $cost = 0;
                $product = null;

                if (!empty($variantIds)) {
                    $itemVariants = $dbVariants->only($variantIds);
                    $product = $itemVariants->first()?->product ?? $dbProducts->get($productId);
                    if (!$product) throw new Exception(message: "Product ID $productId tidak ditemukan.");

                    $validVariantsObjects = $product->selection_type === 'multiple'
                        ? $itemVariants
                        : collect([$itemVariants->first()])->filter();

                    $originalPrice = (float)$validVariantsObjects->sum('price');
                    $discountedPrice = (float)$validVariantsObjects->sum(fn ($v) => $v->active_discount_price ?? $v->price);
                    $cost = (float)$validVariantsObjects->sum('cost');
                } else {
                    $product = $dbProducts->get($productId);
                    if (!$product) throw new Exception(message: "Product ID $productId tidak ditemukan.");

                    $originalPrice = (float)$product->price;
                    $activeDiscountPrice = $product->variants->min('active_discount_price');
                    $discountedPrice = (float)($activeDiscountPrice ?? $product->price);
                }

                $extraPrice = 0;
                $validExtraIds = [];
                if (!empty($item['extra_ids'])) {
                    $extras = $dbExtras->only($item['extra_ids'])->where('product_id', $product->id);
                    $extraPrice = $extras->sum('price');
                    $validExtraIds = $extras->pluck('id')->toArray();
                }

                $itemOriginalPrice = $originalPrice + $extraPrice;
                $itemDiscountedPrice = $discountedPrice + $extraPrice;
                $aiDiscount = max(0, $itemOriginalPrice - $itemDiscountedPrice);
                $manualDiscount = (float)($item['itemDiscount'] ?? $item['discount'] ?? 0);
                $totalItemDiscount = $aiDiscount + $manualDiscount;
                $itemSubtotal = max(0, ($itemOriginalPrice - $totalItemDiscount) * $qty);

                $realSubtotal += $itemSubtotal;

                $recalculatedItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $validVariantsObjects->first()?->id ?? null,
                    'product_name' => $item['name'] ?? $item['cartName'] ?? $product->name,
                    'variant_name' => !empty($item['variant_name'])
                        ? $item['variant_name']
                        : ($product->has_variants ? $validVariantsObjects->pluck('name')->join(', ') : null),
                    'quantity' => $qty,
                    'price' => $itemOriginalPrice,
                    'cost' => $cost,
                    'discount' => $totalItemDiscount,
                    'subtotal' => $itemSubtotal,
                    'note' => $item['note'] ?? null,
                    'kitchen_status' => $isKitchenActive ? 'waiting' : 'completed',
                    'selected_variants' => $validVariantsObjects->pluck('id')->toArray(),
                    'selected_extras' => $validExtraIds,
                    'valid_variants_objects' => $validVariantsObjects,
                ];
            }

            if (empty($recalculatedItems)) throw new Exception(message: 'Semua produk dalam pesanan tidak valid.');

            if ($existingOrder) {
                $order = $existingOrder;
                $newSubtotal = $order->subtotal + $realSubtotal;

                $calculations = $this->calculateTaxesAndTotal(
                    subtotal: $newSubtotal,
                    discount: (float)$order->discount,
                    taxRate: $taxRate,
                    serviceRate: $serviceRate,
                    applicationFeeAmount: (float)$order->application_fee
                );
                $calculations['kitchen_status'] = $isKitchenActive ? 'waiting' : 'completed';

                $order->update($calculations);
            } else {
                $globalDiscount = $data->globalDiscount;
                $calculations = $this->calculateTaxesAndTotal(
                    subtotal: $realSubtotal,
                    discount: $globalDiscount,
                    taxRate: $taxRate,
                    serviceRate: $serviceRate,
                    applicationFeeAmount: $applicationFeeAmount
                );

                $order = Order::create(array_merge([
                    'invoice_code' => $data->invoiceCode ?? 'INV-' . strtoupper(Str::random(6)),
                    'customer_name' => $data->customerName,
                    'customer_phone' => $data->customerPhone,
                    'customer_email' => $data->customerEmail,
                    'table_number' => $data->tableNumber,
                    'notes' => $data->notes,
                    'order_type' => $data->orderType,
                    'is_online' => $data->isOnline,
                    'payment_method' => $data->paymentMethod,
                    'duitku_payment_method' => $data->duitkuPaymentMethod,
                    'tax_percentage' => $taxRate,
                    'service_charge_percentage' => $serviceRate,
                    'amount_paid' => $data->amountPaid,
                    'change_amount' => $data->changeAmount,
                    'status' => $data->status,
                    'user_id' => $data->userId ?? Auth::id(),
                ], $calculations));
            }

            $orderItemData = [];
            $variantAdjustments = [];
            $rawMaterialAdjustments = [];
            $now = now();

            foreach ($recalculatedItems as $recalculatedItem) {
                $orderItemData[] = [
                    'order_id' => $order->id,
                    'product_id' => $recalculatedItem['product_id'],
                    'variant_id' => $recalculatedItem['variant_id'],
                    'product_name' => $recalculatedItem['product_name'],
                    'variant_name' => $recalculatedItem['variant_name'],
                    'quantity' => $recalculatedItem['quantity'],
                    'price' => $recalculatedItem['price'],
                    'cost' => $recalculatedItem['cost'],
                    'discount' => $recalculatedItem['discount'],
                    'subtotal' => $recalculatedItem['subtotal'],
                    'note' => $recalculatedItem['note'],
                    'kitchen_status' => $recalculatedItem['kitchen_status'],
                    'selected_variants' => json_encode($recalculatedItem['selected_variants']),
                    'selected_extras' => json_encode($recalculatedItem['selected_extras']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($recalculatedItem['valid_variants_objects'] as $variant) {
                    $this->aggregateStockAdjustments(
                        variant: $variant,
                        quantity: $recalculatedItem['quantity'],
                        variantAdjustments: $variantAdjustments,
                        rawMaterialAdjustments: $rawMaterialAdjustments
                    );
                }
            }

            if (!empty($orderItemData)) OrderItem::insert($orderItemData);

            $this->executeStockAdjustments(
                variantAdjustments: $variantAdjustments,
                rawMaterialAdjustments: $rawMaterialAdjustments
            );

            if (in_array($order->status, ['paid', 'completed'], true)) {
                $netRevenue = $order->total_price - (float)($order->application_fee ?? 0);
                if ($netRevenue > 0) {
                    $this->processRevenue(
                        order: $order,
                        netRevenue: $netRevenue,
                        paymentMethod: $order->payment_method,
                        description: "Penerimaan dana dari pesanan $order->invoice_code"
                    );
                }
            }

            event(new KitchenUpdated);

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function cancelOrder(int $orderId, ?string $note = null): Order
    {
        try {
            DB::beginTransaction();

            /** @var Order $order */
            $order = Order::with('items.variant.recipes.rawMaterial')->lockForUpdate()->find($orderId);
            if (!$order) throw new Exception(message: 'Pesanan tidak ditemukan.');

            if ($order->status === 'cancelled') throw new Exception(message: 'Pesanan sudah dibatalkan sebelumnya.');

            if ($order->status !== 'pending') {
                if (
                    $order->is_printed || Carbon::parse($order->created_at)->toDateString() !== today()->toDateString()
                ) throw new Exception(
                    message: 'Pesanan yang sudah dicetak struk atau lewat hari tidak bisa dibatalkan.'
                );
            }

            $hasProcessedItems = $order->items->whereIn('kitchen_status', ['processing', 'ready', 'completed'])->isNotEmpty();
            if ($hasProcessedItems) throw new Exception(
                message: 'Pesanan tidak dapat dibatalkan secara keseluruhan karena sebagian/seluruh item sudah diproses oleh dapur.'
            );

            $variantAdjustments = [];
            $rawMaterialAdjustments = [];

            foreach ($order->items as $item) {
                if ($item->variant) {
                    $this->aggregateStockAdjustments(
                        variant: $item->variant,
                        quantity: $item->quantity,
                        variantAdjustments: $variantAdjustments,
                        rawMaterialAdjustments: $rawMaterialAdjustments
                    );
                }
            }

            $this->executeStockAdjustments(
                variantAdjustments: $variantAdjustments,
                rawMaterialAdjustments: $rawMaterialAdjustments,
                operation: self::OPERATION_INCREMENT
            );

            $updateData = ['status' => 'cancelled'];
            if ($note) $updateData['cancellation_note'] = $note;
            $order->update($updateData);

            $originalStatus = $order->getOriginal('status');

            if ($originalStatus !== 'pending') {
                $this->billingService()->processVoidPenalty(order: $order);

                if (in_array($originalStatus, ['paid', 'completed'], true)) {
                    $netRevenue = $order->total_price - (float)($order->application_fee ?? 0);
                    if ($netRevenue > 0) {
                        $this->processRefund(
                            order: $order,
                            netRefund: $netRevenue,
                            paymentMethod: $order->payment_method,
                            description: "Pengembalian dana (refund) pembatalan pesanan $order->invoice_code"
                        );
                    }
                }
            }

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function voidItem(int $orderItemId): Order
    {
        try {
            DB::beginTransaction();

            $item = OrderItem::with('order')->lockForUpdate()->find($orderItemId);
            if (!$item) throw new Exception(message: 'Item tidak ditemukan.');

            $order = $item->order;
            if (
                !$order || in_array($order->status, ['completed', 'cancelled'], true)
            ) throw new Exception(message: 'Pesanan sudah selesai atau dibatalkan, tidak bisa void.');

            if (
                in_array($item->kitchen_status, ['processing', 'ready', 'completed'], true)
            ) throw new Exception(message: 'Item sedang/sudah diproses oleh dapur dan tidak bisa dibatalkan.');

            if (
                $order->items()->count() <= 1
            ) throw new Exception(
                message: "Ini adalah item terakhir. Silakan gunakan tombol 'Batalkan Pesanan' di bagian bawah untuk membatalkan pesanan secara penuh beserta alasannya."
            );

            $variantAdjustments = [];
            $rawMaterialAdjustments = [];

            if ($item->variant_id) {
                $variant = ProductVariant::with('recipes.rawMaterial')->lockForUpdate()->find($item->variant_id);
                if ($variant) {
                    $this->aggregateStockAdjustments(
                        variant: $variant,
                        quantity: $item->quantity,
                        variantAdjustments: $variantAdjustments,
                        rawMaterialAdjustments: $rawMaterialAdjustments
                    );
                }
            }

            $this->executeStockAdjustments(
                variantAdjustments: $variantAdjustments,
                rawMaterialAdjustments: $rawMaterialAdjustments,
                operation: self::OPERATION_INCREMENT
            );

            $subtotalToDeduct = $item->subtotal;
            $item->delete();

            $taxRate = (float)$order->tax_percentage;
            $serviceRate = (float)$order->service_charge_percentage;

            $newSubtotal = max(0, $order->subtotal - $subtotalToDeduct);

            $oldTotalPrice = $order->getOriginal('total_price') - (float)($order->getOriginal('application_fee') ?? 0);

            $order->update($this->calculateTaxesAndTotal(
                subtotal: $newSubtotal,
                discount: (float)$order->discount,
                taxRate: $taxRate,
                serviceRate: $serviceRate,
                applicationFeeAmount: (float)($order->application_fee ?? 0)
            ));

            $originalStatus = $order->getOriginal('status');
            if (in_array($originalStatus, ['paid', 'completed'], true)) {
                $newTotalPrice = $order->total_price - (float)($order->application_fee ?? 0);
                $refundAmount = max(0, $oldTotalPrice - $newTotalPrice);

                if ($refundAmount > 0) {
                    $this->processRefund(
                        order: $order,
                        netRefund: $refundAmount,
                        paymentMethod: $order->payment_method,
                        description: "Pengembalian dana void item pada pesanan $order->invoice_code"
                    );
                }
            }

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function processPayment(int $orderId, string $paymentMethod, float $discount, float $amountPaid): Order
    {
        try {
            DB::beginTransaction();

            $order = Order::with('items')->lockForUpdate()->find($orderId);

            $isPayable = $order && ($order->status === 'pending' ||
                    ($order->status === 'progress' && $order->amount_paid < $order->total_price));

            if (!$order || !$isPayable) throw new Exception(message: 'Pesanan tidak ditemukan atau sudah dibayar penuh.');

            $baseTotal = isset($order->total_price) ? (float)$order->total_price : (float)$order->subtotal;
            $totalPrice = max(0, $baseTotal - $discount);
            $paid = $amountPaid > 0 ? $amountPaid : $totalPrice;

            $accumulatedPaid = $order->amount_paid + $paid;
            $change = max(0, $accumulatedPaid - $totalPrice);

            if ($accumulatedPaid >= $totalPrice) {
                $newStatus = ($order->kitchen_status === 'ready' || $order->kitchen_status === 'completed') ? 'completed' : 'paid';
            } else {
                $newStatus = 'progress';
            }

            $order->update([
                'payment_method' => $paymentMethod,
                'discount' => $order->discount + $discount,
                'total_price' => $totalPrice,
                'amount_paid' => $accumulatedPaid,
                'change_amount' => $change,
                'status' => $newStatus,
            ]);

            if (in_array($newStatus, ['paid', 'completed'], true)) {
                $netRevenue = $totalPrice - (float)($order->application_fee ?? 0);
                if ($netRevenue > 0) {
                    $this->processRevenue(
                        order: $order,
                        netRevenue: $netRevenue,
                        paymentMethod: $paymentMethod,
                        description: "Penerimaan dana pembayaran pesanan $order->invoice_code"
                    );
                }
            }

            event(new KitchenUpdated);
            $this->billingService()->chargeTransactionFee(order: $order);

            DB::commit();

            return $order;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function splitOrder(int $orderId, array $itemsToSplitData): Order
    {
        try {
            DB::beginTransaction();

            /** @var Order $order */
            $order = Order::with('items')->lockForUpdate()->find($orderId);

            if (
                !$order || in_array($order->status, ['completed', 'cancelled', 'paid'], true)
            ) throw new Exception(message: 'Pesanan yang sudah lunas/selesai tidak bisa dipisah.');

            if ($order->amount_paid > 0) throw new Exception(message: 'Pesanan yang sudah dicicil bayar tidak bisa dipisah per item.');

            if (empty($itemsToSplitData)) throw new Exception(message: 'Pilih minimal 1 item untuk dipisah.');

            $taxRate = $order->tax_percentage ?? 10.00;
            $serviceRate = $order->service_charge_percentage ?? 5.00;
            $originalSubtotal = (float)$order->items->sum('subtotal');
            $sourceDiscount = (float)$order->discount;

            $newOrderItemsData = [];
            $itemsToMove = [];
            $partialItemUpdates = [];
            $now = now();
            $newSubtotal = 0;

            foreach ($itemsToSplitData as $splitData) {
                $itemId = $splitData['id'] ?? null;
                $splitQty = (int)($splitData['qty'] ?? 0);

                if ($splitQty <= 0) continue;

                $item = $order->items->where('id', $itemId)->first();
                if (!$item) continue;

                $splitQty = min($splitQty, (int)$item->quantity);
                $perItemSubtotal = $item->quantity > 0 ? $item->subtotal / $item->quantity : 0;
                $newItemSubtotal = max(0, $perItemSubtotal * $splitQty);
                if ($newItemSubtotal <= 0) continue;

                if ($splitQty < $item->quantity) {
                    $newOrderItemsData[] = [
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'product_name' => $item->product_name,
                        'variant_name' => $item->variant_name,
                        'quantity' => $splitQty,
                        'price' => $item->price,
                        'cost' => $item->cost,
                        'discount' => $item->discount,
                        'subtotal' => $newItemSubtotal,
                        'note' => $item->note,
                        'kitchen_status' => $item->kitchen_status,
                        'selected_variants' => $item->getRawOriginal('selected_variants'),
                        'selected_extras' => $item->getRawOriginal('selected_extras'),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    $remainQty = $item->quantity - $splitQty;
                    $partialItemUpdates[] = [
                        'item' => $item,
                        'quantity' => $remainQty,
                        'subtotal' => max(0, $perItemSubtotal * $remainQty),
                    ];
                } else {
                    $itemsToMove[] = $item;
                }

                $newSubtotal += $newItemSubtotal;
            }

            if ($newSubtotal <= 0) throw new Exception(message: 'Tidak ada item valid yang bisa dipisah.');

            do {
                $newInvoiceCode = $order->invoice_code . '-' . strtoupper(Str::random(3));
            } while (Order::where('invoice_code', $newInvoiceCode)->exists());

            $discountRatio = $originalSubtotal > 0 ? $newSubtotal / $originalSubtotal : 0;
            $splitDiscount = round(min($sourceDiscount, $sourceDiscount * $discountRatio), 2);
            $remainingDiscount = max(0, $sourceDiscount - $splitDiscount);

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

            foreach ($newOrderItemsData as &$itemData) {
                $itemData['order_id'] = $newOrder->id;
            }
            unset($itemData);

            if (!empty($newOrderItemsData)) {
                OrderItem::insert($newOrderItemsData);
            }

            foreach ($partialItemUpdates as $update) {
                $update['item']->update([
                    'quantity' => $update['quantity'],
                    'subtotal' => $update['subtotal'],
                ]);
            }

            foreach ($itemsToMove as $item) {
                $item->update(['order_id' => $newOrder->id]);
            }

            $newOrder->update($this->calculateTaxesAndTotal(
                subtotal: $newSubtotal,
                discount: $splitDiscount,
                taxRate: $taxRate,
                serviceRate: $serviceRate,
                applicationFeeAmount: 0.00
            ));

            $order->refresh();
            $oldSubtotal = $order->items->sum('subtotal');

            if ($oldSubtotal == 0 && $order->items->count() == 0) {
                $order->delete();
            } else {
                $order->update($this->calculateTaxesAndTotal(
                    subtotal: $oldSubtotal,
                    discount: $remainingDiscount,
                    taxRate: $taxRate,
                    serviceRate: $serviceRate,
                    applicationFeeAmount: (float)($order->application_fee ?? 0)
                ));
            }

            DB::commit();

            return $newOrder;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    public function mergeOrder(int $sourceOrderId, int $targetOrderId): Order
    {
        try {
            DB::beginTransaction();

            $firstId = min($sourceOrderId, $targetOrderId);
            $secondId = max($sourceOrderId, $targetOrderId);

            Order::whereIn('id', [$firstId, $secondId])->lockForUpdate()->get();

            $sourceOrder = Order::with('items')->find($sourceOrderId);
            /** @var Order $targetOrder */
            $targetOrder = Order::with('items')->find($targetOrderId);

            if (!$sourceOrder || !$targetOrder) throw new Exception(message: 'Pesanan tidak ditemukan.');

            if (
                in_array($sourceOrder->status, ['completed', 'cancelled', 'paid'], true) || in_array($targetOrder->status, ['completed', 'cancelled', 'paid'], true)
            ) throw new Exception(message: 'Tidak bisa menggabungkan pesanan yang sudah selesai, dibatalkan, atau lunas.');

            if (
                $sourceOrder->is_online !== $targetOrder->is_online
            ) throw new Exception(message: 'Tidak bisa menggabungkan Pesanan Digital dengan Pesanan Kasir Manual.');

            if (
                $sourceOrder->amount_paid > 0 || $targetOrder->amount_paid > 0
            ) throw new Exception(message: 'Tidak bisa menggabungkan pesanan yang sudah dicicil/memiliki pembayaran masuk.');

            OrderItem::where('order_id', $sourceOrderId)->update(['order_id' => $targetOrder->id]);

            $newNotes = trim(($targetOrder->notes ? $targetOrder->notes . ' | ' : '') . ($sourceOrder->notes ?? ''));
            $newCustomerName = trim(($targetOrder->customer_name ? $targetOrder->customer_name . ' & ' : '') . ($sourceOrder->customer_name ?? ''));

            $targetOrder->notes = substr($newNotes, 0, 255);
            $targetOrder->customer_name = substr($newCustomerName, 0, 100);

            $targetOrder->refresh();
            $taxRate = (float)$targetOrder->tax_percentage;
            $serviceRate = (float)$targetOrder->service_charge_percentage;

            $newSubtotal = $targetOrder->items->sum('subtotal');
            $newDiscount = (float)$targetOrder->discount + (float)$sourceOrder->discount;
            $targetOrder->update($this->calculateTaxesAndTotal(
                subtotal: $newSubtotal,
                discount: $newDiscount,
                taxRate: $taxRate,
                serviceRate: $serviceRate,
                applicationFeeAmount: max((float)($targetOrder->application_fee ?? 0), (float)($sourceOrder->application_fee ?? 0))
            ));

            $sourceOrder->delete();

            DB::commit();

            return $targetOrder;

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Calculates tax, service charge, and total price after discount.
     */
    private function calculateTaxesAndTotal(float $subtotal, float $discount, float $taxRate, float $serviceRate, float $applicationFeeAmount = 0): array
    {
        $subtotalAfterDiscount = max(0, $subtotal - $discount);
        $serviceChargeAmount = round(($serviceRate / 100) * $subtotalAfterDiscount);

        $dpp = $subtotalAfterDiscount + $serviceChargeAmount + $applicationFeeAmount;
        $taxAmount = round(($taxRate / 100) * $dpp);
        $totalPrice = $dpp + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'service_charge_amount' => $serviceChargeAmount,
            'application_fee' => $applicationFeeAmount,
            'tax_amount' => $taxAmount,
            'total_price' => $totalPrice,
            'discount' => $discount,
        ];
    }

    /**
     * Aggregates stock adjustments for bulk processing.
     */
    private function aggregateStockAdjustments(ProductVariant $variant, int $quantity, array &$variantAdjustments, array &$rawMaterialAdjustments): void
    {
        $variantAdjustments[$variant->id] = ($variantAdjustments[$variant->id] ?? 0) + $quantity;

        foreach ($variant->recipes as $recipe) {
            if ($recipe->rawMaterial) {
                $rmId = $recipe->rawMaterial->id;
                $qtyUsed = $recipe->quantity_used * $quantity;
                $rawMaterialAdjustments[$rmId] = ($rawMaterialAdjustments[$rmId] ?? 0) + $qtyUsed;
            }
        }
    }

    /**
     * Executes the aggregated stock adjustments.
     *
     * @throws Exception
     */
    private function executeStockAdjustments(array $variantAdjustments, array $rawMaterialAdjustments, string $operation = self::OPERATION_DECREMENT): void
    {
        foreach ($variantAdjustments as $id => $qty) {
            if ($operation === self::OPERATION_DECREMENT) {
                $updated = ProductVariant::where('id', $id)->where('stock', '>=', $qty)->decrement('stock', $qty);
                if ($updated !== 1) throw new Exception(message: 'Stok varian tidak mencukupi.');

                continue;
            }

            ProductVariant::where('id', $id)->increment('stock', $qty);
        }

        foreach ($rawMaterialAdjustments as $id => $qty) {
            if ($operation === self::OPERATION_DECREMENT) {
                $updated = RawMaterial::where('id', $id)->where('stock', '>=', $qty)->decrement('stock', $qty);
                if ($updated !== 1) throw new Exception(message: 'Stok bahan baku tidak mencukupi.');

                continue;
            }

            RawMaterial::where('id', $id)->increment('stock', $qty);
        }
    }
}
