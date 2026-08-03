<?php

use App\Central\Services\BillingService;
use App\Shared\Traits\ShowsToast;
use App\Tenant\Data\ProcessOrderData;
use App\Tenant\Data\ShiftClosingData;
use App\Tenant\Data\ShiftExpenseData;
use App\Tenant\Data\ShiftOpnameItemData;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\Shift;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\OrderService;
use App\Tenant\Services\PaymentGatewayService;
use App\Tenant\Services\ShiftService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use ShowsToast;

    public string $activeTab = 'cashier';

    public ?int $addToOrder = null;

    public ?Order $existingOrder = null;

    public string $queueFilter = 'waiting';

    public string $queueSearch = '';

    public float $startingCash = 0;

    public float $expenseAmount = 0;

    public string $expenseDescription = '';

    public array $opnameItems = [];

    public float $actualCash = 0;

    public int $closeShiftStep = 1;

    protected ?OrderService $orderService = null;

    protected ?PaymentGatewayService $paymentGatewayService = null;

    protected ?BillingService $billingService = null;

    protected ?ShiftService $shiftService = null;

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

    protected function shiftService(): ShiftService
    {
        return $this->shiftService ??= app(ShiftService::class);
    }

    private function isOrderEditable(Order $order): bool
    {
        return $order->status === 'pending' ||
            ($order->status === 'progress' && $order->amount_paid < $order->total_price);
    }

    /** @return array{success: false, error: string}|null */
    private function validateCart(array $cart): ?array
    {
        foreach ($cart as $item) {
            if (!isset($item['variant_id'], $item['quantity']) || !is_int($item['variant_id']) || !is_numeric($item['quantity']) || $item['quantity'] < 1)
                return ['success' => false, 'error' => 'Data keranjang tidak valid.'];
        }

        return null;
    }

    private function clearIfExistingOrderIsNotEditable(): void
    {
        if ($this->existingOrder && !$this->isOrderEditable($this->existingOrder)) {
            $this->addToOrder = null;
            $this->existingOrder = null;
        }
    }

    public function changeTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function mount(?int $addToOrder = null): void
    {
        $this->addToOrder = $addToOrder;
        if ($this->addToOrder) $this->existingOrder = Order::find($this->addToOrder);
        $this->clearIfExistingOrderIsNotEditable();
    }

    public function setEditOrder($orderId): void
    {
        $this->addToOrder = $orderId;
        $this->existingOrder = Order::find($orderId);

        if ($this->existingOrder && !$this->isOrderEditable($this->existingOrder)) {
            $this->addToOrder = null;
            $this->existingOrder = null;
            $this->toast('Pesanan tidak dapat diedit.', 'danger');

            return;
        }

        $this->activeTab = 'cashier';

        $detailJson = json_encode([
            'invoice_code' => $this->existingOrder->invoice_code ?? '',
            'customer' => $this->existingOrder->customer_name ?? '',
            'table' => $this->existingOrder->table_number ?? $this->existingOrder->notes ?? '',
            'type' => $this->existingOrder->order_type ?? '',
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

            $this->toast('Item berhasil dibatalkan dan stok dikembalikan.');
            $this->js("\$wire.\$island('queue').\$refresh();");
        } catch (Exception $e) {
            $this->toast('Gagal membatalkan item: ' . $e->getMessage(), 'danger');
        }
    }

    public function createOrder($cart, $customerName, $tableNumber, $orderType, $isTaxActive = true, $isServiceActive = true): array
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        // Security: validate cart structure from JS to prevent mass assignment / fraud
        if ($error = $this->validateCart($cart)) return $error;

        try {
            $dto = new ProcessOrderData(
                customerName: $customerName ?: 'Pelanggan Umum',
                orderType: $orderType,
                paymentMethod: 'cash',
                status: 'pending',
                tableNumber: $orderType === 'dinein' ? $tableNumber : null,
                notes: $orderType !== 'dinein' ? $tableNumber : null,
                isTaxActive: $isTaxActive,
                isServiceActive: $isServiceActive,
            );

            if (
                $this->existingOrder && !$this->isOrderEditable($this->existingOrder)
            ) throw new Exception(message: 'Pesanan sudah selesai, lunas, atau dibatalkan. Tidak bisa menambah menu.');

            $order = $this->orderService()->processOrder($dto, $cart, $this->existingOrder);

            return ['success' => true, 'invoice_code' => $order->invoice_code];
        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function processDirectCheckout($cart, $customerName, $tableNumber, $orderType, $paymentMethod, $discount, $amountPaid, $isTaxActive = true, $isServiceActive = true, $duitkuMethod = null, $customerEmail = null): array
    {
        if (empty($cart)) return ['success' => false, 'error' => 'Keranjang kosong.'];

        // Security: validate cart structure from JS to prevent mass assignment / fraud
        if ($error = $this->validateCart($cart)) return $error;

        // Security: whitelist payment methods
        if (!in_array($paymentMethod, ['cash', 'transfer', 'digital', 'duitku'])) {
            return ['success' => false, 'error' => 'Metode pembayaran tidak valid.'];
        }

        try {
            $isDuitku = $paymentMethod === 'duitku';
            $isMidtrans = $paymentMethod === 'digital';

            $dto = new ProcessOrderData(
                customerName: $customerName ?: 'Pelanggan Umum',
                orderType: $orderType,
                paymentMethod: $isMidtrans ? 'transfer' : ($isDuitku ? $paymentMethod : $paymentMethod),
                status: ($isDuitku || $isMidtrans) ? 'pending' : 'paid',
                tableNumber: $orderType === 'dinein' ? $tableNumber : null,
                notes: $orderType !== 'dinein' ? $tableNumber : null,
                duitkuPaymentMethod: $duitkuMethod,
                customerEmail: $customerEmail,
                globalDiscount: (float)$discount,
                amountPaid: (float)$amountPaid,
                isTaxActive: $isTaxActive,
                isServiceActive: $isServiceActive,
            );

            $order = $this->orderService()->processOrder($dto, $cart, $this->existingOrder);

            if ($isDuitku) {
                if (!$duitkuMethod) throw new Exception(message: 'Metode pembayaran Duitku belum dipilih.');

                $result = $this->paymentGatewayService()->generateDuitku(
                    $order->id, $duitkuMethod, $customerEmail
                );

                return [
                    'success' => true,
                    'invoice_code' => $order->invoice_code,
                    'payment_url' => $result['payment_url'],
                ];
            }

            if ($isMidtrans) {
                $result = $this->paymentGatewayService()->generateMidtrans(
                    $order->id, $customerEmail
                );

                return [
                    'success' => true,
                    'invoice_code' => $order->invoice_code,
                    'snap_token' => $result['snap_token'],
                ];
            }

            // Manual cash/transfer
            $totalPrice = $order->total_price;
            $paid = $dto->amountPaid > 0 ? $dto->amountPaid : $totalPrice;
            if ($paid < $totalPrice) throw new Exception(message: 'Nominal pembayaran kurang dari total tagihan.');
            $change = max(0, $paid - $totalPrice);

            $order->update([
                'amount_paid' => $paid,
                'change_amount' => $change,
                'payment_method' => $dto->paymentMethod,
                'status' => 'paid',
            ]);

            $this->billingService()->chargeTransactionFee($order);
            $storeName = $this->storeSetting()?->name ?? 'Resto Kami';

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

    public function processPayment($orderId, $paymentMethod, $discount, $amountPaid, $duitkuMethod = null, $customerEmail = null): array
    {
        // Security: validate orderId is integer, whitelist payment methods
        if (!is_int($orderId) || $orderId < 1) return ['success' => false, 'error' => 'Pesanan tidak valid.'];
        if (!in_array($paymentMethod, ['cash', 'transfer', 'digital', 'duitku'])) {
            return ['success' => false, 'error' => 'Metode pembayaran tidak valid.'];
        }

        try {
            $isDuitku = $paymentMethod === 'duitku';
            $isMidtrans = $paymentMethod === 'digital';

            if ($isDuitku) {
                if (!$duitkuMethod) throw new Exception(message: 'Metode pembayaran Duitku belum dipilih.');

                $result = $this->paymentGatewayService()->generateDuitku(
                    $orderId, $duitkuMethod, $customerEmail
                );

                return [
                    'success' => true,
                    'payment_url' => $result['payment_url'],
                ];
            }

            if ($isMidtrans) {
                $result = $this->paymentGatewayService()->generateMidtrans(
                    $orderId, $customerEmail
                );

                return [
                    'success' => true,
                    'snap_token' => $result['snap_token'],
                ];
            }

            $order = $this->orderService()->processPayment($orderId, $paymentMethod, (float)$discount, (float)$amountPaid);

            $storeName = $this->storeSetting()?->name ?? 'Resto Kami';

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
            $this->toast('Pesanan berhasil dibatalkan.');
            $this->js("\$wire.\$island('queue').\$refresh();");
        } catch (Exception $e) {
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            $this->toast($e->getMessage(), 'danger');
        }
    }

    public function splitOrder($orderId, $itemsToSplitData): void
    {
        try {
            $newOrder = $this->orderService()->splitOrder($orderId, $itemsToSplitData);

            $this->toast("Pesanan #$newOrder->invoice_code berhasil disimpan.");
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
            $this->toast('Gagal memisah pesanan: ' . $e->getMessage(), 'danger');
        }
    }

    public function mergeOrder($sourceOrderId, $targetOrderId): void
    {
        if ($sourceOrderId == $targetOrderId) {
            $this->toast('Pilih pesanan yang berbeda untuk digabungkan.', 'warning');

            return;
        }

        try {
            $this->orderService()->mergeOrder($sourceOrderId, $targetOrderId);

            $this->toast('Pesanan berhasil digabungkan.');
            $this->js("\$wire.\$island('queue').\$refresh();");

            if (
                $this->existingOrder && $this->existingOrder->id == $targetOrderId
            ) $this->existingOrder->refresh();
        } catch (Exception $e) {
            $this->toast('Gagal menggabungkan pesanan: ' . $e->getMessage(), 'danger');
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
    public function activeShift(): ?Shift
    {
        return Shift::where('user_id', auth()->id())
            ->where('status', Shift::STATUS_ACTIVE)
            ->first();
    }

    public function openShift(): void
    {
        $this->validate([
            'startingCash' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $this->shiftService()->openShift(
                userId: auth()->id(),
                startingCash: (float)$this->startingCash
            );
            $this->startingCash = 0;
            unset($this->activeShift);
            $this->toast('Shift berhasil dibuka.');
            $this->js("window.dispatchEvent(new CustomEvent('close-open-shift-modal')); window.dispatchEvent(new CustomEvent('shift-active'));");
        } catch (Exception $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    public function saveExpense(): void
    {
        $activeShift = $this->activeShift();
        if (!$activeShift) {
            $this->toast('Tidak ada shift aktif.', 'danger');

            return;
        }

        $this->validate([
            'expenseAmount' => ['required', 'numeric', 'gt:0'],
            'expenseDescription' => ['required', 'string', 'max:255'],
        ]);

        try {
            $this->shiftService()->addExpense(
                shift: $activeShift,
                data: new ShiftExpenseData(
                    amount: (float)$this->expenseAmount,
                    description: $this->expenseDescription
                )
            );
            $this->expenseAmount = 0;
            $this->expenseDescription = '';
            $this->toast('Pengeluaran berhasil dicatat.');
            $this->js("window.dispatchEvent(new CustomEvent('close-shift-expense-modal'));");
        } catch (Exception $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    public function prepareCloseShift(): void
    {
        $activeShift = $this->activeShift();
        if (!$activeShift) {
            $this->toast('Tidak ada shift aktif.', 'danger');

            return;
        }

        $this->opnameItems = $this->shiftService()->initiateClose($activeShift);
        $this->actualCash = 0;
        $this->closeShiftStep = 1;
        $this->js("window.dispatchEvent(new CustomEvent('open-close-shift-modal'));");
    }

    public function submitCloseShift(): void
    {
        $activeShift = $this->activeShift();
        if (!$activeShift) {
            $this->toast('Tidak ada shift aktif.', 'danger');

            return;
        }

        if ($this->closeShiftStep === 1) {
            $this->validate([
                'opnameItems.*.physical_stock' => ['required', 'numeric', 'min:0'],
            ], [
                'opnameItems.*.physical_stock.required' => 'Semua stok fisik harus diisi.',
                'opnameItems.*.physical_stock.numeric' => 'Stok fisik harus berupa angka.',
            ]);
            $this->closeShiftStep = 2;

            return;
        }

        $this->validate([
            'actualCash' => ['required', 'numeric', 'min:0'],
        ]);

        try {
            $dtoItems = [];
            foreach ($this->opnameItems as $item) {
                $dtoItems[] = new ShiftOpnameItemData(
                    rawMaterialId: (int)$item['id'],
                    physicalStock: (float)$item['physical_stock'],
                    note: $item['note'] ?? null
                );
            }

            $closingData = new ShiftClosingData(
                actualCash: (float)$this->actualCash,
                opnameItems: $dtoItems
            );

            $this->shiftService()->closeShift(
                shift: $activeShift,
                data: $closingData
            );

            unset($this->activeShift);
            $this->toast('Shift berhasil ditutup.');
            $this->js("window.dispatchEvent(new CustomEvent('close-close-shift-modal')); window.dispatchEvent(new CustomEvent('shift-closed'));");
        } catch (Exception $e) {
            $this->toast($e->getMessage(), 'danger');
        }
    }

    #[Computed]
    public function storeSetting(): ?StoreSetting
    {
        return StoreSetting::select([
            'is_dinein_active', 'is_takeaway_active', 'is_delivery_active',
            'is_tax_active', 'tax_rate', 'is_service_charge_active', 'service_charge_rate', 'name', 'is_shift_active',
        ])->first();
    }

    #[Computed]
    public function activeOrders(): Collection|array
    {
        return Order::select([
            'id', 'invoice_code', 'customer_name', 'table_number', 'notes',
            'order_type', 'created_at', 'total_price', 'subtotal', 'amount_paid',
            'payment_method', 'status', 'kitchen_status', 'tax_amount',
            'service_charge_amount', 'discount', 'updated_at',
        ])
            ->with(['items' => fn ($query) => $query->select([
                'id', 'order_id', 'product_name', 'variant_name',
                'quantity', 'subtotal', 'note', 'kitchen_status',
            ])])
            // ponytail: optimize index usage and drop deep history scanning
            ->where(fn ($query) => $query->whereIn('status', ['pending', 'progress', 'paid'])
                ->orWhere(fn ($q) => $q->where('status', 'completed')
                    ->where('updated_at', '>=', now()->subHours(2))
                )
            )
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
            'order_type' => $order->order_type,
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
            'items' => $order->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'note' => $item->note,
                'kitchen_status' => $item->kitchen_status,
            ]),
        ];
    }

    public function setQueueFilter(string $filter): void
    {
        $this->queueFilter = $filter;
    }

    public function with(): array
    {
        $storeSetting = $this->storeSetting();
        $orderTypes = [];

        if ($storeSetting?->is_dinein_active) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];
        if ($storeSetting?->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        if ($storeSetting?->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
        if (empty($orderTypes)) $orderTypes[] = ['id' => 'dinein', 'label' => 'Makan Sini'];

        $activeOrders = $this->activeOrders();

        $queueOrders = $activeOrders->map(fn ($order) => $this->mapOrderForQueue($order));

        $kStatusCounts = $queueOrders->countBy('kStatus');

        // Filter queueOrders based on Livewire state for rendering
        $filteredQueueOrders = $queueOrders
            ->when($this->queueFilter !== 'all', fn ($collection) => $collection->where('kStatus', $this->queueFilter))
            ->when($this->queueSearch !== '', fn ($collection) => $collection->filter(fn ($order) => str_contains(strtolower($order->invoice_code), strtolower($this->queueSearch))
                || str_contains(strtolower($order->customer_name), strtolower($this->queueSearch))
                || str_contains(strtolower($order->table_number ?? $order->notes), strtolower($this->queueSearch))
            ));

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
            'isShiftActive' => $storeSetting?->is_shift_active ?? false,
            'activeShift' => $this->activeShift()?->only(['id', 'started_at', 'starting_cash', 'cash_sales', 'cash_expenses']),
            'counts' => $counts,
            'filters' => $filters,
            'queueOrders' => $filteredQueueOrders, // Use filtered orders for the view
        ];
    }
};
