<?php

use App\Services\DuitkuService;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

beforeEach(function () {
    // Set standard Duitku credentials for testing
    Config::set('duitku.merchant_code', 'DS12345');
    Config::set('duitku.merchant_key', 'test_api_key_12345');
    Config::set('duitku.sandbox', true);
    Config::set('duitku.expiry_period', 10);
    Config::set('duitku.callback_base_url', 'https://api.pakaiapp.online');
    Config::set('duitku.ip_whitelist_enabled', false);
});

test('it correctly calculates HMAC-SHA256 signature and creates invoice', function () {
    // Fake the HTTP inquiry response
    Http::fake([
        'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response([
            'statusCode' => '00',
            'statusMessage' => 'SUCCESS',
            'paymentUrl' => 'https://sandbox.duitku.com/pay/123456',
            'reference' => 'REF12345',
            'vaNumber' => '7007014001444348',
        ], 200)
    ]);

    $order = new Order();
    $order->invoice_code = 'INV-999';
    $order->total_price = 50000;

    $customerDetail = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
        'phoneNumber' => '08123456789',
    ];

    $service = new DuitkuService();
    $result = $service->createInvoice($order, $customerDetail, 'BT', 'tenant123');

    // Assert that returned values are correct
    expect($result)->toBeArray()
        ->toHaveKey('payment_url', 'https://sandbox.duitku.com/pay/123456')
        ->toHaveKey('reference', 'REF12345')
        ->toHaveKey('va_number', '7007014001444348');

    // Assert that signature matches Duitku v2.0 signature standard
    // Formula: merchantCode + merchantOrderId + paymentAmount
    // merchantOrderId = "tenant123~INV-999"
    $expectedStringToSign = 'DS12345' . 'tenant123~INV-999' . 50000;
    $expectedSignature = hash_hmac('sha256', $expectedStringToSign, 'test_api_key_12345');

    Http::assertSent(function ($request) use ($expectedSignature) {
        return $request->url() === 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry'
            && $request['signature'] === $expectedSignature
            && $request['merchantCode'] === 'DS12345'
            && $request['paymentAmount'] === 50000
            && $request['merchantOrderId'] === 'tenant123~INV-999';
    });
});

test('it throws RuntimeException when createInvoice API returns non-00 status code', function () {
    Http::fake([
        'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry' => Http::response([
            'statusCode' => '401',
            'statusMessage' => 'Wrong signature',
        ], 200)
    ]);

    $order = new Order();
    $order->invoice_code = 'INV-999';
    $order->total_price = 50000;

    $customerDetail = [
        'firstName' => 'John',
        'lastName' => 'Doe',
        'email' => 'john@example.com',
    ];

    $service = new DuitkuService();

    expect(fn() => $service->createInvoice($order, $customerDetail, 'BT', 'tenant123'))
        ->toThrow(RuntimeException::class, 'Gagal membuat invoice Duitku: Wrong signature');
});

test('it correctly calculates signature and checks transaction status', function () {
    Http::fake([
        'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus' => Http::response([
            'merchantOrderId' => 'tenant123~INV-999',
            'reference' => 'REF12345',
            'amount' => '50000',
            'statusCode' => '00',
            'statusMessage' => 'SUCCESS',
        ], 200)
    ]);

    $service = new DuitkuService();
    $result = $service->checkTransactionStatus('tenant123~INV-999');

    expect($result)->toBeArray()
        ->toHaveKey('statusCode', '00')
        ->toHaveKey('statusMessage', 'SUCCESS');

    // Assert that signature matches Duitku v2.0 status signature standard
    // Formula: merchantCode + merchantOrderId
    $expectedStringToSign = 'DS12345' . 'tenant123~INV-999';
    $expectedSignature = hash_hmac('sha256', $expectedStringToSign, 'test_api_key_12345');

    Http::assertSent(function ($request) use ($expectedSignature) {
        return $request->url() === 'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus'
            && $request['signature'] === $expectedSignature
            && $request['merchantOrderId'] === 'tenant123~INV-999';
    });
});

test('it correctly fetches active payment methods', function () {
    Http::fake([
        'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod' => Http::response([
            'paymentFee' => [
                ['paymentMethod' => 'BT', 'paymentName' => 'Permata VA'],
                ['paymentMethod' => 'NQ', 'paymentName' => 'QRIS Nobu'],
            ],
            'responseCode' => '00',
            'responseMessage' => 'SUCCESS',
        ], 200)
    ]);

    $service = new DuitkuService();
    $result = $service->getPaymentMethods(50000);

    expect($result)->toBeArray()->toHaveCount(2);

    // Formula: merchantCode + amount + datetime
    Http::assertSent(function ($request) {
        $expectedStringToSign = 'DS12345' . 50000 . $request['datetime'];
        $expectedSignature = hash_hmac('sha256', $expectedStringToSign, 'test_api_key_12345');

        return $request->url() === 'https://sandbox.duitku.com/webapi/api/merchant/paymentmethod/getpaymentmethod'
            && $request['signature'] === $expectedSignature
            && $request['amount'] === 50000;
    });
});

test('it successfully validates callback with correct signature', function () {
    $merchantOrderId = 'tenant123~INV-999';
    $amount = '50000';
    $merchantCode = 'DS12345';

    // Calculate correct signature
    $stringToSign = $merchantCode . $amount . $merchantOrderId;
    $validSignature = hash_hmac('sha256', $stringToSign, 'test_api_key_12345');

    // Mock Laravel global request input
    request()->merge([
        'merchantCode' => $merchantCode,
        'amount' => $amount,
        'merchantOrderId' => $merchantOrderId,
        'signature' => $validSignature,
        'resultCode' => '00',
    ]);

    $service = new DuitkuService();
    $result = $service->handleCallback();

    expect($result)->toBeArray()
        ->toHaveKey('merchantOrderId', $merchantOrderId)
        ->toHaveKey('signature', $validSignature);
});

test('it throws RuntimeException on callback with invalid signature', function () {
    // Mock Laravel global request with wrong signature
    request()->merge([
        'merchantCode' => 'DS12345',
        'amount' => '50000',
        'merchantOrderId' => 'tenant123~INV-999',
        'signature' => 'invalid_signature_here',
    ]);

    $service = new DuitkuService();

    expect(fn() => $service->handleCallback())
        ->toThrow(RuntimeException::class, 'Callback Duitku: signature tidak valid.');
});
