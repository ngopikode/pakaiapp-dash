<?php

namespace App\Tenant\Services;

use App\Tenant\Data\DeliverySlotData;
use App\Tenant\Data\DeliveryZoneData;
use App\Tenant\Models\Core\DeliverySlot;
use App\Tenant\Models\Core\DeliveryZone;
use App\Tenant\Models\Core\Order;
use Exception;

class DeliverySettingService
{
    /**
     * Save (Create/Update) Delivery Zone
     */
    public function saveZone(DeliveryZoneData $data, ?DeliveryZone $zone = null): DeliveryZone
    {
        $zone ??= new DeliveryZone;
        $zone->fill([
            'name' => $data->name,
            'shipping_cost' => $data->shippingCost,
            'min_free_shipping' => $data->minFreeShipping,
            'is_active' => $data->isActive,
        ])->save();

        return $zone;
    }

    /**
     * Delete or Deactivate Delivery Zone
     *
     * @throws Exception
     */
    public function deleteZone(DeliveryZone $zone): void
    {
        // Cek apakah zona dipakai di pesanan yang belum selesai
        $activeOrders = Order::where('delivery_zone_id', $zone->id)
            ->whereIn('status', ['pending', 'progress'])
            ->exists();

        if ($activeOrders) {
            throw new Exception('Gagal menghapus: Zona ini sedang digunakan oleh pesanan yang belum selesai.');
        }

        // Cek apakah dipakai di pesanan riwayat (paid/cancelled)
        $hasHistory = Order::where('delivery_zone_id', $zone->id)->exists();

        if ($hasHistory) {
            // Soft approach: just deactivate to keep history intact
            $zone->update(['is_active' => false]);

            return;
        }

        $zone->delete();
    }

    /**
     * Save (Create/Update) Delivery Slot
     */
    public function saveSlot(DeliverySlotData $data, ?DeliverySlot $slot = null): DeliverySlot
    {
        // Validasi start_time < end_time bisa dilakukan di form,
        // tapi service ini menerima DTO yang sudah divalidasi.
        $slot ??= new DeliverySlot;
        $slot->fill([
            'name' => $data->name,
            'start_time' => $data->startTime,
            'end_time' => $data->endTime,
            'max_orders' => $data->maxOrders,
            'is_active' => $data->isActive,
        ])->save();

        return $slot;
    }

    /**
     * Delete or Deactivate Delivery Slot
     *
     * @throws Exception
     */
    public function deleteSlot(DeliverySlot $slot): void
    {
        // Cek apakah slot dipakai di pesanan yang belum selesai
        $activeOrders = Order::where('delivery_slot_id', $slot->id)
            ->whereIn('status', ['pending', 'progress'])
            ->exists();

        if ($activeOrders) {
            throw new Exception('Gagal menghapus: Slot ini sedang digunakan oleh pesanan yang belum selesai.');
        }

        // Cek apakah dipakai di pesanan riwayat
        $hasHistory = Order::where('delivery_slot_id', $slot->id)->exists();

        if ($hasHistory) {
            $slot->update(['is_active' => false]);

            return;
        }

        $slot->delete();
    }
}
