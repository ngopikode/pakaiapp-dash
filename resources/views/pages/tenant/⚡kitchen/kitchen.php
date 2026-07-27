<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\OrderItem;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\KitchenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tampilan Dapur')]
class extends Component
{
    use ShowsToast;

    public bool $kitchenDisabled = false;

    private ?KitchenService $kitchenService = null;

    public function mount(): void
    {
        $setting = StoreSetting::first();
        if ($setting && !$setting->is_kitchen_active) {
            $this->kitchenDisabled = true;
        }
    }

    private function kitchenService(): KitchenService
    {
        return $this->kitchenService ??= app(KitchenService::class);
    }

    public function markAsProcessing(int $orderId): void
    {
        try {
            $this->kitchenService()->markAsProcessing($orderId);
            $this->toast('Pesanan mulai dimasak!');
        } catch (Throwable $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    public function markAsReady(int $orderId): void
    {
        try {
            $this->kitchenService()->markAsReady($orderId);
            $this->toast('Pesanan siap disajikan!');
        } catch (Throwable $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    public function markItemAsProcessing(int $itemId): void
    {
        try {
            $this->kitchenService()->markItemAsProcessing($itemId);
            $this->toast('Item mulai dimasak!');
        } catch (Throwable $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    public function markItemAsReady(int $itemId): void
    {
        try {
            $this->kitchenService()->markItemAsReady($itemId);
            $this->toast('Item siap disajikan!');
        } catch (Throwable $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    #[On('echo:kitchen,.KitchenUpdated')]
    public function refreshKitchen(): void
    {
        $this->dispatch('kitchen-updated', count: count($this->kitchenBatches));
    }

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login');
    }

    #[Computed]
    public function kitchenStats(): array
    {
        $today = today();

        $statusCounts = Order::selectRaw("COUNT(CASE WHEN kitchen_status = 'waiting' THEN 1 END) AS waiting, COUNT(CASE WHEN kitchen_status = 'processing' THEN 1 END) AS processing, COUNT(CASE WHEN kitchen_status = 'ready' THEN 1 END) AS ready")
            ->whereDate('created_at', $today)
            ->first();

        // ponytail: fallback static 18m if no prep data yet
        $avgPrep = OrderItem::whereDate('created_at', $today)
            ->where('kitchen_status', 'ready')
            ->whereNotNull('updated_at')
            ->whereRaw('TIMESTAMPDIFF(MINUTE, created_at, updated_at) BETWEEN 1 AND 119')
            ->avg(DB::raw('TIMESTAMPDIFF(MINUTE, created_at, updated_at)')) ?? 18;

        return [
            'active' => ($statusCounts->waiting ?? 0) + ($statusCounts->processing ?? 0),
            'avg_prep' => round($avgPrep),
            'pending' => ($statusCounts->waiting ?? 0),
            'ready' => ($statusCounts->ready ?? 0),
        ];
    }

    #[Computed]
    public function kitchenBatches(): array
    {
        $orders = Order::with(['items' => fn ($q) => $q->select('id', 'order_id', 'product_name', 'variant_name', 'note', 'quantity', 'kitchen_status', 'created_at')])
            ->select('id', 'invoice_code', 'status', 'kitchen_status', 'order_type', 'table_number', 'notes', 'amount_paid', 'total_price', 'created_at', 'updated_at', 'is_online')
            ->where(fn ($query) => $query->whereIn('status', ['paid', 'progress'])
                ->orWhere(fn ($q) => $q->where('status', 'pending')->where('is_online', false)))
            ->whereIn('kitchen_status', ['waiting', 'processing', 'ready'])
            ->whereDate('created_at', today())
            ->get();

        $batches = [];
        $readyCutoff = now()->subMinutes(15);

        foreach ($orders as $order) {
            foreach (['waiting', 'processing'] as $status) {
                $items = $order->items->where('kitchen_status', $status);
                if ($items->isNotEmpty()) {
                    $batches[] = [
                        'order' => $order,
                        'status' => $status,
                        'items' => $items,
                        'created_at' => $items->max('created_at'),
                    ];
                }
            }

            $readyItems = $order->items->where('kitchen_status', 'ready');
            if ($readyItems->isNotEmpty() && $order->updated_at?->gte($readyCutoff)) {
                $batches[] = [
                    'order' => $order,
                    'status' => 'ready',
                    'items' => $readyItems,
                    'created_at' => $readyItems->max('created_at'),
                ];
            }
        }

        usort($batches, fn ($a, $b) => $a['created_at'] <=> $b['created_at']);

        return $batches;
    }
};
