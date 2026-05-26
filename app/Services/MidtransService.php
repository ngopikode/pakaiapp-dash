<?php

namespace App\Services;

use App\Models\Order;
use App\Models\TenantRegistration;
use Exception;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    public function __construct()
    {
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
                'name' => 'Biaya Layanan',
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
}
