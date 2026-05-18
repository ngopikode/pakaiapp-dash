<?php

use App\Models\Order;
use App\Services\TenantWalletService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public $paymentOrderId = null;
    public $paymentMethod = 'cash';
    public $paymentAmount = 0;
    public $paymentTotal = 0;

    // Tarif potong kredit per transaksi
    private int $feePerTransaction = 300;

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
        try {
            // Gunakan DB Transaction agar status order dan wallet sinkron
            DB::transaction(function () {
                // lockForUpdate mencegah pesanan dibayar dua kali secara bersamaan
                $order = Order::lockForUpdate()->find($this->paymentOrderId);

                if (!$order || $order->status !== 'pending') {
                    throw new Exception('Pesanan tidak valid atau sudah dibayar sebelumnya.');
                }

                $change = max(0, (float)$this->paymentAmount - $order->total_price);

                $order->update([
                    'status' => 'paid',
                    'payment_method' => $this->paymentMethod,
                    'amount_paid' => $this->paymentAmount,
                    'change_amount' => $change
                ]);

                // --- POTONG SALDO WALLET ---
                app(TenantWalletService::class)->deductBalance(
                    $this->feePerTransaction,
                    $order,
                    "Biaya pelunasan pesanan antrean {$order->invoice_code}"
                );
            });

            // Jika transaksi sukses (tidak ada Exception)
            $this->dispatch('hide-payment-modal');
            $this->dispatch('order-updated'); // trigger table refresh

            // Opsional: Kamu bisa pakai parameter type: 'success' kalau komponen notifikasimu mendukung
            $this->dispatch('notify', message: 'Pembayaran berhasil dikonfirmasi!');

        } catch (Exception $e) {
            // Jika saldo kurang atau error lainnya, tampilkan pesan error ke kasir
            // Tanpa menutup modal, agar kasir bisa memberi tahu owner untuk top up
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }
};
