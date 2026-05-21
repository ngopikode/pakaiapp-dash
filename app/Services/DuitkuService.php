<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * DuitkuService — Wrapper untuk Duitku PHP Library (duitkupg/duitku-php)
 *
 * Docs: https://docs.duitku.com/api/id
 *
 * Instalasi library:
 *   composer require duitkupg/duitku-php:dev-master
 *
 * Arsitektur Multi-Tenancy:
 *   merchantOrderId ke Duitku = "{tenantId}~{invoiceCode}"
 *   callbackUrl & returnUrl   = central domain (api.pakaiapp.online)
 *   Sehingga 1 URL callback berlaku untuk semua merchant.
 */
class DuitkuService
{
    private \Duitku\Config $config;

    public function __construct()
    {
        $merchantKey  = config('duitku.merchant_key');
        $merchantCode = config('duitku.merchant_code');

        // TODO(security): Di production, pastikan key tidak kosong. Fail-close jika tidak ada.
        if (empty($merchantKey) || empty($merchantCode)) {
            throw new RuntimeException('Duitku merchant key atau merchant code belum dikonfigurasi di .env');
        }

        $this->config = new \Duitku\Config($merchantKey, $merchantCode);
        $this->config->setSandboxMode((bool) config('duitku.sandbox', true));
        $this->config->setSanitizedMode(true);
        // NONAKTIFKAN log bawaan Duitku: library mencoba mkdir di vendor/duitkupg/duitku-php/logs/
        // yang tidak punya write permission. Logging sudah ditangani oleh Laravel Log di service ini.
        $this->config->setDuitkuLogs(false);
    }

    /**
     * Buat invoice pembayaran ke Duitku.
     *
     * @param  Order  $order           Order yang sudah tersimpan (status: pending)
     * @param  array  $customerDetail  Data customer: firstName, lastName, email, phoneNumber
     * @param  string $paymentMethod   Kode metode pembayaran Duitku (QRIS, BT, BV, dll)
     * @param  string $tenantId        ID tenant — digunakan untuk embed di merchantOrderId
     * @return array { payment_url, reference, va_number }
     *
     * @throws RuntimeException jika Duitku API error
     */
    public function createInvoice(Order $order, array $customerDetail, string $paymentMethod, string $tenantId): array
    {

        // Validasi tenantId — only alphanumeric + dash + underscore (UUID format)
        if (! preg_match('/^[A-Za-z0-9\-_]+$/', $tenantId)) {
            throw new RuntimeException('Format tenantId tidak valid.');
        }

        $expiryPeriod    = (int) config('duitku.expiry_period', 60);
        $callbackBaseUrl = config('duitku.callback_base_url', 'https://api.pakaiapp.online');

        // Central URLs — TIDAK menggunakan route() tenant karena butuh URL statis
        $callbackUrl = $callbackBaseUrl . '/duitku/callback';
        $returnUrl   = $callbackBaseUrl . '/duitku/return';

        // merchantOrderId = "{tenantId}~{invoiceCode}"
        // Format ini memungkinkan CentralDuitkuController mem-parse tenant & order
        $merchantOrderId = $tenantId . '~' . $order->invoice_code;

        // Sanitasi input customer — jangan kirim raw user input ke Duitku
        $firstName   = substr(strip_tags($customerDetail['firstName'] ?? 'Pelanggan'), 0, 50);
        $lastName    = substr(strip_tags($customerDetail['lastName'] ?? ''), 0, 50);
        $phoneNumber = preg_replace('/[^0-9+]/', '', $customerDetail['phoneNumber'] ?? '');

        // Email wajib valid untuk Duitku
        $email = filter_var($customerDetail['email'] ?? '', FILTER_VALIDATE_EMAIL)
            ? $customerDetail['email']
            : throw new RuntimeException('Email customer tidak valid. Email wajib untuk pembayaran Duitku.');

        $address = [
            'firstName'   => $firstName,
            'lastName'    => $lastName,
            'address'     => strip_tags($customerDetail['address'] ?? 'Indonesia'),
            'city'        => strip_tags($customerDetail['city'] ?? 'Jakarta'),
            'postalCode'  => preg_replace('/[^0-9]/', '', $customerDetail['postalCode'] ?? '00000'),
            'phone'       => $phoneNumber,
            'countryCode' => 'ID',
        ];

        $customerDetailParam = [
            'firstName'       => $firstName,
            'lastName'        => $lastName,
            'email'           => $email,
            'phoneNumber'     => $phoneNumber,
            'billingAddress'  => $address,
            'shippingAddress' => $address,
        ];

        // Build item details — Selalu gunakan single item representatif dengan total harga order.
        // Duitku mewajibkan paymentAmount sama persis dengan total penjumlahan (price * quantity) dari itemDetails.
        // Di POS/Store, total tagihan akhir dapat dipengaruhi oleh diskon, tax, service charge, dll.
        // Mengirimkan detail per item dapat menyebabkan ketidakcocokan jumlah total (Error 409).
        $itemDetails = [[
            'name'     => substr(strip_tags('Pembayaran ' . $order->invoice_code), 0, 255),
            'price'    => (int) $order->total_price,
            'quantity' => 1,
        ]];

        $params = [
            'paymentAmount'    => (int) $order->total_price,
            'paymentMethod'    => $paymentMethod,
            'merchantOrderId'  => $merchantOrderId,             // {tenantId}~{invoiceCode}
            'productDetails'   => 'Pembayaran ' . $order->invoice_code,
            'additionalParam'  => $tenantId,                    // backup identifier untuk callback
            'merchantUserInfo' => $tenantId,                    // backup identifier
            'customerVaName'   => trim($firstName . ' ' . $lastName),
            'email'            => $email,
            'phoneNumber'      => $phoneNumber,
            'itemDetails'      => $itemDetails,
            'customerDetail'   => $customerDetailParam,
            'callbackUrl'      => $callbackUrl,
            'returnUrl'        => $returnUrl . '?merchantOrderId=' . urlencode($merchantOrderId),
            'expiryPeriod'     => $expiryPeriod,
        ];

        Log::info('[Duitku] createInvoice request', [
            'merchantOrderId' => $merchantOrderId,
            'payment_method'  => $paymentMethod,
            'amount'          => $order->total_price,
        ]);

        $response = \Duitku\Api::createInvoice($params, $this->config);
        $data     = json_decode($response, true);

        if (! isset($data['paymentUrl'])) {
            Log::error('[Duitku] createInvoice gagal', ['response' => $data]);
            throw new RuntimeException('Gagal membuat invoice Duitku: ' . ($data['message'] ?? 'Unknown error'));
        }

        Log::info('[Duitku] createInvoice berhasil', [
            'merchantOrderId' => $merchantOrderId,
            'reference'       => $data['reference'] ?? null,
        ]);

        return [
            'payment_url' => $data['paymentUrl'],
            'reference'   => $data['reference'] ?? null,
            'va_number'   => $data['vaNumber'] ?? null,
        ];
    }

    /**
     * Cek status transaksi ke Duitku.
     *
     * @param  string $merchantOrderId  Format: "{tenantId}~{invoiceCode}"
     * @throws RuntimeException
     */
    public function checkTransactionStatus(string $merchantOrderId): array
    {
        // Validasi format — hanya izinkan alfanumerik, dash, tilde, underscore
        if (! preg_match('/^[A-Za-z0-9\-~_]+$/', $merchantOrderId)) {
            throw new RuntimeException('Format merchantOrderId tidak valid.');
        }

        $response = \Duitku\Api::transactionStatus($merchantOrderId, $this->config);
        $data     = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Response Duitku tidak valid (bukan JSON).');
        }

        return $data;
    }

    /**
     * Proses callback dari Duitku — validasi signature via library Duitku.
     *
     * @return array Data callback yang sudah divalidasi
     * @throws RuntimeException jika signature tidak valid
     */
    public function handleCallback(): array
    {
        $response = \Duitku\Api::callback($this->config);
        $data     = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Callback Duitku: data tidak valid.');
        }

        return $data;
    }

    /**
     * Ambil daftar metode pembayaran yang tersedia dari Duitku.
     *
     * @param  int $amount  Jumlah yang akan dibayar (dalam Rupiah)
     */
    public function getPaymentMethods(int $amount): array
    {
        $response = \Duitku\Api::getPaymentMethod((string) $amount, $this->config);
        $data     = json_decode($response, true);

        return $data['paymentFee'] ?? [];
    }
}
