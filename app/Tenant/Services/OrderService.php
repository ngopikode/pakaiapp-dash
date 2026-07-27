<?php

namespace App\Tenant\Services;

use App\Central\Services\BillingService;
use App\Tenant\Events\KitchenUpdated;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\OrderItem;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Resto\ProductExtra;
use App\Tenant\Models\Core\ProductVariant;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\SettingService;
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

    public const OPERATION_DECREMENT = 'decrement';

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
    public function processOrder(array $orderData, array $items, ?Order $existingOrder = null): Order
    {
        try {
            DB::beginTransaction();

            $storeSetting = StoreSetting::select('is_tax_active', 'tax_rate', 'is_service_charge_active', 'service_charge_rate', 'is_application_fee_passed', 'is_kitchen_active')->first();
            $isTaxActive = $orderData['is_tax_active'] ?? ($storeSetting && $storeSetting->is_tax_active);
            $taxRate = $isTaxActive ? (float) $storeSetting->tax_rate : 0.00;
            $isServiceActive = $orderData['is_service_active'] ?? ($storeSetting && $storeSetting->is_service_charge_active);
            $serviceRate = $isServiceActive ? (float) $storeSetting->service_charge_rate : 0.00;
            
            $isAppFeeActive = $orderData['is_application_fee_passed'] ?? ($storeSetting && $storeSetting->is_application_fee_passed);
            $appFeeAmount = $isAppFeeActive ? (float) $this->settingService()->get('default_trx_fee', tenant(), 300) : 0;
            
            // ponytail: default true agar tenant yang belum punya kolom (retail) tidak terdampak
            $isKitchenActive = (bool) ($storeSetting->is_kitchen_active ?? true);

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

            $dbVariants = ProductVariant::with(['recipes.rawMaterial', 'product'])
                ->whereIn('id', $allVariantIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $dbProducts = Product::with('variants')
                ->whereIn('id', $allProductIds)
                ->get()
                ->keyBy('id');

            $dbExtras = ProductExtra::whereIn('id', $allExtraIds)
                ->get()
                ->keyBy('id');

            foreach ($items as $item) {
                $productId = $item['product_id'] ?? $item['id'];
                $qty = (int) ($item['quantity'] ?? $item['qty'] ?? 1);
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
                    if (!$product) throw new Exception("Product ID $productId tidak ditemukan.");

                    $validVariantsObjects = $product->selection_type === 'multiple'
                        ? $itemVariants
                        : collect([$itemVariants->first()])->filter();

                    $originalPrice = (float) $validVariantsObjects->sum('price');
                    $discountedPrice = (float) $validVariantsObjects->sum(fn ($v) => $v->active_discount_price ?? $v->price);
                    $cost = (float) $validVariantsObjects->sum('cost');
                } else {
                    $product = $dbProducts->get($productId);
                    if (!$product) throw new Exception("Product ID $productId tidak ditemukan.");

                    $originalPrice = (float) $product->price;
                    $activeDiscountPrice = $product->variants->min('active_discount_price');
                    $discountedPrice = (float) ($activeDiscountPrice ?? $product->price);
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
                $manualDiscount = (float) ($item['itemDiscount'] ?? $item['discount'] ?? 0);
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

            if (empty($recalculatedItems)) {
                throw new Exception('Semua produk dalam pesanan tidak valid.');
            }

            if ($existingOrder) {
                $order = $existingOrder;
                $newSubtotal = $order->subtotal + $realSubtotal;

                $calculations = $this->calculateTaxesAndTotal($newSubtotal, (float) $order->discount, $taxRate, $serviceRate, (float) $order->application_fee);
                $calculations['kitchen_status'] = $isKitchenActive ? 'waiting' : 'completed';

                $order->update($calculations);
            } else {
                $globalDiscount = (float) ($orderData['global_discount'] ?? $orderData['discount'] ?? 0);
                $calculations = $this->calculateTaxesAndTotal($realSubtotal, $globalDiscount, $taxRate, $serviceRate, $appFeeAmount);

                $order = Order::create(array_merge([
                    'invoice_code' => $orderData['invoice_code'] ?? 'INV-' . strtoupper(Str::random(6)),
                    'customer_name' => $orderData['customer_name'] ?? 'Pelanggan Umum',
                    'customer_phone' => $orderData['customer_phone'] ?? null,
                    'customer_email' => $orderData['customer_email'] ?? null,
                    'table_number' => $orderData['table_number'] ?? null,
                    'notes' => $orderData['notes'] ?? null,
                    'order_type' => $orderData['order_type'] ?? 'retail',
                    'is_online' => $orderData['is_online'] ?? false,
                    'payment_method' => $orderData['payment_method'] ?? 'cash',
                    'duitku_payment_method' => $orderData['duitku_payment_method'] ?? null,
                    'tax_percentage' => $taxRate,
                    'service_charge_percentage' => $serviceRate,
                    'amount_paid' => $orderData['amount_paid'] ?? 0,
                    'change_amount' => $orderData['change_amount'] ?? 0,
                    'status' => $orderData['status'] ?? 'pending',
                    'user_id' => $orderData['user_id'] ?? Auth::id(),
                ], $calculations));
            }

            $orderItemData = [];
            $variantAdjustments = [];
            $rawMaterialAdjustments = [];
            $now = now();

            foreach ($recalculatedItems as $item) {
                $orderItemData[] = [
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'variant_name' => $item['variant_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost' => $item['cost'],
                    'discount' => $item['discount'],
                    'subtotal' => $item['subtotal'],
                    'note' => $item['note'],
                    'kitchen_status' => $item['kitchen_status'],
                    'selected_variants' => json_encode($item['selected_variants']),
                    'selected_extras' => json_encode($item['selected_extras']),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                foreach ($item['valid_variants_objects'] as $variant) {
                    $this->aggregateStockAdjustments($variant, $item['quantity'], $variantAdjustments, $rawMaterialAdjustments);
                }
            }

            if (!empty($orderItemData)) {
                OrderItem::insert($orderItemData);
            }

            $this->executeStockAdjustments($variantAdjustments, $rawMaterialAdjustments);

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
            if (!$order) throw new Exception('Pesanan tidak ditemukan.');

            if ($order->status === 'cancelled') throw new Exception('Pesanan sudah dibatalkan sebelumnya.');

            if ($order->status !== 'pending') {
                if (
                    $order->is_printed || Carbon::parse($order->created_at)->toDateString() !== today()->toDateString()
                ) throw new Exception('Pesanan yang sudah dicetak struk atau lewat hari tidak bisa dibatalkan.');
            }

            $hasProcessedItems = $order->items()->whereIn('kitchen_status', ['processing', 'ready', 'completed'])->exists();
            if ($hasProcessedItems) throw new Exception('Pesanan tidak dapat dibatalkan secara keseluruhan karena sebagian/seluruh item sudah diproses oleh dapur.');

            $variantAdjustments = [];
            $rawMaterialAdjustments = [];

            foreach ($order->items as $item) {
                if ($item->variant) $this->aggregateStockAdjustments($item->variant, $item->quantity, $variantAdjustments, $rawMaterialAdjustments);
            }

            $this->executeStockAdjustments($variantAdjustments, $rawMaterialAdjustments, self::OPERATION_INCREMENT);

            $updateData = ['status' => 'cancelled'];
            if ($note) $updateData['cancellation_note'] = $note;
            $order->update($updateData);

            if ($order->getOriginal('status') !== 'pending') $this->billingService()->processVoidPenalty($order);

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
            if (!$item) throw new Exception('Item tidak ditemukan.');

            $order = $item->order;
            if (
                !$order || in_array($order->status, ['completed', 'cancelled'])
            ) throw new Exception('Pesanan sudah selesai atau dibatalkan, tidak bisa void.');

            if (
                in_array($item->kitchen_status, ['processing', 'ready', 'completed'])
            ) throw new Exception('Item sedang/sudah diproses oleh dapur dan tidak bisa dibatalkan.');

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
                    $this->aggregateStockAdjustments($variant, $item->quantity, $variantAdjustments, $rawMaterialAdjustments);
                }
            }

            $this->executeStockAdjustments($variantAdjustments, $rawMaterialAdjustments, self::OPERATION_INCREMENT);

            $subtotalToDeduct = $item->subtotal;
            $item->delete();

            $taxRate = (float) $order->tax_percentage;
            $serviceRate = (float) $order->service_charge_percentage;

            $newSubtotal = max(0, $order->subtotal - $subtotalToDeduct);

            $order->update($this->calculateTaxesAndTotal($newSubtotal, (float) $order->discount, $taxRate, $serviceRate, (float) ($order->application_fee ?? 0)));

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

            if (!$order || !$isPayable) throw new Exception('Pesanan tidak ditemukan atau sudah dibayar penuh.');

            $baseTotal = isset($order->total_price) ? (float) $order->total_price : (float) $order->subtotal;
            $totalPrice = max(0, $baseTotal - $discount);
            $paid = (float) $amountPaid > 0 ? (float) $amountPaid : $totalPrice;

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

            event(new KitchenUpdated);
            $this->billingService()->chargeTransactionFee($order);

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
                !$order || in_array($order->status, ['completed', 'cancelled', 'paid'])
            ) throw new Exception('Pesanan yang sudah lunas/selesai tidak bisa dipisah.');

            if ($order->amount_paid > 0) throw new Exception('Pesanan yang sudah dicicil bayar tidak bisa dipisah per item.');

            if (empty($itemsToSplitData)) throw new Exception('Pilih minimal 1 item untuk dipisah.');

            $taxRate = $order->tax_percentage ?? 10.00;
            $serviceRate = $order->service_charge_percentage ?? 5.00;
            $originalSubtotal = (float) $order->items->sum('subtotal');
            $sourceDiscount = (float) $order->discount;

            $newOrderItemsData = [];
            $itemsToMove = [];
            $partialItemUpdates = [];
            $now = now();
            $newSubtotal = 0;

            foreach ($itemsToSplitData as $splitData) {
                $itemId = $splitData['id'] ?? null;
                $splitQty = (int) ($splitData['qty'] ?? 0);

                if ($splitQty <= 0) continue;

                $item = $order->items->where('id', $itemId)->first();
                if (!$item) continue;

                $splitQty = min($splitQty, (int) $item->quantity);
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

            if ($newSubtotal <= 0) throw new Exception('Tidak ada item valid yang bisa dipisah.');

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

            $newOrder->update($this->calculateTaxesAndTotal($newSubtotal, $splitDiscount, $taxRate, $serviceRate, 0.00));

            $order->refresh();
            $oldSubtotal = $order->items->sum('subtotal');

            if ($oldSubtotal == 0 && $order->items->count() == 0) {
                $order->delete();
            } else {
                $order->update($this->calculateTaxesAndTotal($oldSubtotal, $remainingDiscount, $taxRate, $serviceRate, (float) ($order->application_fee ?? 0)));
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

            $sourceOrder = Order::with('items')->lockForUpdate()->find($sourceOrderId);

            /** @var Order $targetOrder */
            $targetOrder = Order::with('items')->lockForUpdate()->find($targetOrderId);

            if (!$sourceOrder || !$targetOrder) throw new Exception('Pesanan tidak ditemukan.');

            if (
                in_array($sourceOrder->status, ['completed', 'cancelled', 'paid']) || in_array($targetOrder->status, ['completed', 'cancelled', 'paid'])
            ) throw new Exception('Tidak bisa menggabungkan pesanan yang sudah selesai, dibatalkan, atau lunas.');

            if (
                $sourceOrder->is_online !== $targetOrder->is_online
            ) throw new Exception('Tidak bisa menggabungkan Pesanan Digital dengan Pesanan Kasir Manual.');

            if (
                $sourceOrder->amount_paid > 0 || $targetOrder->amount_paid > 0
            ) throw new Exception('Tidak bisa menggabungkan pesanan yang sudah dicicil/memiliki pembayaran masuk.');

            OrderItem::where('order_id', $sourceOrderId)->update(['order_id' => $targetOrder->id]);

            $newNotes = trim(($targetOrder->notes ? $targetOrder->notes . ' | ' : '') . ($sourceOrder->notes ?? ''));
            $newCustomerName = trim(($targetOrder->customer_name ? $targetOrder->customer_name . ' & ' : '') . ($sourceOrder->customer_name ?? ''));

            $targetOrder->notes = substr($newNotes, 0, 255);
            $targetOrder->customer_name = substr($newCustomerName, 0, 100);

            $targetOrder->refresh();
            $taxRate = (float) $targetOrder->tax_percentage;
            $serviceRate = (float) $targetOrder->service_charge_percentage;

            $newSubtotal = $targetOrder->items->sum('subtotal');
            $newDiscount = (float) $targetOrder->discount + (float) $sourceOrder->discount;
            $targetOrder->update($this->calculateTaxesAndTotal(
                $newSubtotal, 
                $newDiscount, 
                $taxRate, 
                $serviceRate, 
                (float) ($targetOrder->application_fee ?? 0) + (float) ($sourceOrder->application_fee ?? 0)
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
    private function calculateTaxesAndTotal(float $subtotal, float $discount, float $taxRate, float $serviceRate, float $appFeeAmount = 0): array
    {
        $subtotalAfterDiscount = max(0, $subtotal - $discount);
        $serviceCharge = round(($serviceRate / 100) * $subtotalAfterDiscount);

        $dpp = $subtotalAfterDiscount + $serviceCharge + $appFeeAmount;
        $taxAmount = round(($taxRate / 100) * $dpp);
        $totalPrice = $dpp + $taxAmount;

        return [
            'subtotal' => $subtotal,
            'service_charge_amount' => $serviceCharge,
            'application_fee' => $appFeeAmount,
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

        if (tenant('store_type') === 'resto') {
            foreach ($variant->recipes as $recipe) {
                if ($recipe->rawMaterial) {
                    $rmId = $recipe->rawMaterial->id;
                    $qtyUsed = $recipe->quantity_used * $quantity;
                    $rawMaterialAdjustments[$rmId] = ($rawMaterialAdjustments[$rmId] ?? 0) + $qtyUsed;
                }
            }
        }
    }

    /**
     * Executes the aggregated stock adjustments.
     */
    private function executeStockAdjustments(array $variantAdjustments, array $rawMaterialAdjustments, string $operation = self::OPERATION_DECREMENT): void
    {
        foreach ($variantAdjustments as $id => $qty) {
            if ($operation === self::OPERATION_DECREMENT) {
                $updated = ProductVariant::where('id', $id)->where('stock', '>=', $qty)->decrement('stock', $qty);
                if ($updated !== 1) throw new Exception('Stok varian tidak mencukupi.');

                continue;
            }

            ProductVariant::where('id', $id)->increment('stock', $qty);
        }

        foreach ($rawMaterialAdjustments as $id => $qty) {
            if ($operation === self::OPERATION_DECREMENT) {
                $updated = RawMaterial::where('id', $id)->where('stock', '>=', $qty)->decrement('stock', $qty);
                if ($updated !== 1) throw new Exception('Stok bahan baku tidak mencukupi.');

                continue;
            }

            RawMaterial::where('id', $id)->increment('stock', $qty);
        }
    }
}
