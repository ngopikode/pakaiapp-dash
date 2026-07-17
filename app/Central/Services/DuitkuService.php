<?php

namespace App\Central\Services;

use App\Central\Models\Tenant;
use App\Central\Models\TenantRegistration;
use App\Central\Services\BillingService;
use App\Central\Services\TenantRegistrationService;
use App\Tenant\Models\Core\Order;
use App\Tenant\Services\TenantWalletService;
use App\Central\Data\DuitkuInvoiceResultData;
use App\Central\Data\DuitkuPaymentMethodData;
use App\Central\Data\DuitkuTransactionStatusData;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Spatie\LaravelData\DataCollection;
use Throwable;

/**
 * DuitkuService — Wrapper untuk API Duitku Payment Gateway (API v2.0)
 *
 * Menggunakan HMAC-SHA256 sesuai standar keamanan terbaru Duitku.
 * Menggantikan library duitkupg/duitku-php yang usang dan tidak aman.
 *
 * Arsitektur Multi-Tenancy:
 *   merchantOrderId ke Duitku = "{tenantId}~{invoiceCode}"
 *   callbackUrl & returnUrl   = central domain (api.pakaiapp.online)
 *   Sehingga 1 URL callback berlaku untuk semua merchant.
 */
class DuitkuService
{
    private string $merchantKey;
    private string $merchantCode;
    private bool $sandbox;

    public function __construct(
        protected readonly TenantRegistrationService $tenantRegService,
        protected readonly TenantWalletService       $walletService,
        protected readonly BillingService            $billingService
    )
    {
        $this->merchantKey = config('duitku.merchant_key') ?? '';
        $this->merchantCode = config('duitku.merchant_code') ?? '';
        $this->sandbox = (bool)config('duitku.sandbox', true);

        // TODO(security): Di production, pastikan key tidak kosong. Fail-close jika tidak ada.
        if (empty($this->merchantKey) || empty($this->merchantCode)) {
            throw new RuntimeException('Duitku merchant key atau merchant code belum dikonfigurasi di .env');
        }
    }

    /**
     * Dapatkan base URL API Duitku secara dinamis berdasarkan environment sandbox.
     */
    private function getApiUrl(): string
    {
        return $this->sandbox
            ? 'https://sandbox.duitku.com'
            : 'https://passport.duitku.com';
    }

    /**
     * Buat invoice pembayaran ke Duitku.
     *
     * @param Order $order Order yang sudah tersimpan (status: pending)
     * @param array $customerDetail Data customer: firstName, lastName, email, phoneNumber
     * @param string $paymentMethod Kode metode pembayaran Duitku (QRIS, BT, BV, dll)
     * @param string $tenantId ID tenant — digunakan untuk embed di merchantOrderId
     * @return DuitkuInvoiceResultData
     *
     * @throws RuntimeException|ConnectionException jika Duitku API error
     */
    public function createInvoice(Order $order, array $customerDetail, string $paymentMethod, string $tenantId): DuitkuInvoiceResultData
    {
        // Validasi tenantId — only alphanumeric + dash + underscore (UUID format)
        if (!preg_match('/^[A-Za-z0-9\-_]+$/', $tenantId)) {
            throw new RuntimeException('Format tenantId tidak valid.');
        }

        $expiryPeriod = (int)config('duitku.expiry_period', 60);
        $callbackBaseUrl = config('duitku.callback_base_url', 'https://api.pakaiapp.online');

        // Central URLs — TIDAK menggunakan route() tenant karena butuh URL statis
        $callbackUrl = $callbackBaseUrl . '/duitku/callback';
        $returnUrl = $callbackBaseUrl . '/duitku/return';

        // merchantOrderId = "{tenantId}__{invoiceCode}"
        // Format ini memungkinkan DuitkuController mem-parse tenant & order
        $merchantOrderId = $tenantId . '__' . $order->invoice_code;

        // Sanitasi input customer — jangan kirim raw user input ke Duitku
        $firstName = substr(strip_tags($customerDetail['firstName'] ?? 'Pelanggan'), 0, 50);
        $lastName = substr(strip_tags($customerDetail['lastName'] ?? ''), 0, 50);
        $phoneNumber = preg_replace('/[^0-9+]/', '', $customerDetail['phoneNumber'] ?? '');

        // Email wajib valid untuk Duitku — gunakan fallback jika tidak ada
        // (Kasir sering tidak punya email customer; email manager sudah di-resolve di controller)
        $email = filter_var($customerDetail['email'] ?? '', FILTER_VALIDATE_EMAIL)
            ? $customerDetail['email']
            : 'noreply@pakaiapp.online';


        $address = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => strip_tags($customerDetail['address'] ?? 'Indonesia'),
            'city' => strip_tags($customerDetail['city'] ?? 'Jakarta'),
            'postalCode' => preg_replace('/[^0-9]/', '', $customerDetail['postalCode'] ?? '00000'),
            'phone' => $phoneNumber,
            'countryCode' => 'ID',
        ];

        $customerDetailParam = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
            'shippingAddress' => $address,
        ];

        // Build item details — Selalu gunakan single item representatif dengan total harga order.
        // Duitku mewajibkan paymentAmount sama persis dengan total penjumlahan (price * quantity) dari itemDetails.
        // Di POS/Store, total tagihan akhir dapat dipengaruhi oleh diskon, tax, service charge, dll.
        // Mengirimkan detail per item dapat menyebabkan ketidakcocokan jumlah total (Error 409).
        $itemDetails = [[
            'name' => substr(strip_tags('Pembayaran ' . $order->invoice_code), 0, 255),
            'price' => (int)$order->total_price,
            'quantity' => 1,
        ]];

        $paymentAmount = (int)$order->total_price;
        $stringToSign = $this->merchantCode . $merchantOrderId . $paymentAmount;
        $signature = hash_hmac('sha256', $stringToSign, $this->merchantKey);

        $params = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,             // {tenantId}__{invoiceCode}
            'productDetails' => 'Pembayaran ' . $order->invoice_code,
            'additionalParam' => $tenantId,                    // backup identifier untuk callback
            'merchantUserInfo' => $tenantId,                    // backup identifier
            'customerVaName' => trim($firstName . ' ' . $lastName),
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetailParam,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl . '?merchantOrderId=' . urlencode($merchantOrderId),
            'signature' => $signature,
            'expiryPeriod' => $expiryPeriod,
        ];

        Log::info('[Duitku] createInvoice request', [
            'merchantOrderId' => $merchantOrderId,
            'payment_method' => $paymentMethod,
            'amount' => $paymentAmount,
        ]);

        $url = $this->getApiUrl() . '/webapi/api/merchant/v2/inquiry';
        $response = Http::timeout(15)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $params);

        if (!$response->successful()) {
            Log::error('[Duitku] createInvoice HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Koneksi ke server Duitku bermasalah (HTTP ' . $response->status() . ').');
        }

        $data = $response->json();

        if (empty($data) || !is_array($data)) {
            Log::error('[Duitku] createInvoice response not JSON', ['body' => $response->body()]);
            throw new RuntimeException('Response dari Duitku tidak valid.');
        }

        if (($data['statusCode'] ?? '') !== '00') {
            Log::error('[Duitku] createInvoice gagal', ['response' => $data]);
            throw new RuntimeException('Gagal membuat invoice Duitku: ' . ($data['statusMessage'] ?? 'Unknown error'));
        }

        if (!isset($data['paymentUrl'])) {
            Log::error('[Duitku] createInvoice tidak mengembalikan paymentUrl', ['response' => $data]);
            throw new RuntimeException('Gagal membuat invoice Duitku: paymentUrl tidak ditemukan.');
        }

        Log::info('[Duitku] createInvoice berhasil', [
            'merchantOrderId' => $merchantOrderId,
            'reference' => $data['reference'] ?? null,
        ]);

        return new DuitkuInvoiceResultData(
            paymentUrl: $data['paymentUrl'],
            reference: $data['reference'] ?? '',
            vaNumber: $data['vaNumber'] ?? null,
        );
    }

    /**
     * Buat invoice pembayaran ke Duitku untuk Registrasi Tenant.
     *
     * @param TenantRegistration $registration Registration entry
     * @param string $paymentMethod Kode metode pembayaran Duitku (QRIS, BT, BV, dll)
     * @return array { payment_url, reference, va_number }
     *
     * @throws RuntimeException|ConnectionException jika Duitku API error
     */
    public function createRegistrationInvoice(TenantRegistration $registration, string $paymentMethod): DuitkuInvoiceResultData
    {
        $expiryPeriod = (int)config('duitku.expiry_period', 60);
        $callbackBaseUrl = config('duitku.callback_base_url', 'https://api.pakaiapp.online');

        // Central URLs
        $callbackUrl = $callbackBaseUrl . '/duitku/callback';
        $returnUrl = $callbackBaseUrl . '/register/status/' . $registration->invoice_code;

        // merchantOrderId = "{invoice_code}"
        $merchantOrderId = $registration->invoice_code;

        $firstName = substr(strip_tags($registration->owner_name), 0, 50);
        $lastName = ''; // We only collect one name field
        $phoneNumber = preg_replace('/[^0-9+]/', '', $registration->whatsapp ?? '');
        $email = filter_var($registration->email, FILTER_VALIDATE_EMAIL) ? $registration->email : 'noreply@pakaiapp.online';

        $address = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'address' => 'Indonesia',
            'city' => 'Jakarta',
            'postalCode' => '00000',
            'phone' => $phoneNumber,
            'countryCode' => 'ID',
        ];

        $customerDetailParam = [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'billingAddress' => $address,
            'shippingAddress' => $address,
        ];

        $itemDetails = [[
            'name' => 'Registrasi Pakaiapp Paket ' . ucfirst($registration->plan),
            'price' => (int)$registration->amount,
            'quantity' => 1,
        ]];

        $paymentAmount = (int)$registration->amount;
        $stringToSign = $this->merchantCode . $merchantOrderId . $paymentAmount;
        $signature = hash_hmac('sha256', $stringToSign, $this->merchantKey);

        $params = [
            'merchantCode' => $this->merchantCode,
            'paymentAmount' => $paymentAmount,
            'paymentMethod' => $paymentMethod,
            'merchantOrderId' => $merchantOrderId,
            'productDetails' => 'Registrasi Pakaiapp Paket ' . ucfirst($registration->plan),
            'additionalParam' => 'REG',
            'merchantUserInfo' => 'REG',
            'customerVaName' => trim($firstName . ' ' . $lastName),
            'email' => $email,
            'phoneNumber' => $phoneNumber,
            'itemDetails' => $itemDetails,
            'customerDetail' => $customerDetailParam,
            'callbackUrl' => $callbackUrl,
            'returnUrl' => $returnUrl,
            'signature' => $signature,
            'expiryPeriod' => $expiryPeriod,
        ];

        Log::info('[Duitku] createRegistrationInvoice request', [
            'merchantOrderId' => $merchantOrderId,
            'payment_method' => $paymentMethod,
            'amount' => $paymentAmount,
        ]);

        $url = $this->getApiUrl() . '/webapi/api/merchant/v2/inquiry';
        $response = Http::timeout(15)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $params);

        if (!$response->successful()) {
            Log::error('[Duitku] createRegistrationInvoice HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Koneksi ke server Duitku bermasalah (HTTP ' . $response->status() . ').');
        }

        $data = $response->json();

        if (empty($data) || !is_array($data)) {
            Log::error('[Duitku] createRegistrationInvoice response not JSON', ['body' => $response->body()]);
            throw new RuntimeException('Response dari Duitku tidak valid.');
        }

        if (($data['statusCode'] ?? '') !== '00') {
            Log::error('[Duitku] createRegistrationInvoice gagal', ['response' => $data]);
            throw new RuntimeException('Gagal membuat invoice Duitku: ' . ($data['statusMessage'] ?? 'Unknown error'));
        }

        if (!isset($data['paymentUrl'])) {
            Log::error('[Duitku] createRegistrationInvoice tidak mengembalikan paymentUrl', ['response' => $data]);
            throw new RuntimeException('Gagal membuat invoice Duitku: paymentUrl tidak ditemukan.');
        }

        Log::info('[Duitku] createRegistrationInvoice berhasil', [
            'merchantOrderId' => $merchantOrderId,
            'reference' => $data['reference'] ?? null,
        ]);

        return new DuitkuInvoiceResultData(
            paymentUrl: $data['paymentUrl'],
            reference: $data['reference'] ?? '',
            vaNumber: $data['vaNumber'] ?? null,
        );
    }

    /**
     * Cek status transaksi ke Duitku.
     *
     * @param string $merchantOrderId Format: "{tenantId}~{invoiceCode}"
     * @throws RuntimeException|ConnectionException
     */
    public function checkTransactionStatus(string $merchantOrderId): DuitkuTransactionStatusData
    {
        // Validasi format — hanya izinkan alfanumerik, dash, tilde, underscore
        if (!preg_match('/^[A-Za-z0-9\-~_]+$/', $merchantOrderId)) {
            throw new RuntimeException('Format merchantOrderId tidak valid.');
        }

        $stringToSign = $this->merchantCode . $merchantOrderId;
        $signature = hash_hmac('sha256', $stringToSign, $this->merchantKey);

        $params = [
            'merchantCode' => $this->merchantCode,
            'merchantOrderId' => $merchantOrderId,
            'signature' => $signature,
        ];

        $url = $this->getApiUrl() . '/webapi/api/merchant/transactionStatus';
        $response = Http::timeout(15)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $params);

        if (!$response->successful()) {
            Log::error('[Duitku] checkTransactionStatus HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Koneksi ke server Duitku bermasalah.');
        }

        $data = $response->json();

        if (empty($data) || !is_array($data)) {
            Log::error('[Duitku] checkTransactionStatus response not JSON', ['body' => $response->body()]);
            throw new RuntimeException('Response Duitku tidak valid.');
        }

        return new DuitkuTransactionStatusData(
            statusCode: $data['statusCode'] ?? null,
            statusMessage: $data['statusMessage'] ?? null,
        );
    }

    /**
     * Proses callback dari Duitku — validasi signature via HMAC-SHA256.
     *
     * @return array Data callback yang sudah divalidasi
     * @throws RuntimeException jika signature tidak valid
     */
    public function handleCallback(): array
    {
        $notification = request()->all();
        if (empty($notification)) {
            throw new RuntimeException('Callback Duitku: data kosong.');
        }

        $merchantCode = $notification['merchantCode'] ?? '';
        $amount = $notification['amount'] ?? '';
        $merchantOrderId = $notification['merchantOrderId'] ?? '';
        $signature = $notification['signature'] ?? '';

        if (empty($merchantCode) || empty($amount) || empty($merchantOrderId) || empty($signature)) {
            throw new RuntimeException('Callback Duitku: parameter wajib tidak lengkap.');
        }

        // Generate signature
        $stringToSign = $merchantCode . $amount . $merchantOrderId;
        $calcSignature = hash_hmac('sha256', $stringToSign, $this->merchantKey);

        if (!hash_equals($calcSignature, $signature)) {
            throw new RuntimeException('Callback Duitku: signature tidak valid.');
        }

        return $notification;
    }

    /**
     * Ambil daftar metode pembayaran yang tersedia dari Duitku.
     *
     * @param int $amount Jumlah yang akan dibayar (dalam Rupiah)
     * @throws ConnectionException
     */
    /**
     * @return DataCollection<DuitkuPaymentMethodData>
     */
    public function getPaymentMethods(int $amount): DataCollection
    {
        $datetime = date('Y-m-d H:i:s');
        $stringToSign = $this->merchantCode . $amount . $datetime;
        $signature = hash_hmac('sha256', $stringToSign, $this->merchantKey);

        $params = [
            'merchantcode' => $this->merchantCode,
            'amount' => $amount,
            'datetime' => $datetime,
            'signature' => $signature,
        ];

        $url = $this->getApiUrl() . '/webapi/api/merchant/paymentmethod/getpaymentmethod';
        $response = Http::timeout(10)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->post($url, $params);

        if (!$response->successful()) {
            Log::error('[Duitku] getPaymentMethods HTTP error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return [];
        }

        $data = $response->json();

        if (empty($data) || !is_array($data) || ($data['responseCode'] ?? '') !== '00') {
            Log::error('[Duitku] getPaymentMethods error response', ['response' => $data]);
            return [];
        }

        $paymentMethods = [];
        $methodsArray = $data['paymentFee'] ?? [];
        
        foreach ($methodsArray as $method) {
            $paymentMethods[] = new DuitkuPaymentMethodData(
                paymentMethod: $method['paymentMethod'],
                paymentName: $method['paymentName'],
                paymentImage: $method['paymentImage'],
                totalFee: (int) $method['totalFee']
            );
        }

        return DuitkuPaymentMethodData::collection($paymentMethods);
    }

    /**
     * Parse merchantOrderId format "{tenantId}~{invoiceCode}" atau "{tenantId}__{invoiceCode}".
     *
     * @return array{0: string|null, 1: string|null}  [tenantId, invoiceCode]
     */
    public function parseMerchantOrderId(string $merchantOrderId): array
    {
        $separator = str_contains($merchantOrderId, '__') ? '__' : '~';
        if (!str_contains($merchantOrderId, $separator)) {
            return [null, null];
        }

        $parts = explode($separator, $merchantOrderId, 2);

        $tenantId = $parts[0] ?? null;
        $invoiceCode = $parts[1] ?? null;

        // Validasi: tenantId hanya boleh alfanumerik + dash + underscore (UUID format)
        if ($tenantId && !preg_match('/^[A-Za-z0-9\-_]+$/', $tenantId)) {
            return [null, null];
        }

        // Validasi: invoiceCode hanya boleh alfanumerik + dash
        if ($invoiceCode && !preg_match('/^[A-Za-z0-9\-]+$/', $invoiceCode)) {
            return [null, null];
        }

        return [$tenantId, $invoiceCode];
    }

    /**
     * Map kode payment method Duitku ke nilai enum orders.payment_method.
     */
    public function mapPaymentMethodCode(string $paymentCode): string
    {
        if (in_array(strtoupper($paymentCode), ['QRIS', 'QRISC', 'SP', 'NQ', 'LQ', 'GQ'], true)) {
            return 'qris';
        }

        return 'transfer';
    }

    /**
     * @param string $rawMerchantOrderId
     * @return void
     * @throws Throwable
     */
    public function handleWebhook(string $rawMerchantOrderId): void
    {
        [$tenantId, $invoiceCode] = $this->parseMerchantOrderId($rawMerchantOrderId);

        $isRegCallback = (str_starts_with($rawMerchantOrderId, 'INV-REG-') || ($tenantId === 'REG' && $invoiceCode));

        if ($isRegCallback) {
            $registration = str_starts_with($rawMerchantOrderId, 'INV-REG-')
                ? TenantRegistration::where('invoice_code', $rawMerchantOrderId)->first()
                : TenantRegistration::find($invoiceCode);

            if (!$registration) {
                Log::warning('[Duitku Central] Registration not found', ['reg_id' => $rawMerchantOrderId]);
                throw new RuntimeException('REG_NOT_FOUND', 404);
            }

            if (in_array($registration->status, ['paid', 'created', 'failed'])) {
                return; // Already processed
            }

            // Verify signature using existing method
            $notif = $this->handleCallback();
            $resultCode = $notif['resultCode'] ?? null;

            if ($resultCode === '00') {
                $registration->update(['status' => 'paid']);
                $this->tenantRegService->completeRegistration($registration);
            } elseif ($resultCode === '01') {
                $registration->update(['status' => 'failed']);
            }

            return;
        }

        if (!$tenantId || !$invoiceCode) {
            Log::warning('[Duitku Central] Format merchantOrderId tidak valid', [
                'raw' => substr($rawMerchantOrderId, 0, 100),
            ]);
            throw new RuntimeException('INVALID', 400);
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            Log::warning('[Duitku Central] Tenant tidak ditemukan', ['tenantId' => $tenantId]);
            return; // Controller will return 200 to prevent retry
        }

        $tenant->run(function () use ($invoiceCode) {
            $this->processOrderCallback($invoiceCode);
        });
    }

    /**
     * @param string $invoiceCode
     * @return void
     * @throws Throwable
     */
    private function processOrderCallback(string $invoiceCode): void
    {
        if (!preg_match('/^[A-Za-z0-9\-]+$/', $invoiceCode)) {
            Log::warning('[Duitku Central] invoiceCode tidak valid di processCallback', [
                'invoiceCode' => substr($invoiceCode, 0, 50),
            ]);
            return;
        }

        $notif = $this->handleCallback();
        $resultCode = $notif['resultCode'] ?? null;

        DB::transaction(function () use ($invoiceCode, $notif, $resultCode) {
            $order = Order::where('invoice_code', $invoiceCode)->lockForUpdate()->first();

            if (!$order) {
                Log::warning('[Duitku Central] Order tidak ditemukan di tenant DB', ['invoiceCode' => $invoiceCode]);
                return;
            }

            if ($order->status !== 'pending') {
                Log::info('[Duitku Central] Callback diabaikan — order sudah diproses', [
                    'invoiceCode' => $invoiceCode,
                    'currentStatus' => $order->status,
                ]);
                return;
            }

            if ($resultCode === '00') {
                $amountPaid = (int)($notif['amount'] ?? $order->total_price);

                if ($amountPaid < $order->total_price) {
                    Log::warning('[Duitku Central] Fraud detected: Underpaid', [
                        'invoiceCode' => $invoiceCode,
                        'amountPaid' => $amountPaid,
                        'expected' => $order->total_price,
                    ]);
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_note' => 'Pembayaran otomatis dibatalkan karena nominal kurang dari tagihan (Underpaid).',
                ]);
                $order->restoreStock();
                event(new \App\Tenant\Events\KitchenUpdated());
                return;
                }

                $order->update([
                    'status' => 'paid',
                    'payment_method' => $this->mapPaymentMethodCode($notif['paymentCode'] ?? ''),
                    'duitku_reference' => $notif['reference'] ?? $order->duitku_reference,
                    'amount_paid' => $amountPaid,
                ]);

                $this->walletService->addBalance($amountPaid, $order, "Pendapatan Duitku masuk untuk pesanan $order->invoice_code");
                $this->billingService->chargeTransactionFee($order);

                event(new \App\Tenant\Events\KitchenUpdated());

                Log::info('[Duitku Central] Pembayaran berhasil, wallet dikreditkan', [
                    'invoiceCode' => $invoiceCode,
                    'amountPaid' => $amountPaid,
                ]);

            } elseif ($resultCode === '01') {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_note' => 'Pembayaran Duitku gagal (resultCode: 01)',
                ]);
                $order->restoreStock();
                event(new \App\Tenant\Events\KitchenUpdated());
                Log::info('[Duitku Central] Pembayaran gagal/dibatalkan, stok dikembalikan', ['invoiceCode' => $invoiceCode]);
            }
        });
    }
}
