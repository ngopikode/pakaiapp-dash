<?php

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public $paymentOrderId = null;
    public $paymentMethod = 'cash';
    public $paymentAmount = 0;
    public $paymentTotal = 0;

    #[On('trigger-payment-modal')]
    public function openPaymentModal($orderId): void
    {
        $order = Order::find($orderId);
        if ($order) {
            $this->paymentOrderId = $orderId;
            $this->paymentMethod = 'cash';
            $this->paymentTotal = $order->total_price;
            $this->paymentAmount = $order->total_price;
            $this->dispatch('show-payment-modal');
        }
    }

    public function processPayment(): void
    {
        $order = Order::find($this->paymentOrderId);

        if ($order) {
            $change = max(0, (float)$this->paymentAmount - $order->total_price);

            $order->update([
                'status' => 'paid',
                'payment_method' => $this->paymentMethod,
                'amount_paid' => $this->paymentAmount,
                'change_amount' => $change
            ]);

            $this->dispatch('hide-payment-modal');
            $this->dispatch('order-updated'); // trigger table refresh
            $this->dispatch('notify', message: 'Pembayaran berhasil dikonfirmasi!');
        }
    }
};
