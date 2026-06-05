<?php

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public int $perPage = 10;

    #[On('order-updated')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->perPage = 10;
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->perPage = 10;
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    #[On('cancel-confirmed')]
    public function handleCancelConfirmed($orderId, $note): void
    {
        $this->updateStatus($orderId, 'cancelled', $note);
    }

    public function updateStatus($id, $status, $cancellationNote = null): void
    {
        $order = Order::with('items')->find($id);
        if (!$order) return;

        if ($status === 'cancelled' && $order->status === 'cancelled') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pesanan sudah dibatalkan sebelumnya.']);
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            return;
        }

        if ($status === 'cancelled' && $order->status !== 'pending') {
            if ($order->is_printed || \Carbon\Carbon::parse($order->created_at)->toDateString() !== today()->toDateString()) {
                $this->dispatch('notify', ['type' => 'error', 'message' => 'Pesanan yang sudah dicetak struk atau lewat hari tidak bisa dibatalkan.']);
                $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
                return;
            }
        }

        // --- VALIDASI FRAUD DAPUR ---
        if ($status === 'cancelled' && in_array($order->kitchen_status, ['processing', 'ready', 'completed'])) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pesanan tidak dapat dibatalkan karena sedang/sudah diproses oleh dapur.']);
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $status, $cancellationNote) {
            $updateData = ['status' => $status];
            if ($status === 'cancelled' && $cancellationNote) {
                $updateData['cancellation_note'] = $cancellationNote;
            }
            if ($status === 'completed') {
                $updateData['kitchen_status'] = 'completed';
            }
            $order->update($updateData);

            // Kembalikan stok saat cancel
            if ($status === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        \App\Models\ProductVariant::where('id', $item->variant_id)
                            ->increment('stock', $item->quantity);
                    }
                }
                if ($order->getOriginal('status') !== 'pending') {
                    app(\App\Services\BillingService::class)->processVoidPenalty($order);
                }
            }
        });

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status pesanan diperbarui.']);
        $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
    }


    public function splitOrder($orderId, $itemsToSplitData)
    {
        // $itemsToSplitData format: [ ['id' => order_item_id, 'qty' => split_quantity], ... ]
        $order = Order::with('items')->find($orderId);

        if (!$order || $order->status !== 'pending') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Hanya pesanan pending yang bisa dipisah.']);
            return;
        }

        if (empty($itemsToSplitData)) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pilih minimal 1 item untuk dipisah.']);
            return;
        }

        try {
            $newOrderId = \Illuminate\Support\Facades\DB::transaction(function () use ($order, $itemsToSplitData) {
                // 1. Create New Order
                $storeSetting = \App\Models\StoreSetting::first();
                $taxRate = $order->tax_percentage ?? 10.00;
                $serviceRate = $order->service_charge_percentage ?? 5.00;

                $newInvoiceCode = $order->invoice_code . '-' . strtoupper(\Illuminate\Support\Str::random(3));

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
                    'user_id' => \Illuminate\Support\Facades\Auth::id(),
                ]);

                $newSubtotal = 0;
                $oldSubtotal = 0;

                // 2. Process Items
                foreach ($itemsToSplitData as $splitData) {
                    $itemId = $splitData['id'];
                    $splitQty = (int)$splitData['qty'];

                    if ($splitQty <= 0) continue;

                    $item = $order->items->where('id', $itemId)->first();
                    if (!$item) continue;

                    // If splitting partial qty
                    if ($splitQty < $item->quantity) {
                        // Create item for new order
                        $newItemSubtotal = $item->price * $splitQty;
                        \App\Models\OrderItem::create([
                            'order_id' => $newOrder->id,
                            'product_id' => $item->product_id,
                            'variant_id' => $item->variant_id,
                            'product_name' => $item->product_name,
                            'variant_name' => $item->variant_name,
                            'quantity' => $splitQty,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'subtotal' => $newItemSubtotal,
                            'note' => $item->note,
                            'kitchen_status' => $item->kitchen_status,
                        ]);
                        $newSubtotal += $newItemSubtotal;

                        // Update old item
                        $remainQty = $item->quantity - $splitQty;
                        $oldItemSubtotal = $item->price * $remainQty;
                        $item->update([
                            'quantity' => $remainQty,
                            'subtotal' => $oldItemSubtotal,
                        ]);
                    } else {
                        // Move entirely
                        $item->update(['order_id' => $newOrder->id]);
                        $newSubtotal += $item->subtotal;
                    }
                }

                // 3. Recalculate New Order Totals
                $newServiceCharge = round(($serviceRate / 100) * $newSubtotal);
                $newTaxAmount = round(($taxRate / 100) * ($newSubtotal + $newServiceCharge));
                $newOrder->update([
                    'subtotal' => $newSubtotal,
                    'service_charge_amount' => $newServiceCharge,
                    'tax_amount' => $newTaxAmount,
                    'total_price' => $newSubtotal + $newServiceCharge + $newTaxAmount
                ]);

                // 4. Recalculate Old Order Totals
                $order->refresh(); // Reload items
                $oldSubtotal = $order->items->sum('subtotal');

                // If old order has 0 items left, maybe delete it?
                // For safety, we keep it as 0 if they moved everything, or we can delete it.
                // Assuming they don't split 100% of the items.
                if ($oldSubtotal == 0 && $order->items->count() == 0) {
                    $order->delete();
                } else {
                    $oldServiceCharge = round(($serviceRate / 100) * $oldSubtotal);
                    $oldTaxAmount = round(($taxRate / 100) * ($oldSubtotal + $oldServiceCharge));
                    $order->update([
                        'subtotal' => $oldSubtotal,
                        'service_charge_amount' => $oldServiceCharge,
                        'tax_amount' => $oldTaxAmount,
                        'total_price' => $oldSubtotal + $oldServiceCharge + $oldTaxAmount - $order->discount
                    ]);
                }

                return $newOrder->id;
            });

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pesanan berhasil dipisah.']);
            $this->js("window.dispatchEvent(new CustomEvent('close-split-modal'));");

            // Redirect to cashier queue tab to pay for the new order
            $this->redirectRoute('cashier');

        } catch (\Exception $e) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal memisah pesanan: ' . $e->getMessage()]);
        }
    }

    public function with(): array
    {
        $query = Order::query()
            ->when($this->search, function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_code', 'like', '%' . $this->search . '%')
                    ->orWhere('table_number', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            });

        $counts = \Illuminate\Support\Facades\DB::table('orders')
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $allCount = $counts->sum();

        return [
            'orders' => $query->with('items')->latest()->paginate($this->perPage),

            'allCount' => $allCount,
            'pendingCount' => $counts->get('pending', 0),
            'paidCount' => $counts->get('paid', 0),
            'progressCount' => $counts->get('progress', 0),
            'completedCount' => $counts->get('completed', 0),
            'cancelledCount' => $counts->get('cancelled', 0),
        ];
    }
};
