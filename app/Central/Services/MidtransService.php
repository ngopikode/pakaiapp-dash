<?php

namespace App\Central\Services;

use App\Tenant\Models\Core\Order;
use App\Central\Models\TenantRegistration;
use App\Central\Services\BillingService;
use App\Central\Services\TenantRegistrationService;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct(
        protected readonly TenantRegistrationService $tenantRegService,
        protected readonly BillingService $billingService
    ) {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Membuat transaksi Snap Token untuk pemesanan.
     *
     * @param Order $order
     * @param array $customerDetail
     * @param string $tenantId
     * @return string $snapToken
     * @throws Exception
     */
    public function createSnapToken(Order $order, array $customerDetail, string $tenantId): string
    {
        // Merchant order ID gabungan untuk identifikasi tenant di webhook
        $merchantOrderId = $tenantId . '__' . $order->invoice_code;

        $params = [
            'transaction_details' => [
                'order_id' => $merchantOrderId,
                'gross_amount' => (int)$order->total_price,
            ],
            'customer_details' => [
                'first_name' => $customerDetail['firstName'] ?? '',
                'last_name' => $customerDetail['lastName'] ?? '',
                'email' => $customerDetail['email'] ?? '',
                'phone' => $customerDetail['phoneNumber'] ?? '',
            ],
            'item_details' => $order->items->map(function ($item) {
                return [
                    'id' => $item->product_id,
                    'price' => (int)$item->price,
                    'quantity' => (int)$item->quantity,
                    'name' => mb_strimwidth($item->product_name, 0, 50, '...'), // max 50 chars for Midtrans
                ];
            })->toArray()
        ];

        // Tambah Service Charge sebagai item_detail (jika ada)
        if ($order->service_charge_amount > 0) {
            $params['item_details'][] = [
                'id' => 'SERVICE_CHARGE',
                'price' => (int)$order->service_charge_amount,
                'quantity' => 1,
                'name' => 'Biaya Layanan Restoran',
            ];
        }

        // Tambah Biaya Aplikasi sebagai item_detail (jika ada)
        if ($order->application_fee > 0) {
            $params['item_details'][] = [
                'id' => 'APP_FEE',
                'price' => (int)$order->application_fee,
                'quantity' => 1,
                'name' => 'Biaya Aplikasi PakaiApp',
            ];
        }

        // Tambah Pajak (PB1) sebagai item_detail (jika ada)
        if ($order->tax_amount > 0) {
            $params['item_details'][] = [
                'id' => 'TAX_PB1',
                'price' => (int)$order->tax_amount,
                'quantity' => 1,
                'name' => 'Pajak Restoran (PB1)',
            ];
        }

        try {
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('[Midtrans] Gagal membuat Snap Token', [
                'order_id' => $merchantOrderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Membuat transaksi Snap Token khusus untuk pendaftaran/registrasi tenant di Central.
     *
     * @param TenantRegistration $registration
     * @return string $snapToken
     * @throws Exception
     */
    public function createRegistrationSnapToken(TenantRegistration $registration): string
    {
        $merchantOrderId = $registration->invoice_code;

        $params = [
            'transaction_details' => [
                'order_id' => $merchantOrderId,
                'gross_amount' => (int)$registration->amount,
            ],
            'customer_details' => [
                'first_name' => mb_strimwidth($registration->owner_name, 0, 50, ''),
                'email' => $registration->email,
                'phone' => $registration->whatsapp,
            ],
            'item_details' => [
                [
                    'id' => 'PLAN_' . strtoupper($registration->plan),
                    'price' => (int)$registration->amount,
                    'quantity' => 1,
                    'name' => 'Pendaftaran Pakaiapp - Paket ' . ucfirst($registration->plan),
                ]
            ],
            'callbacks' => [
                'finish' => 'https://api.pakaiapp.online/register/status/' . $registration->invoice_code,
            ],
        ];

        try {
            return Snap::getSnapToken($params);
        } catch (Exception $e) {
            Log::error('[Midtrans] Gagal membuat Snap Token Registrasi', [
                'order_id' => $merchantOrderId,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
    
    /**
     * Handle notification/webhook dari Midtrans.
     * Endpoint ini diletakkan di central domain.
     */
    public function handleWebhook(array $payload): void
    {
        // $payload can be ignored if we just use Notification object, but Notification reads from input stream.
        // It's better to let Notification initialize itself as it reads raw input.
        $notif = new \Midtrans\Notification();

        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $order_id = $notif->order_id;
        $fraud = $notif->fraud_status;

        if (empty($order_id)) {
            throw new Exception('INVALID_ORDER_ID');
        }

        // Validasi Signature Key untuk memastikan webhook asli dari Midtrans
        $signatureKey = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . Config::$serverKey);

        if ($signatureKey !== $notif->signature_key) {
            Log::warning('[Midtrans] Invalid signature key on webhook', ['order_id' => $notif->order_id]);
            throw new Exception('INVALID_SIGNATURE');
        }

        // Handle Central Tenant Registration
        if (str_starts_with($order_id, 'INV-REG-')) {
            $registration = TenantRegistration::where('invoice_code', $order_id)->first();
            $this->handleRegistrationWebhook($registration, $order_id, $transaction, $tenantRegService);
            return;
        }

        // Extract tenant ID dan real invoice code
        $separator = str_contains($order_id, '__') ? '__' : '~';
        if (!str_contains($order_id, $separator)) {
            Log::warning('[Midtrans] Format order_id tidak valid (tidak ada tenant separator)', ['order_id' => $order_id]);
            throw new Exception('INVALID_ORDER_FORMAT');
        }

        $parts = explode($separator, $order_id, 2);

        // Backward compatibility for old 'REG' tenantId
        if ($parts[0] === 'REG') {
            $registrationId = explode('~', $parts[1])[0] ?? null;
            $registration = TenantRegistration::find($registrationId);
            $this->handleRegistrationWebhook($registration, $order_id, $transaction, $tenantRegService);
            return;
        }

        $tenantId = $parts[0];
        $invoiceCode = $parts[1];

        // Inisialisasi context tenant secara manual
        try {
            tenancy()->initialize($tenantId);
        } catch (\Throwable $e) {
            Log::error('[Midtrans] Gagal inisialisasi tenant', [
                'tenant_id' => $tenantId,
                'error' => $e->getMessage(),
            ]);
            // Throwing exception so controller returns 404
            throw new Exception('TENANT_NOT_FOUND', 404);
        }

        // Cari order di DB tenant
        $order = Order::where('invoice_code', $invoiceCode)->first();

        if (!$order) {
            Log::warning('[Midtrans] Order tidak ditemukan di tenant', [
                'tenant_id' => $tenantId,
                'invoice_code' => $invoiceCode,
            ]);
            tenancy()->end();
            return; // Controller will return 200 OK so midtrans stops retry
        }

        // Cek idempotency
        if (in_array($order->status, ['paid', 'completed', 'cancelled'])) {
            Log::info('[Midtrans] Webhook diabaikan karena status order sudah final', [
                'invoice_code' => $invoiceCode,
                'current_status' => $order->status
            ]);
            tenancy()->end();
            return;
        }

        $status = $this->resolveOrderStatus($transaction, $type, $fraud);
        $paymentMethodDb = in_array(strtolower($type), ['gopay', 'shopeepay', 'qris']) ? 'qris' : 'transfer';

        if ($status === 'paid') {
            $amountPaid = (int)$notif->gross_amount;

            // MITIGASI FRAUD: Cek apakah nominal bayar sesuai
            if ($amountPaid < $order->total_price) {
                Log::warning('[Midtrans] Fraud detected: Underpaid', [
                    'invoiceCode' => $invoiceCode,
                    'amountPaid' => $amountPaid,
                    'expected' => $order->total_price,
                ]);
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_note' => 'Pembayaran otomatis dibatalkan karena nominal kurang dari tagihan (Underpaid).',
                    'midtrans_transaction_id' => $notif->transaction_id,
                    'midtrans_payment_type' => $type,
                ]);
                $order->restoreStock();
                event(new \App\Tenant\Events\KitchenUpdated());
                tenancy()->end();
                return;
            }

            $order->update([
                'status' => 'paid',
                'midtrans_transaction_id' => $notif->transaction_id,
                'midtrans_payment_type' => $type,
                'payment_method' => $paymentMethodDb,
                'amount_paid' => $amountPaid,
            ]);

            // POTONG SALDO WALLET (DYNAMIC PAYG CAPPING)
            $this->billingService->chargeTransactionFee($order);

            event(new \App\Tenant\Events\KitchenUpdated());

        } else if ($status === 'cancelled') {
            $order->update([
                'status' => 'cancelled',
                'cancellation_note' => 'Dibatalkan oleh sistem pembayaran Midtrans',
                'midtrans_transaction_id' => $notif->transaction_id,
                'midtrans_payment_type' => $type,
            ]);
            $order->restoreStock();
            event(new \App\Tenant\Events\KitchenUpdated());
        }

        Log::info('[Midtrans] Order status updated', [
            'invoice_code' => $invoiceCode,
            'status' => $status,
            'transaction_status' => $transaction
        ]);

        tenancy()->end();
    }

    private function handleRegistrationWebhook(?TenantRegistration $registration, string $orderId, string $transaction, TenantRegistrationService $tenantRegService): void
    {
        if (!$registration) {
            Log::warning('[Midtrans] Registration not found', ['reg_id' => $orderId]);
            throw new Exception('REG_NOT_FOUND', 404);
        }

        if (in_array($registration->status, ['paid', 'created', 'failed'])) {
            return; // Already processed
        }

        $status = $this->resolveRegistrationStatus($transaction);

        if ($status === 'paid') {
            $registration->update(['status' => 'paid']);
            $this->tenantRegService->completeRegistration($registration);
        } else if ($status === 'failed') {
            $registration->update(['status' => 'failed']);
        }
    }

    private function resolveRegistrationStatus(string $transaction): string
    {
        if (in_array($transaction, ['capture', 'settlement'])) {
            return 'paid';
        }
        if (in_array($transaction, ['deny', 'expire', 'cancel'])) {
            return 'failed';
        }
        return 'pending';
    }

    private function resolveOrderStatus(string $transaction, string $type, string $fraud): string
    {
        if ($transaction == 'capture') {
            if ($type == 'credit_card') {
                return ($fraud == 'challenge') ? 'pending' : 'paid';
            }
            return 'paid'; // Default capture is paid if not credit_card or no challenge
        }
        
        if ($transaction == 'settlement') return 'paid';
        if (in_array($transaction, ['deny', 'expire', 'cancel'])) return 'cancelled';
        
        return 'pending';
    }
}
