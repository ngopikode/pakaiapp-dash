<?php

use App\Central\Services\BillingService;
use App\Tenant\Data\CheckoutData;
use App\Tenant\Data\CreateOrderData;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\OrderService;
use App\Tenant\Services\PaymentGatewayService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public string $activeTab = 'cashier';
    public ?int $addToOrder = null;
    public ?Order $existingOrder = null;
    public string $queueFilter = 'all';
    public string $queueSearch = '';

    protected ?OrderService $orderService = null;
    protected ?PaymentGatewayService $paymentGatewayService = null;
    protected ?BillingService $billingService = null;

    protected function orderService(): OrderService
    {
        return $this->orderService ??= app(OrderService::class);
    }

    protected function paymentGatewayService(): PaymentGatewayService
    {
        return $this->paymentGatewayService ??= app(PaymentGatewayService::class);
    }

    protected function billingService(): BillingService
    {
        return $this->billingService ??= app(BillingService::class);
    }

    private function isOrderEditable(Order $order): bool
    {
        return $order->status === 'pending' ||
            ($order->status === 'progress' && $order->amount_paid < $order->total_price);
    }

    public function changeTab($tab): void
    {
        $this->activeTab = $tab;
    }

    public function mount(?int $addToOrder = null): void
    {
        $this->addToOrder = $addToOrder;
        if ($this->addToOrder) {
            $this->existingOrder = Order::find($this->addToOrder);
            if ($this->existingOrder && !$this->isOrderEditable($this->existingOrder)) {
                $this->addToOrder = null;
                $this->existingOrder = null;
            }
        }
    }

    public function setEditOrder($orderId): void
    {
        $this->addToOrder = $orderId;
        $this->existingOrder = Order::find($orderId);

        if ($this->existingOrder && !$this->isOrderEditable($this->existingOrder)) {
            $this->addToOrder = null;
            $this->existingOrder = null;
            $this->js("window.showIslandToast('Pesanan tidak dapat diedit.', 'danger');");
            return;
        }

        $this->activeTab = 'cashier';

        $detailJson = json_encode([
            'invoice_code' => $this->existingOrder->invoice_code ?? '',
            'customer' => $this->existingOrder->customer_name ?? '',
            'table' => $this->existingOrder->table_number ?? $this->existingOrder->notes ?? '',
            'type' => $this->existingOrder->order_type ?? ''
        ], JSON_THROW_ON_ERROR);

        $this->js("window.dispatchEvent(new CustomEvent('start-editing-order', { detail: $detailJson }));");
    }

    public function cancelEditOrder(): void
    {
        $this->addToOrder = null;
        $this->existingOrder = null;
    }

    public function voidItem($orderItemId): void
    {
        try {
            $order = $this->orderService()->voidItem($orderItemId);

            if (
                $this->existingOrder && $this->existingOrder->id === $order->id
            ) $this->existingOrder->refresh();

            $this->js("window.showIslandToast('Item berhasil dibatalkan dan stok dikembalikan.', 'success');");
        } catch (Exception $e) {
            $errorMsg = json_encode('Gagal membatalkan item: ' . $e->getMessage(), JSON_THROW_ON_ERROR);
            $this->js("window.showIslandToast($errorMsg, 'danger');");
        }
    }

    public function createOrder($cart, $customerName, $tableNumber, $orderType, $isTaxActive = true, $isServiceActive = true): array
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        try {
            $dto = new CreateOrderData(
                customerName: $customerName ?: 'Pelanggan Umum',
                tableNumber: $tableNumber,
                orderType: $orderType,
                isTaxActive: $isTaxActive,
                isServiceActive: $isServiceActive,
            );

            $orderData = [
                'table_number' => $dto->orderType === 'dinein' ? $dto->tableNumber : null,
                'notes' => $dto->orderType !== 'dinein' ? $dto->tableNumber : null,
                'customer_name' => $dto->customerName,
                'order_type' => $dto->orderType,
                'payment_method' => 'cash',
                'status' => 'pending',
                'is_tax_active' => $dto->isTaxActive,
                'is_service_active' => $dto->isServiceActive,
            ];

            if (
                $this->existingOrder && !$this->isOrderEditable($this->existingOrder)
            ) throw new Exception("Pesanan sudah selesai, lunas, atau dibatalkan. Tidak bisa menambah menu.");

            $order = $this->orderService()->processOrder($orderData, $cart, $this->existingOrder);

            return ['success' => true, 'invoice_code' => $order->invoice_code];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processDirectCheckout($cart, $customerName, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid, $isTaxActive = true, $isServiceActive = true): array
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        try {
            $dto = new CheckoutData(
                customerName: $customerName ?: 'Pelanggan Umum',
                tableNumber: $tableNumber,
                orderType: $orderType,
                paymentMethod: $paymentMethod,
                discount: (float)$discount,
                amountPaid: (float)$amountPaid,
                isTaxActive: $isTaxActive,
                isServiceActive: $isServiceActive,
            );

            $orderData = [
                'table_number' => $dto->orderType === 'dinein' ? $dto->tableNumber : null,
                'notes' => $dto->orderType !== 'dinein' ? $dto->tableNumber : null,
                'customer_name' => $dto->customerName,
                'order_type' => $dto->orderType,
                'payment_method' => $dto->paymentMethod,
                'global_discount' => $dto->discount,
                'status' => 'paid',
                'is_tax_active' => $dto->isTaxActive,
                'is_service_active' => $dto->isServiceActive,
            ];

            $order = $this->orderService()->processOrder($orderData, $cart, $this->existingOrder);

            // Directly charge and process payment completion since this is direct checkout
            $totalPrice = $order->total_price;
            $paid = $dto->amountPaid ?: $totalPrice;
            $change = max(0, $paid - $totalPrice);

            $order->update([
                'amount_paid' => $paid,
                'change_amount' => $change,
                'payment_method' => $dto->paymentMethod,
                'status' => 'paid',
            ]);

            $this->billingService()->chargeTransactionFee($order);
            $storeName = StoreSetting::first()?->name ?? 'Resto Kami';

            return [
                'success' => true,
                'invoice_code' => $order->invoice_code,
                'customer_name' => $dto->customerName,
                'table_number' => $dto->tableNumber,
                'store_name' => $storeName,
                'total_price' => $totalPrice,
                'discount' => $dto->discount,
                'amount_paid' => $paid,
                'change_amount' => $change,
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processPayment($orderId, $paymentMethod, $discount, $amountPaid): array
    {
        try {
            $order = $this->orderService()->processPayment($orderId, $paymentMethod, (float)$discount, (float)$amountPaid);

            $storeName = StoreSetting::first()?->name ?? 'Resto Kami';

            return [
                'success' => true,
                'invoice_code' => $order->invoice_code,
                'customer_name' => $order->customer_name,
                'customer_phone' => $order->customer_phone,
                'store_name' => $storeName,
                'total_price' => $order->total_price,
            ];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function generateDuitkuPayment($orderId, $paymentMethod, $customerEmail): array
    {
        try {
            $result = $this->paymentGatewayService()->generateDuitku($orderId, $paymentMethod, $customerEmail);
            return array_merge(['success' => true], $result);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function generateMidtransPayment($orderId, $customerEmail): array
    {
        try {
            $result = $this->paymentGatewayService()->generateMidtrans($orderId, $customerEmail);
            return array_merge(['success' => true], $result);
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    #[On('cancel-confirmed')]
    public function cancelOrder($orderId, $note = null): void
    {
        if (is_array($orderId)) {
            $note = $orderId['note'] ?? $note;
            $orderId = $orderId['orderId'] ?? null;
        }

        try {
            $this->orderService()->cancelOrder($orderId, $note);

            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $this->js("window.showIslandToast('Pesanan berhasil dibatalkan.', 'success');");
            $this->js("\$wire.\$island('queue').\$refresh();");
        } catch (Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $errorMsg = json_encode($e->getMessage(), JSON_THROW_ON_ERROR);
            $this->js("window.showIslandToast($errorMsg, 'danger');");
        }
    }

    public function splitOrder($orderId, $itemsToSplitData): void
    {
        try {
            $newOrder = $this->orderService()->splitOrder($orderId, $itemsToSplitData);

            $this->js("window.showIslandToast('Pesanan #$newOrder->invoice_code berhasil disimpan.', 'success');");
            $this->js("\$wire.\$island('queue').\$refresh();");

            $orderData = json_encode([
                'id' => $newOrder->id,
                'invoice_code' => $newOrder->invoice_code,
                'customer_name' => $newOrder->customer_name,
                'subtotal' => $newOrder->subtotal,
                'total_price' => $newOrder->total_price,
            ]);
            $this->js("window.dispatchEvent(new CustomEvent('open-payment-modal', { detail: $orderData }));");
        } catch (Exception $e) {
            $errorMsg = json_encode('Gagal memisah pesanan: ' . $e->getMessage(), JSON_THROW_ON_ERROR);
            $this->js("window.showIslandToast($errorMsg, 'danger');");
        }
    }

    public function mergeOrder($sourceOrderId, $targetOrderId): void
    {
        if ($sourceOrderId == $targetOrderId) {
            $this->js("window.showIslandToast('Pilih pesanan yang berbeda untuk digabungkan.', 'warning');");
            return;
        }

        try {
            $this->orderService()->mergeOrder($sourceOrderId, $targetOrderId);

            $this->js("window.showIslandToast('Pesanan berhasil digabungkan.', 'success');");
            $this->js("\$wire.\$island('queue').\$refresh();");

            if (
                $this->existingOrder && $this->existingOrder->id == $targetOrderId
            ) $this->existingOrder->refresh();
        } catch (Exception $e) {
            $errorMsg = json_encode('Gagal menggabungkan pesanan: ' . $e->getMessage(), JSON_THROW_ON_ERROR);
            $this->js("window.showIslandToast($errorMsg, 'danger');");
        }
    }

    public function updateCustomerPhone($invoiceCode, $phone): void
    {
        Order::where('invoice_code', $invoiceCode)->update(['customer_phone' => $phone]);
    }

    #[On('echo:kitchen,.KitchenUpdated')]
    public function onKitchenUpdated(): void
    {
        $this->js("\$wire.\$island('queue').\$refresh();");
    }

    #[Computed]
    public function activeOrders(): Collection|array
    {
        return Order::select([
            'id', 'invoice_code', 'customer_name', 'table_number', 'notes',
            'order_type', 'created_at', 'total_price', 'subtotal', 'amount_paid',
            'payment_method', 'status', 'kitchen_status', 'tax_amount',
            'service_charge_amount', 'discount', 'updated_at'
        ])
            ->with(['items' => function ($query) {
                $query->select([
                    'id', 'order_id', 'product_name', 'variant_name',
                    'quantity', 'subtotal', 'note', 'kitchen_status'
                ]);
            }])
            ->where('created_at', '>=', now()->subHours(24))
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) {
                $query->where('status', '!=', 'completed')
                    ->orWhere('updated_at', '>=', now()->subHours(2));
            })
            ->orderByDesc('created_at')
            ->get();
    }

    private function mapOrderForQueue(Order $order): Order
    {
        $kStatus = $order->status === 'completed' ? 'completed' : ($order->kitchen_status ?: 'waiting');

        $statusConfig = match ($kStatus) {
            'waiting' => ['label' => 'Pesanan Masuk', 'icon' => 'ph-clock', 'bg' => 'bg-amber-100', 'color' => 'text-amber-700', 'dot' => 'bg-amber-500'],
            'processing' => ['label' => 'Diproses Dapur', 'icon' => 'ph-cooking-pot', 'bg' => 'bg-blue-100', 'color' => 'text-blue-700', 'dot' => 'bg-blue-500'],
            'ready' => ['label' => 'Siap Disajikan', 'icon' => 'ph-bell-ringing', 'bg' => 'bg-green-100', 'color' => 'text-green-700', 'dot' => 'bg-green-500'],
            'completed' => ['label' => 'Selesai', 'icon' => 'ph-check-circle', 'bg' => 'bg-emerald-100', 'color' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
            default => ['label' => 'Unknown', 'icon' => 'ph-question', 'bg' => 'bg-gray-100', 'color' => 'text-gray-700', 'dot' => 'bg-gray-500'],
        };

        $orderTypeConfig = match ($order->order_type) {
            'dinein' => ['label' => 'Dine In', 'icon' => 'ph-fork-knife', 'color' => 'text-indigo-600 bg-indigo-50'],
            'takeaway' => ['label' => 'Takeaway', 'icon' => 'ph-bag', 'color' => 'text-orange-600 bg-orange-50'],
            'delivery' => ['label' => 'Delivery', 'icon' => 'ph-moped', 'color' => 'text-sky-600 bg-sky-50'],
            default => ['label' => 'Retail', 'icon' => 'ph-storefront', 'color' => 'text-gray-600 bg-gray-50'],
        };

        $order->statusConfig = $statusConfig;
        $order->kStatus = $kStatus;
        $order->kStatusLabel = $statusConfig['label'];
        $order->kStatusIcon = $statusConfig['icon'];
        $order->kStatusColor = $statusConfig['bg'] . ' ' . $statusConfig['color'];
        $order->kStatusDot = $statusConfig['dot'];

        $order->typeLabel = $orderTypeConfig['label'];
        $order->typeIcon = $orderTypeConfig['icon'];
        $order->typeColor = $orderTypeConfig['color'];

        $order->orderData = $this->buildOrderViewData($order, $statusConfig);

        return $order;
    }

    private function buildOrderViewData(Order $order, array $statusConfig): array
    {
        return [
            'id' => $order->id,
            'invoice_code' => $order->invoice_code,
            'customer_name' => $order->customer_name,
            'table_number' => $order->table_number,
            'notes' => $order->notes,
            'kStatus' => $order->kStatus,
            'kStatusLabel' => $order->kStatusLabel,
            'kStatusColor' => $order->kStatusColor,
            'typeLabel' => $order->typeLabel,
            'typeIcon' => $order->typeIcon,
            'typeColor' => $order->typeColor,
            'created_at' => $order->created_at->format('H:i'),
            'created_at_human' => $order->created_at->diffForHumans(),
            'total_price' => $order->total_price,
            'subtotal' => $order->subtotal,
            'amount_paid' => $order->amount_paid,
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'kitchen_status' => $order->kitchen_status,
            'can_cancel' => $order->status !== 'completed' && $order->status !== 'cancelled',
            'status_config' => $statusConfig,
            'tax_amount' => $order->tax_amount,
            'service_charge_amount' => $order->service_charge_amount,
            'discount' => $order->discount,
            'items' => $order->items->map(fn($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'note' => $item->note,
                'kitchen_status' => $item->kitchen_status,
            ])
        ];
    }

    public function setQueueFilter(string $filter): void
    {
        $this->queueFilter = $filter;
    }

    public function with(): array
    {
        $storeSetting = StoreSetting::select([
            'is_dinein_active', 'is_takeaway_active', 'is_delivery_active',
            'is_tax_active', 'tax_rate', 'is_service_charge_active', 'service_charge_rate', 'name'
        ])->first();
        $orderTypes = [];

        if ($storeSetting?->is_dinein_active) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];
        if ($storeSetting?->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        if ($storeSetting?->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
        if (empty($orderTypes)) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];

        $activeOrders = $this->activeOrders;

        $queueOrders = $activeOrders->map(fn($order) => $this->mapOrderForQueue($order));

        $kStatusCounts = $queueOrders->countBy('kStatus');

        // Filter queueOrders based on Livewire state for rendering
        $filteredQueueOrders = $queueOrders
            ->when($this->queueFilter !== 'all', fn($collection) => $collection->where('kStatus', $this->queueFilter))
            ->when($this->queueSearch !== '', fn($collection) => $collection->filter(function ($order) {
                $search = strtolower($this->queueSearch);
                return str_contains(strtolower($order->invoice_code), $search)
                    || str_contains(strtolower($order->customer_name), $search)
                    || str_contains(strtolower($order->table_number ?? $order->notes), $search);
            }));

        $counts = [
            'all' => $queueOrders->count(),
            'waiting' => $kStatusCounts->get('waiting', 0),
            'processing' => $kStatusCounts->get('processing', 0),
            'ready' => $kStatusCounts->get('ready', 0),
            'completed' => $kStatusCounts->get('completed', 0),
        ];

        $filters = [
            ['id' => 'all', 'label' => 'All'],
            ['id' => 'waiting', 'label' => 'New'],
            ['id' => 'processing', 'label' => 'Proses'],
            ['id' => 'ready', 'label' => 'Siap'],
            ['id' => 'completed', 'label' => 'Selesai'],
        ];

        return [
            'activeTab' => $this->activeTab,
            'restoOrderTypes' => $orderTypes,
            'isTaxActive' => $storeSetting?->is_tax_active ?? true,
            'taxRate' => (float)($storeSetting?->tax_rate ?? 10.00),
            'isServiceChargeActive' => $storeSetting?->is_service_charge_active ?? true,
            'serviceChargeRate' => (float)($storeSetting?->service_charge_rate ?? 5.00),
            'counts' => $counts,
            'filters' => $filters,
            'queueOrders' => $filteredQueueOrders, // Use filtered orders for the view
        ];
    }
};
