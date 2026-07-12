<?php

namespace App\Tenant\Services;

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\OrderItem;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\ProductExtra;
use App\Tenant\Models\Core\ProductVariant;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Events\KitchenUpdated;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Memproses dan membuat Order secara tersentralisasi.
     * Mengkalkulasi Harga Asli dan Diskon dengan akurat.
     *
     * @param array $orderData
     * @param array $items
     * @param Order|null $existingOrder
     * @return Order
     * @throws Exception
     */
    public function processOrder(array $orderData, array $items, ?Order $existingOrder = null): Order
    {
        $storeSetting = StoreSetting::first();

        $isTaxActive = $orderData['is_tax_active'] ?? ($storeSetting && $storeSetting->is_tax_active);
        $taxRate = $isTaxActive ? (float)$storeSetting->tax_rate : 0.00;

        $isServiceActive = $orderData['is_service_active'] ?? ($storeSetting && $storeSetting->is_service_charge_active);
        $serviceRate = $isServiceActive ? (float)$storeSetting->service_charge_rate : 0.00;

        $recalculatedItems = [];
        $realSubtotal = 0;

        // 1. Kumpulkan semua variant IDs untuk efisiensi query
        $allVariantIds = [];
        foreach ($items as $item) {
            $vid = $item['variant_id'] ?? null;
            if ($vid) $allVariantIds[] = $vid;
            if (!empty($item['variant_ids'])) {
                $allVariantIds = array_merge($allVariantIds, $item['variant_ids']);
            }
        }
        $allVariantIds = array_unique($allVariantIds);

        $dbVariants = ProductVariant::with(['recipes.rawMaterial', 'product'])
            ->whereIn('id', $allVariantIds)
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        // 2. Kalkulasi per item
        foreach ($items as $item) {
            $productId = $item['product_id'] ?? $item['id'];
            $qty = (int)($item['quantity'] ?? $item['qty'] ?? 1);
            if ($qty <= 0) continue;

            $variantIds = $item['variant_ids'] ?? [];
            if (!empty($item['variant_id']) && !in_array($item['variant_id'], $variantIds)) {
                $variantIds[] = $item['variant_id'];
            }

            $validVariantsObjects = collect();
            $originalPrice = 0;
            $discountedPrice = 0;
            $cost = 0;
            $product = null;

            if (!empty($variantIds)) {
                $itemVariants = $dbVariants->only($variantIds);
                $product = $itemVariants->first()?->product ?? Product::find($productId);

                if (!$product) throw new Exception("Product ID $productId tidak ditemukan.");

                if ($product->selection_type === 'multiple') {
                    $validVariantsObjects = $itemVariants;
                    $originalPrice = $itemVariants->sum('price');
                    $discountedPrice = $itemVariants->sum(function ($v) {
                        return (float)($v->active_discount_price ?? $v->price);
                    });
                    $cost = $itemVariants->sum('cost');
                } else {
                    $firstVariant = $itemVariants->first();
                    if ($firstVariant) {
                        $validVariantsObjects = collect([$firstVariant]);
                        $originalPrice = (float)$firstVariant->price;
                        $discountedPrice = (float)($firstVariant->active_discount_price ?? $firstVariant->price);
                        $cost = $firstVariant->cost;
                    }
                }
            } else {
                $product = Product::find($productId);
                if (!$product) throw new Exception("Product ID $productId tidak ditemukan.");

                $originalPrice = (float)$product->price;
                $activeDiscountPrice = $product->variants->min('active_discount_price');
                $discountedPrice = (float)($activeDiscountPrice ?? $product->price);
            }

            // Hitung extra addons (jika ada)
            $extraPrice = 0;
            $validExtraIds = [];
            if (!empty($item['extra_ids'])) {
                $extras = ProductExtra::whereIn('id', $item['extra_ids'])
                    ->where('product_id', $product->id)
                    ->get();
                $extraPrice = $extras->sum('price');
                $validExtraIds = $extras->pluck('id')->toArray();
            }

            // Harga Item Asli (Tanpa Diskon)
            $itemOriginalPrice = $originalPrice + $extraPrice;

            // Harga Setelah Diskon AI
            $itemDiscountedPrice = $discountedPrice + $extraPrice;

            // Selisih AI Discount
            $aiDiscount = max(0, $itemOriginalPrice - $itemDiscountedPrice);

            // Manual Discount (dari Kasir POS)
            $manualDiscount = (float)($item['itemDiscount'] ?? $item['discount'] ?? 0);

            // Total Diskon per QTY 1
            $totalItemDiscount = $aiDiscount + $manualDiscount;

            // Pastikan subtotal tidak minus
            $itemSubtotal = max(0, ($itemOriginalPrice - $totalItemDiscount) * $qty);

            $realSubtotal += $itemSubtotal;

            $recalculatedItems[] = [
                'product_id' => $product->id,
                'variant_id' => $validVariantsObjects->first()?->id ?? null,
                'product_name' => $item['name'] ?? $item['cartName'] ?? $product->name,
                'variant_name' => $item['variant_name'] ?? $validVariantsObjects->pluck('name')->join(', '),
                'quantity' => $qty,
                'price' => $itemOriginalPrice, // HARGA ASLI SEBELUM DISKON
                'cost' => $cost,
                'discount' => $totalItemDiscount, // NILAI DISKON
                'subtotal' => $itemSubtotal,
                'note' => $item['note'] ?? null,
                'kitchen_status' => 'waiting',
                'selected_variants' => $validVariantsObjects->pluck('id')->toArray(),
                'selected_extras' => $validExtraIds,
                'valid_variants_objects' => $validVariantsObjects
            ];
        }

        if (empty($recalculatedItems)) {
            throw new Exception('Semua produk dalam pesanan tidak valid.');
        }

        // 3. Simpan Order Induk
        if ($existingOrder) {
            $order = $existingOrder;
            $newSubtotal = $order->subtotal + $realSubtotal;
            // Gunakan global discount dari order yang sudah ada
            $subtotalAfterGlobal = max(0, $newSubtotal - $order->discount);

            $newServiceCharge = round(($serviceRate / 100) * $subtotalAfterGlobal);
            $newTaxAmount = round(($taxRate / 100) * ($subtotalAfterGlobal + $newServiceCharge));
            $newTotalPrice = $subtotalAfterGlobal + $newServiceCharge + $newTaxAmount;

            $order->update([
                'subtotal' => $newSubtotal,
                'service_charge_amount' => $newServiceCharge,
                'tax_amount' => $newTaxAmount,
                'total_price' => $newTotalPrice,
                'kitchen_status' => 'waiting', // Refresh untuk dapur
            ]);
        } else {
            $globalDiscount = (float)($orderData['global_discount'] ?? $orderData['discount'] ?? 0);
            $subtotalAfterGlobal = max(0, $realSubtotal - $globalDiscount);

            $serviceChargeAmount = round(($serviceRate / 100) * $subtotalAfterGlobal);
            $taxAmount = round(($taxRate / 100) * ($subtotalAfterGlobal + $serviceChargeAmount));
            $totalPrice = $subtotalAfterGlobal + $serviceChargeAmount + $taxAmount;

            $order = Order::create([
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
                'subtotal' => $realSubtotal,
                'tax_amount' => $taxAmount,
                'service_charge_amount' => $serviceChargeAmount,
                'tax_percentage' => $taxRate,
                'service_charge_percentage' => $serviceRate,
                'discount' => $globalDiscount,
                'total_price' => $totalPrice,
                'amount_paid' => $orderData['amount_paid'] ?? 0,
                'change_amount' => $orderData['change_amount'] ?? 0,
                'status' => $orderData['status'] ?? 'pending',
                'user_id' => $orderData['user_id'] ?? Auth::id(),
            ]);
        }

        // 4. Simpan Item & Kurangi Stok
        foreach ($recalculatedItems as $item) {
            OrderItem::create([
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
            ]);

            foreach ($item['valid_variants_objects'] as $variant) {
                // Kurangi stok varian
                $variant->decrement('stock', $item['quantity']);

                // Kurangi stok bahan mentah (jika resto)
                if (tenant('store_type') === 'resto') {
                    foreach ($variant->recipes as $recipe) {
                        if ($recipe->rawMaterial) {
                            $recipe->rawMaterial->decrement('stock', $recipe->quantity_used * $item['quantity']);
                        }
                    }
                }
            }
        }

        // Beritahu dapur ada order baru/update via Reverb
        event(new KitchenUpdated());

        return $order;
    }
}
