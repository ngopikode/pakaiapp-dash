<?php

namespace App\Tenant\Services;

use App\Tenant\Events\KitchenUpdated;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\OrderItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class KitchenService
{
    /**
     * @throws Throwable
     */
    public function markAsProcessing(int $orderId): void
    {
        $this->updateOrderStatus($orderId, 'waiting', 'processing');
    }

    /**
     * @throws Throwable
     */
    public function markAsReady(int $orderId): void
    {
        $this->updateOrderStatus($orderId, 'processing', 'ready');
    }

    /**
     * @throws Throwable
     */
    public function markItemAsProcessing(int $itemId): void
    {
        $this->updateItemStatus($itemId, 'waiting', 'processing');
    }

    /**
     * @throws Throwable
     */
    public function markItemAsReady(int $itemId): void
    {
        $this->updateItemStatus($itemId, 'processing', 'ready');
    }

    /**
     * @throws Throwable
     */
    private function updateOrderStatus(int $orderId, string $fromStatus, string $toStatus): void
    {
        try {
            DB::beginTransaction();

            $order = Order::lockForUpdate()->find($orderId);
            if (!$order) throw new Exception(message: 'Pesanan tidak ditemukan.');
            if ($order->status === 'cancelled') throw new Exception(message: 'Pesanan sudah dibatalkan.');

            $order->items()->where('kitchen_status', $fromStatus)->update(['kitchen_status' => $toStatus]);
            $this->recalculateOrderStatus($order);

            event(new KitchenUpdated);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @throws Throwable
     */
    private function updateItemStatus(int $itemId, string $fromStatus, string $toStatus): void
    {
        try {
            DB::beginTransaction();

            $item = OrderItem::with('order')->lockForUpdate()->find($itemId);
            if (!$item) throw new Exception(message: 'Item tidak ditemukan.');

            $order = $item->order;
            if (!$order || $order->status === 'cancelled') throw new Exception(
                message: 'Pesanan sudah dibatalkan atau tidak ditemukan.'
            );

            if ($item->kitchen_status !== $fromStatus) throw new Exception(
                message: "Item tidak dalam status $fromStatus."
            );

            $item->update(['kitchen_status' => $toStatus]);
            $this->recalculateOrderStatus($order);

            event(new KitchenUpdated);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function recalculateOrderStatus(Order $order): void
    {
        $kitchenStatus = $this->determineKitchenStatus($order);

        $order->update([
            'kitchen_status' => $kitchenStatus,
            'status' => $this->determineOrderStatus($order, $kitchenStatus),
        ]);
    }

    private function determineKitchenStatus(Order $order): string
    {
        if ($order->items()->where('kitchen_status', 'waiting')->exists()) return 'waiting';
        if ($order->items()->where('kitchen_status', 'processing')->exists()) return 'processing';

        return 'ready';
    }

    private function determineOrderStatus(Order $order, string $kitchenStatus): string
    {
        if ($kitchenStatus !== 'ready' && in_array($order->status, ['paid', 'pending'])) return 'progress';
        if ($kitchenStatus === 'ready' && $order->amount_paid >= $order->total_price) return 'completed';
        if ($kitchenStatus === 'ready') return 'progress';

        return $order->status;
    }
}
