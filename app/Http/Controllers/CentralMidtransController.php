<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;
use Throwable;

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

            // --- NEW: Handle Central Tenant Registration with custom invoice_code ---
            if (str_starts_with($order_id, 'INV-REG-')) {
                $registration = \App\Models\TenantRegistration::where('invoice_code', $order_id)->first();

                if (!$registration) {
                    Log::warning('[Midtrans] Registration not found', ['reg_id' => $order_id]);
                    return response()->json(['message' => 'REG_NOT_FOUND'], 200);
                }

                if (in_array($registration->status, ['paid', 'created', 'failed'])) {
                    return response()->json(['message' => 'ALREADY_PROCESSED'], 200);
                }

                $status = 'pending';
                if ($transaction == 'capture' || $transaction == 'settlement') {
                    $status = 'paid';
                } else if (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                    $status = 'failed';
                }

                if ($status === 'paid') {
                    $registration->update(['status' => 'paid']);

                    // Create Tenant
                    try {
                        $domainUrl = $registration->tenant_id . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
                        \Illuminate\Support\Facades\Artisan::call('tenant:create', [
                            'name' => $registration->store_name,
                            '--type' => $registration->store_type,
                            '--domain' => $domainUrl,
                            '--plan' => $registration->plan,
                        ]);

                        $tenant = \App\Models\Tenant::find($registration->tenant_id);
                        $tenant?->run(function () use ($registration) {
                            \App\Models\User::firstOrCreate(
                                ['email' => $registration->email],
                                [
                                    'name' => $registration->owner_name,
                                    'password' => $registration->password,
                                    'role' => 'manager'
                                ]
                            );
                        });

                        $registration->update(['status' => 'created']);

                        // Send Welcome Email
                        $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
                        $emailBody = "Halo {$registration->owner_name},\n\nTerima kasih atas pembayaran Anda! Sistem kasir toko Anda ({$registration->store_name}) telah selesai disiapkan dengan Paket " . ucfirst($registration->plan) . ".\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://{$domainUrl}/auth/login\nEmail: {$registration->email}\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";

                        \Illuminate\Support\Facades\Mail::to($registration->email)->send(
                            new \App\Mail\SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://{$domainUrl}/auth/login")
                        );

                        Log::info('[Midtrans] Tenant Registration Success', ['tenant_id' => $registration->tenant_id]);
                    } catch (\Exception $e) {
                        Log::error('[Midtrans] Failed to create tenant after payment', ['error' => $e->getMessage()]);

                        // Send Failure Email
                        $emailTitle = "Pendaftaran Toko Gagal";
                        $emailBody = "Halo {$registration->owner_name},\n\nTerima kasih atas pembayaran Anda. Namun, mohon maaf terjadi kesalahan sistem saat menyiapkan toko Anda ({$registration->store_name}). Tim kami sedang menelusuri masalah ini secara manual.\n\nSilakan hubungi tim support kami dengan melampirkan email ini agar segera ditindaklanjuti.\n\nSalam,\nTim Pakaiapp";

                        try {
                            \Illuminate\Support\Facades\Mail::to($registration->email)->send(
                                 new \App\Mail\SystemEmail($emailTitle, $emailBody, 'Hubungi Support', "https://wa.me/6285172441544")
                            );
                        } catch (\Exception $mailEx) {
                            Log::error('[Midtrans] Failed to send failure email: ' . $mailEx->getMessage());
                        }
                    }
                } else if ($status === 'failed') {
                    $registration->update(['status' => 'failed']);
                }

                return response()->json(['message' => 'OK'], 200);
            }

            // Extract tenant ID dan real invoice code using safe separators
            $separator = str_contains($order_id, '__') ? '__' : '~';
            if (!str_contains($order_id, $separator)) {
                Log::warning('[Midtrans] Format order_id tidak valid (tidak ada tenant separator)', ['order_id' => $order_id]);
                return response()->json(['message' => 'INVALID_ORDER_FORMAT'], 400);
            }

            $parts = explode($separator, $order_id, 2);

            // Backward compatibility for old 'REG' tenantId
            if ($parts[0] === 'REG') {
                $registrationId = explode('~', $parts[1])[0] ?? null;
                $registration = \App\Models\TenantRegistration::find($registrationId);

                if (!$registration) {
                    Log::warning('[Midtrans] Registration not found', ['reg_id' => $registrationId]);
                    return response()->json(['message' => 'REG_NOT_FOUND'], 200);
                }

                if (in_array($registration->status, ['paid', 'created', 'failed'])) {
                    return response()->json(['message' => 'ALREADY_PROCESSED'], 200);
                }

                $status = 'pending';
                if ($transaction == 'capture' || $transaction == 'settlement') {
                    $status = 'paid';
                } else if (in_array($transaction, ['deny', 'expire', 'cancel'])) {
                    $status = 'failed';
                }

                if ($status === 'paid') {
                    $registration->update(['status' => 'paid']);

                    // Create Tenant
                    try {
                        $domainUrl = $registration->tenant_id . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
                        \Illuminate\Support\Facades\Artisan::call('tenant:create', [
                            'name' => $registration->store_name,
                            '--type' => $registration->store_type,
                            '--domain' => $domainUrl,
                            '--plan' => $registration->plan,
                        ]);

                        $tenant = \App\Models\Tenant::find($registration->tenant_id);
                        $tenant?->run(function () use ($registration) {
                            \App\Models\User::firstOrCreate(
                                ['email' => $registration->email],
                                [
                                    'name' => $registration->owner_name,
                                    'password' => $registration->password,
                                    'role' => 'manager'
                                ]
                            );
                        });

                        $registration->update(['status' => 'created']);

                        // Send Welcome Email
                        $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
                        $emailBody = "Halo {$registration->owner_name},\n\nTerima kasih atas pembayaran Anda! Sistem kasir toko Anda ({$registration->store_name}) telah selesai disiapkan dengan Paket " . ucfirst($registration->plan) . ".\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://{$domainUrl}/auth/login\nEmail: {$registration->email}\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";

                        \Illuminate\Support\Facades\Mail::to($registration->email)->send(
                            new \App\Mail\SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://{$domainUrl}/auth/login")
                        );

                        Log::info('[Midtrans] Tenant Registration Success', ['tenant_id' => $registration->tenant_id]);
                    } catch (\Exception $e) {
                        Log::error('[Midtrans] Failed to create tenant after payment', ['error' => $e->getMessage()]);

                        // Send Failure Email
                        $emailTitle = "Pendaftaran Toko Gagal";
                        $emailBody = "Halo {$registration->owner_name},\n\nTerima kasih atas pembayaran Anda. Namun, mohon maaf terjadi kesalahan sistem saat menyiapkan toko Anda ({$registration->store_name}). Tim kami sedang menelusuri masalah ini secara manual.\n\nSilakan hubungi tim support kami dengan melampirkan email ini agar segera ditindaklanjuti.\n\nSalam,\nTim Pakaiapp";

                        try {
                            \Illuminate\Support\Facades\Mail::to($registration->email)->send(
                                 new \App\Mail\SystemEmail($emailTitle, $emailBody, 'Hubungi Support', "https://wa.me/6285172441544")
                            );
                        } catch (\Exception $mailEx) {
                            Log::error('[Midtrans] Failed to send failure email: ' . $mailEx->getMessage());
                        }
                    }
                } else if ($status === 'failed') {
                    $registration->update(['status' => 'failed']);
                }

                return response()->json(['message' => 'OK'], 200);
            }

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
                if ($type == 'credit_card') {
                    if ($fraud == 'challenge') {
                        $status = 'pending';
                    } else {
                        $status = 'paid';
                    }
                }
            } else if ($transaction == 'settlement') {
                $status = 'paid';
            } else if ($transaction == 'pending') {
                $status = 'pending';
            } else if ($transaction == 'deny') {
                $status = 'cancelled';
            } else if ($transaction == 'expire') {
                $status = 'cancelled';
            } else if ($transaction == 'cancel') {
                $status = 'cancelled';
            }

            $paymentMethodDb = in_array(strtolower($type), ['gopay', 'shopeepay', 'qris']) ? 'qris' : 'transfer';

            if ($status === 'paid') {
                $amountPaid = (int)$notif->gross_amount;
                
                // --- MITIGASI FRAUD: Cek apakah nominal bayar sesuai ---
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
                    
                    tenancy()->end();
                    return response()->json(['message' => 'OK'], 200); // 200 agar midtrans berhenti retry
                }

                $order->update([
                    'status' => 'paid',
                    'midtrans_transaction_id' => $notif->transaction_id,
                    'midtrans_payment_type' => $type,
                    'payment_method' => $paymentMethodDb,
                    'amount_paid' => $amountPaid,
                ]);

                // --- POTONG SALDO WALLET (DYNAMIC PAYG CAPPING) ---
                app(\App\Services\BillingService::class)->chargeTransactionFee($order);
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
