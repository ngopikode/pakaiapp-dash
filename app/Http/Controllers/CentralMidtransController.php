<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use Midtrans\Config;
use Midtrans\Notification;

class CentralMidtransController extends Controller
{
    /**
     * Handle notification/webhook dari Midtrans.
     * Endpoint ini diletakkan di central domain.
     */
    public function notification(Request $request)
    {
        Log::info('[Midtrans] Notification webhook received', $request->all());

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');

        try {
            $notif = new Notification();

            $transaction = $notif->transaction_status;
            $type = $notif->payment_type;
            $order_id = $notif->order_id;
            $fraud = $notif->fraud_status;

            if (empty($order_id)) {
                return response()->json(['message' => 'INVALID_ORDER_ID'], 400);
            }

            // Validasi Signature Key untuk memastikan webhook asli dari Midtrans
            $serverKey = config('midtrans.server_key');
            $signatureKey = hash('sha512', $notif->order_id . $notif->status_code . $notif->gross_amount . $serverKey);

            if ($signatureKey !== $notif->signature_key) {
                Log::warning('[Midtrans] Invalid signature key on webhook', [
                    'order_id' => $notif->order_id,
                ]);
                return response()->json(['message' => 'INVALID_SIGNATURE'], 403);
            }

            // Extract tenant ID dan real invoice code
            // Format: "{tenantId}~{invoiceCode}"
            if (!str_contains($order_id, '~')) {
                Log::warning('[Midtrans] Format order_id tidak valid (tidak ada tenant separator)', ['order_id' => $order_id]);
                return response()->json(['message' => 'INVALID_ORDER_FORMAT'], 400);
            }

            $parts = explode('~', $order_id, 2);
            $tenantId = $parts[0];
            $invoiceCode = $parts[1];

            // Inisialisasi context tenant secara manual
            try {
                tenancy()->initialize($tenantId);
            } catch (Throwable $e) {
                Log::error('[Midtrans] Gagal inisialisasi tenant', [
                    'tenant_id' => $tenantId,
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['message' => 'TENANT_NOT_FOUND'], 404);
            }

            // Cari order di DB tenant
            $order = Order::where('invoice_code', $invoiceCode)->first();

            if (!$order) {
                Log::warning('[Midtrans] Order tidak ditemukan di tenant', [
                    'tenant_id' => $tenantId,
                    'invoice_code' => $invoiceCode,
                ]);
                tenancy()->end();
                return response()->json(['message' => 'ORDER_NOT_FOUND'], 200); // 200 agar midtrans berhenti retry
            }

            // Cek idempotency: jika order sudah berstatus akhir, abaikan webhook
            if (in_array($order->status, ['paid', 'completed', 'cancelled'])) {
                Log::info('[Midtrans] Webhook diabaikan karena status order sudah final', [
                    'invoice_code' => $invoiceCode,
                    'current_status' => $order->status
                ]);
                tenancy()->end();
                return response()->json(['message' => 'ALREADY_PROCESSED'], 200);
            }

            $status = 'pending';

            if ($transaction == 'capture') {
                if ($type == 'credit_card'){
                    if($fraud == 'challenge'){
                        $status = 'pending';
                    } else {
                        $status = 'paid';
                    }
                }
            } else if ($transaction == 'settlement'){
                $status = 'paid';
            } else if ($transaction == 'pending'){
                $status = 'pending';
            } else if ($transaction == 'deny') {
                $status = 'cancelled';
            } else if ($transaction == 'expire') {
                $status = 'cancelled';
            } else if ($transaction == 'cancel') {
                $status = 'cancelled';
            }

            if ($status === 'paid') {
                $order->update([
                    'status' => 'paid',
                    'midtrans_transaction_id' => $notif->transaction_id,
                    'midtrans_payment_type' => $type,
                    'payment_method' => 'digital', // Atau parsing $type ke format enum di DB
                    'amount_paid' => (int) $notif->gross_amount,
                ]);
            } else if ($status === 'cancelled') {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_note' => 'Dibatalkan oleh sistem pembayaran Midtrans',
                    'midtrans_transaction_id' => $notif->transaction_id,
                    'midtrans_payment_type' => $type,
                ]);
            }

            Log::info('[Midtrans] Order status updated', [
                'invoice_code' => $invoiceCode,
                'status' => $status,
                'transaction_status' => $transaction
            ]);

            tenancy()->end();
            return response()->json(['message' => 'OK'], 200);

        } catch (Throwable $e) {
            Log::error('[Midtrans] Notification processing error', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'ERROR'], 500);
        }
    }
}
