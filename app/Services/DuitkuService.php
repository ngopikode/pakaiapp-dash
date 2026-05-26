<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

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

    public function __construct()
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
     * @return array { payment_url, reference, va_number }
     *
     * @throws RuntimeException|ConnectionException jika Duitku API error
     */
    public function createInvoice(Order $order, array $customerDetail, string $paymentMethod, string $tenantId): array
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
        // Format ini memungkinkan CentralDuitkuController mem-parse tenant & order
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

        return [
            'payment_url' => $data['paymentUrl'],
            'reference' => $data['reference'] ?? null,
            'va_number' => $data['vaNumber'] ?? null,
        ];
    }

    /**
     * Buat invoice pembayaran ke Duitku untuk Registrasi Tenant.
     *
     * @param \App\Models\TenantRegistration $registration Registration entry
     * @param string $paymentMethod Kode metode pembayaran Duitku (QRIS, BT, BV, dll)
     * @return array { payment_url, reference, va_number }
     *
     * @throws RuntimeException|ConnectionException jika Duitku API error
     */
    public function createRegistrationInvoice(\App\Models\TenantRegistration $registration, string $paymentMethod): array
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

        return [
            'payment_url' => $data['paymentUrl'],
            'reference' => $data['reference'] ?? null,
            'va_number' => $data['vaNumber'] ?? null,
        ];
    }

    /**
     * Cek status transaksi ke Duitku.
     *
     * @param string $merchantOrderId Format: "{tenantId}~{invoiceCode}"
     * @throws RuntimeException|ConnectionException
     */
    public function checkTransactionStatus(string $merchantOrderId): array
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

        return $data;
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
    public function getPaymentMethods(int $amount): array
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

        return $data['paymentFee'] ?? [];
    }
}
