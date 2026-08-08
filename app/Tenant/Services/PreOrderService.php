<?php

namespace App\Tenant\Services;

use App\Tenant\Data\CreatePreOrderData;
use App\Tenant\Models\Core\DeliverySlot;
use App\Tenant\Models\Core\DeliveryZone;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\OrderItem;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\ProductVariant;
use App\Tenant\Models\Core\StoreSetting;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class PreOrderService
{
    /**
     * Tentukan tanggal pengiriman paling awal yang bisa dipilih pembeli.
     *
     * jam sekarang (WIB) < cutoff_time → HARI INI
     * jam sekarang (WIB) >= cutoff_time → BESOK
     * cutoff_time null → selalu BESOK (safe default)
     */
    public function resolveEarliestDeliveryDate(StoreSetting $setting): Carbon
    {
        if (!$setting->cutoff_time) return Carbon::today('Asia/Jakarta')->addDay();

        $now = Carbon::now('Asia/Jakarta');
        $cutoff = Carbon::parse($setting->cutoff_time, 'Asia/Jakarta')
            ->setDate($now->year, $now->month, $now->day);

        return $now->lt($cutoff)
            ? Carbon::today('Asia/Jakarta')
            : Carbon::today('Asia/Jakarta')->addDay();
    }

    /**
     * Kembalikan daftar slot aktif beserta ketersediaan kuota untuk tanggal tertentu.
     *
     * @return Collection<int, array{id: int, name: string, start_time: string, end_time: string, max_orders: int, booked: int, available: int, is_full: bool}>
     */
    public function getSlotAvailability(Carbon $date): Collection
    {
        $slots = DeliverySlot::where('is_active', true)->get();

        $bookedCounts = Order::whereDate('delivery_date', $date->toDateString())
            ->whereNotNull('delivery_slot_id')
            ->where('status', '!=', 'cancelled')
            ->selectRaw('delivery_slot_id, COUNT(*) as count')
            ->groupBy('delivery_slot_id')
            ->pluck('count', 'delivery_slot_id');

        return $slots->map(function (DeliverySlot $slot) use ($bookedCounts) {
            $booked = $bookedCounts->get($slot->id, 0);
            $available = max(0, $slot->max_orders - $booked);

            return [
                'id' => $slot->id,
                'name' => $slot->name,
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'max_orders' => $slot->max_orders,
                'booked' => $booked,
                'available' => $available,
                'is_full' => $available === 0,
            ];
        });
    }

    /**
     * Agregasi kebutuhan belanja pasar untuk tanggal pengiriman tertentu.
     *
     * @return Collection<int, array{product_name: string, variant_name: string|null, total_qty: int}>
     */
    public function getMarketRecap(Carbon $date): Collection
    {
        return collect(
            DB::select(
                'SELECT oi.product_name, oi.variant_name, SUM(oi.quantity) AS total_qty
                 FROM order_items oi
                 JOIN orders o ON o.id = oi.order_id
                 WHERE DATE(o.delivery_date) = ?
                   AND o.status != ?
                 GROUP BY oi.product_name, oi.variant_name
                 ORDER BY oi.product_name',
                [$date->toDateString(), 'cancelled']
            )
        );
    }

    /**
     * Tandai semua pesanan pending pada tanggal pengiriman tertentu sebagai paid.
     *
     * @return int Jumlah pesanan yang diselesaikan.
     *
     * @throws Throwable
     */
    public function completeAllForDate(Carbon $date): int
    {
        try {
            DB::beginTransaction();

            $count = Order::whereDate('delivery_date', $date->toDateString())
                ->where('status', 'pending')
                ->update(['status' => 'paid']);

            DB::commit();

            return $count;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Format pesan WhatsApp untuk satu pesanan pre-order.
     * Return string siap urlencode — jangan urlencode di sini.
     */
    public function buildWaMessage(Order $order): string
    {
        $order->loadMissing('items', 'deliverySlot', 'deliveryZone');

        $deliveryDate = $order->delivery_date
            ? Carbon::parse($order->delivery_date)->locale('id')->translatedFormat('l, d F Y')
            : '-';

        $itemLines = $order->items->map(
            fn (OrderItem $item) => "  - {$item->product_name}"
                . ($item->variant_name ? " ({$item->variant_name})" : '')
                . " x{$item->quantity}"
                . ' = Rp ' . number_format($item->subtotal, 0, ',', '.')
        )->join("\n");

        $paymentLabel = strtolower($order->payment_method) === 'cash' ? 'COD (Bayar ke Kurir)' : 'QRIS';

        $messageParts = [
            "🛒 *Pesanan Baru - {$order->invoice_code}*",
            '',
            "Nama     : {$order->customer_name}",
            'No. HP   : ' . ($order->customer_phone ?? '-'),
            "Alamat   : {$order->customer_address}",
            '',
        ];

        // Jika ada jadwal khusus, tampilkan (menandakan Pre-Order)
        if ($order->deliverySlot) {
            $messageParts[] = "Tgl Kirim: {$deliveryDate}";
            $messageParts[] = 'Slot     : ' . $order->deliverySlot->name;
            $messageParts[] = 'Zona     : ' . ($order->deliveryZone?->name ?? '-');
            $messageParts[] = '';
        }

        $messageParts = array_merge($messageParts, [
            '*Detail Pesanan:*',
            $itemLines,
            '',
            'Subtotal : Rp ' . number_format($order->subtotal, 0, ',', '.'),
            'Ongkir   : Rp ' . number_format($order->shipping_cost ?? 0, 0, ',', '.'),
            '*Total   : Rp ' . number_format($order->total_price, 0, ',', '.') . '*',
            '',
            "Pembayaran: {$paymentLabel}",
        ]);

        if ($order->notes) {
            $messageParts[] = "\nCatatan  : {$order->notes}";
        }

        return implode("\n", $messageParts);
    }

    /**
     * Buat pre-order baru.
     * Tidak decrement stock, tidak fire event, tidak sentuh Wallet.
     *
     * @throws Throwable
     */
    public function createPreOrder(CreatePreOrderData $data, array $items): Order
    {
        try {
            DB::beginTransaction();

            $setting = StoreSetting::cached();

            // Validasi tanggal pengiriman (hanya jika pre-order aktif)
            $deliveryDate = Carbon::parse($data->deliveryDate, 'Asia/Jakarta')->startOfDay();

            if ($setting->isPreorderActive()) {
                $earliest = $this->resolveEarliestDeliveryDate($setting);
                if ($deliveryDate->lt($earliest->startOfDay())) {
                    throw new Exception('Tanggal pengiriman tidak valid. Pilih tanggal ' . $earliest->format('d/m/Y') . ' atau setelahnya.');
                }
            }

            // Validasi slot & kuota (hanya jika pre-order aktif)
            $slot = null;
            if ($setting->isPreorderActive()) {
                $slot = DeliverySlot::lockForUpdate()->find($data->deliverySlotId);
                if (!$slot || !$slot->is_active) throw new Exception('Slot pengiriman tidak tersedia.');

                $booked = Order::whereDate('delivery_date', $data->deliveryDate)
                    ->where('delivery_slot_id', $slot->id)
                    ->where('status', '!=', 'cancelled')
                    ->count();

                if ($booked >= $slot->max_orders) throw new Exception("Slot {$slot->name} sudah penuh untuk tanggal ini.");
            }

            // Validasi zona
            $zone = null;
            if ($setting->isPreorderActive()) {
                $zone = DeliveryZone::find($data->deliveryZoneId);
                if (!$zone || !$zone->is_active) throw new Exception('Zona pengiriman tidak valid.');
            }

            // Hitung subtotal dari items
            $allProductIds = array_unique(array_column($items, 'product_id'));
            $allVariantIds = array_unique(array_filter(array_column($items, 'variant_id')));

            $dbProducts = Product::whereIn('id', $allProductIds)->where('is_active', true)->get()->keyBy('id');
            $dbVariants = !empty($allVariantIds)
                ? ProductVariant::whereIn('id', $allVariantIds)->lockForUpdate()->get()->keyBy('id')
                : collect();

            $realSubtotal = 0;
            $orderItemData = [];
            $now = now();

            foreach ($items as $item) {
                $productId = $item['product_id'];
                $variantId = $item['variant_id'] ?? null;
                $qty = max(1, (int)($item['quantity'] ?? 1));

                $product = $dbProducts->get($productId);
                if (!$product) throw new Exception("Produk ID {$productId} tidak ditemukan atau tidak aktif.");

                if ($variantId) {
                    $variant = $dbVariants->get($variantId);
                    if (!$variant) throw new Exception("Varian ID {$variantId} tidak ditemukan.");
                    $price = (float)($variant->active_discount_price ?? $variant->price);
                    $variantName = $variant->name;
                } else {
                    $price = (float)$product->price;
                    $variantName = null;
                }

                $itemSubtotal = $price * $qty;
                $realSubtotal += $itemSubtotal;

                $orderItemData[] = [
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                    'product_name' => $item['name'] ?? $product->name,
                    'variant_name' => $variantName,
                    'quantity' => $qty,
                    'price' => $price,
                    'cost' => 0,
                    'discount' => 0,
                    'subtotal' => $itemSubtotal,
                    'note' => $item['note'] ?? null,
                    'kitchen_status' => 'completed',
                    'selected_variants' => json_encode($variantId ? [$variantId] : []),
                    'selected_extras' => json_encode([]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            if (empty($orderItemData)) throw new Exception('Tidak ada item valid dalam pesanan.');

            // Hitung ongkir: gratis jika subtotal >= min_free_shipping (dan min_free_shipping > 0)
            $shippingCost = 0;
            if ($zone) {
                $shippingCost = (float)$zone->shipping_cost;
                if ((float)$zone->min_free_shipping > 0 && $realSubtotal >= (float)$zone->min_free_shipping) {
                    $shippingCost = 0;
                }
            }

            $totalPrice = $realSubtotal + $shippingCost;

            $order = Order::create([
                'invoice_code' => 'PO-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                'customer_name' => $data->customerName,
                'customer_phone' => $data->customerPhone,
                'customer_address' => $data->customerAddress,
                'order_type' => 'online',
                'is_online' => true,
                'delivery_date' => $deliveryDate->toDateString(),
                'delivery_slot_id' => $slot?->id,
                'delivery_zone_id' => $zone?->id,
                'shipping_cost' => $shippingCost,
                'payment_method' => $data->paymentMethod,
                'notes' => $data->notes,
                'subtotal' => $realSubtotal,
                'tax_amount' => 0,
                'tax_percentage' => 0,
                'service_charge_amount' => 0,
                'service_charge_percentage' => 0,
                'discount' => 0,
                'total_price' => $totalPrice,
                'amount_paid' => 0,
                'change_amount' => 0,
                'status' => 'pending',
                'kitchen_status' => 'completed',
                'user_id' => null,
            ]);

            foreach ($orderItemData as &$row) {
                $row['order_id'] = $order->id;
            }
            unset($row);

            OrderItem::insert($orderItemData);

            DB::commit();

            return $order;
        } catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
