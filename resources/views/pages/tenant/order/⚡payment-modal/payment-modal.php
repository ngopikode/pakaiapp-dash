<?php

use App\Models\Order;
use App\Services\TenantWalletService;
use App\Services\DuitkuService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public $paymentOrderId = null;
    public $paymentMethod = 'cash';
    public $paymentAmount = 0;
    public $paymentTotal = 0;

    // Duitku Payment Gateway fields
    public $duitkuMethod = null;
    public $duitkuCustomerEmail = '';
    public $duitkuPaymentMethods = [];

    // Tarif potong kredit per transaksi
    private int $feePerTransaction = 300;

    #[On('trigger-payment-modal')]
    public function openPaymentModal($orderId): void
    {
        $order = Order::find($orderId);
        if ($order) {
            $this->paymentOrderId = $orderId;
            // Gunakan payment_method yang sudah ada di order (misal dari toko online),
            // fallback ke 'cash' hanya jika belum ada.
            $this->paymentMethod = $order->payment_method ?: 'cash';
            $this->paymentTotal = $order->total_price;
            $this->paymentAmount = $order->total_price;
            $this->duitkuMethod = null;
            $this->duitkuCustomerEmail = '';
            $this->duitkuPaymentMethods = [];
            
            $this->fetchDuitkuMethods();
            $this->dispatch('show-payment-modal');
        }
    }

    public function fetchDuitkuMethods(): void
    {
        if (!config('duitku.enabled')) {
            $this->duitkuPaymentMethods = [];
            return;
        }

        try {
            $duitkuService = new DuitkuService();
            $methods = $duitkuService->getPaymentMethods((int) $this->paymentTotal);
            $this->duitkuPaymentMethods = $methods;
            if (!empty($methods)) {
                $hasQris = collect($methods)->first(fn($m) => in_array($m['paymentMethod'], ['NQ', 'SP', 'QRIS', 'QRISC']));
                $this->duitkuMethod = $hasQris ? $hasQris['paymentMethod'] : $methods[0]['paymentMethod'];
            }
        } catch (\Exception $e) {
            $this->duitkuPaymentMethods = [];
        }
    }

    public function processPayment(): void
    {
        // 1. DIGITAL PAYMENT (DUITKU)
        if ($this->paymentMethod === 'duitku') {
            if (!config('duitku.enabled')) {
                $this->dispatch('notify', message: 'Pembayaran digital Duitku sedang tidak aktif.', type: 'error');
                return;
            }

            if (!filter_var($this->duitkuCustomerEmail, FILTER_VALIDATE_EMAIL)) {
                $this->dispatch('notify', message: 'Email customer tidak valid.', type: 'error');
                return;
            }

            try {
                $paymentUrl = DB::transaction(function () {
                    $order = Order::with('items')->lockForUpdate()->find($this->paymentOrderId);

                    if (!$order || $order->status !== 'pending') {
                        throw new \Exception('Pesanan tidak ditemukan atau sudah dibayar.');
                    }

                    $customerDetail = [
                        'firstName' => $order->customer_name ?: 'Pelanggan',
                        'lastName' => '',
                        'email' => $this->duitkuCustomerEmail,
                        'phoneNumber' => $order->customer_phone ?: '',
                        'address' => 'Indonesia',
                        'city' => 'Jakarta',
                        'postalCode' => '00000',
                    ];

                    $duitkuService = new DuitkuService();
                    $tenantId = tenant()->getTenantKey();

                    $duitkuResult = $duitkuService->createInvoice(
                        $order,
                        $customerDetail,
                        $this->duitkuMethod,
                        $tenantId
                    );

                    $order->update([
                        'duitku_reference' => $duitkuResult['reference'],
                        'duitku_payment_url' => $duitkuResult['payment_url'],
                        'duitku_va_number' => $duitkuResult['va_number'],
                        'duitku_payment_method' => $this->duitkuMethod,
                    ]);

                    return $duitkuResult['payment_url'];
                });

                $this->dispatch('hide-payment-modal');
                $this->dispatch('order-updated');
                $this->dispatch('open-duitku-link', url: $paymentUrl);
                $this->dispatch('notify', message: 'Link pembayaran Duitku berhasil digenerate!', type: 'success');

            } catch (\Exception $e) {
                $this->dispatch('notify', message: $e->getMessage(), type: 'error');
            }
            return;
        }

        // 2. MANUAL PAYMENT (CASH, QRIS STATIS, TRANSFER)
        try {
            DB::transaction(function () {
                $order = Order::lockForUpdate()->find($this->paymentOrderId);

                if (!$order || $order->status !== 'pending') {
                    throw new \Exception('Pesanan tidak valid atau sudah dibayar sebelumnya.');
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
                    "Biaya pelunasan pesanan {$order->invoice_code}"
                );
            });

            $this->dispatch('hide-payment-modal');
            $this->dispatch('order-updated');
            $this->dispatch('notify', message: 'Pembayaran berhasil dikonfirmasi!', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }
};
