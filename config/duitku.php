<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Duitku Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Duitku API. Isi DUITKU_MERCHANT_KEY dan
    | DUITKU_MERCHANT_CODE di file .env kamu.
    |
    | Docs: https://docs.duitku.com/api/id
    |
    */

    // Status aktif Duitku Payment Gateway
    'enabled' => filter_var(env('DUITKU_ENABLED', true), FILTER_VALIDATE_BOOLEAN),

    // Merchant key dari dashboard Duitku — JANGAN hardcode di sini, pakai .env
    'merchant_key' => env('DUITKU_MERCHANT_KEY'),

    // Merchant code dari dashboard Duitku
    'merchant_code' => env('DUITKU_MERCHANT_CODE'),

    // true = sandbox (testing), false = production
    'sandbox' => env('DUITKU_SANDBOX', true),

    // Masa berlaku transaksi (dalam menit)
    'expiry_period' => env('DUITKU_EXPIRY_PERIOD', 60),

    /*
    |--------------------------------------------------------------------------
    | Central Callback Base URL
    |--------------------------------------------------------------------------
    |
    | URL dasar untuk callback dan return URL Duitku.
    | Gunakan domain central (bukan subdomain tenant) agar satu URL berlaku
    | untuk semua merchant dalam sistem multi-tenancy.
    |
    | Contoh: https://api.pakaiapp.online
    |
    */
    'callback_base_url' => rtrim(env('DUITKU_CALLBACK_BASE_URL', 'https://api.pakaiapp.online'), '/'),

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    |
    | Daftar kode metode pembayaran Duitku yang diaktifkan.
    | Lihat daftar lengkap di: https://docs.duitku.com/api/id/#payment-method
    |
    | Contoh:
    |   VC  = Credit Card (Visa / Master Card / JCB)
    |   BT  = Permata Bank Virtual Account
    |   B1  = CIMB Niaga Virtual Account
    |   BV  = BNI Virtual Account
    |   I1  = BRI Virtual Account
    |   VA  = Maybank Virtual Account
    |   A1  = ATM Bersama
    |   FT  = Alfa Group (Alfamart & Alfamidi)
    |   OV  = OVO (Support Void)
    |   SA  = Shopeepay Apps (Support Void)
    |   LF  = Pegadaian/Arisan/Pos
    |   DA  = DANA
    |   OL  = Indodana Paylater
    |   LA  = Kredivo
    |   QRIS = QRIS (static)
    |   QRISC = QRIS (dynamic, untuk kasir)
    |
    */
    'active_methods' => [
        'NQ'    => 'QRIS Nobu',
        'SP'    => 'QRIS ShopeePay',
        'BT'    => 'Permata VA',
        'I1'    => 'BNI VA',
        'BR'    => 'BRI VA',
        'BC'    => 'BCA VA',
        'BV'    => 'BSI VA',
        'DA'    => 'DANA',
        'OV'    => 'OVO',
    ],
];
