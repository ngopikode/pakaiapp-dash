<?php

namespace App\Console\Commands;

use App\Central\Models\Tenant;
use App\Tenant\Events\KitchenUpdated;
use App\Tenant\Models\Core\Order;
use App\Tenant\Services\OrderService;
use Illuminate\Console\Command;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class CancelExpiredOrders extends Command
{
    protected $signature = 'orders:cancel-expired';

    protected $description = 'Cancel pending online orders that have expired (30 min for digital, 2 hours for manual)';

    /**
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        $tenants = Tenant::where('is_active', true)->get();
        $totalCancelled = 0;

        foreach ($tenants as $tenant) {
            tenancy()->initialize($tenant);

            $expiredDigital = Order::where('status', 'pending')
                ->where('is_online', true)
                ->whereNotIn('payment_method', ['cash', 'manual'])
                ->where('created_at', '<', now()->subMinutes(30))
                ->get();

            $expiredManual = Order::where('status', 'pending')
                ->where('is_online', true)
                ->whereIn('payment_method', ['cash', 'manual', null])
                ->where('created_at', '<', now()->subHours(2))
                ->get();

            $expired = $expiredDigital->merge($expiredManual);

            if ($expired->isEmpty()) {
                tenancy()->end();
                continue;
            }

            $orderService = app(OrderService::class);

            foreach ($expired as $order) {
                try {
                    $orderService->cancelOrder(
                        $order->id,
                        'Dibatalkan otomatis oleh sistem (waktu pembayaran habis).'
                    );
                    $totalCancelled++;
                    $this->info("  [{$tenant->id}] Cancelled: {$order->invoice_code}");
                } catch (\Exception $e) {
                    $this->warn("  [{$tenant->id}] Failed: {$order->invoice_code} — {$e->getMessage()}");
                }
            }

            try {
                event(new KitchenUpdated());
            } catch (\Exception $e) {
                // Broadcast failed, not critical
            }

            tenancy()->end();
        }

        $this->info("Done. Total cancelled: {$totalCancelled}");

        return Command::SUCCESS;
    }
}
