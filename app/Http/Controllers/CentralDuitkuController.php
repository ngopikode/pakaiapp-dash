<?php

namespace App\Http\Controllers;

use App\Mail\SystemEmail;
use App\Models\Order;
use App\Models\Tenant;
use App\Models\TenantRegistration;
use App\Models\User;
use App\Services\BillingService;
use App\Services\DuitkuService;
use App\Services\TenantWalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

/**
 * CentralDuitkuController
 *
 * Menangani semua komunikasi Duitku yang masuk melalui central domain
 * (api.pakaiapp.online), bukan melalui subdomain tenant.
 *
 * Ini adalah solusi untuk sistem multi-tenancy: satu callback URL untuk
 * semua merchant. Tenant diidentifikasi dari prefix di merchantOrderId.
 *
 * Format merchantOrderId: "{tenantId}~{invoiceCode}"
 * Contoh: "abc-123~INV-20260521-ABCDEF"
 *
 * TODO(security): Pertimbangkan whitelist IP server Duitku di Nginx/firewall
 * untuk layer keamanan tambahan pada endpoint callback.
 */
class CentralDuitkuController extends Controller
{
    public function __construct()
    {
        if (!config('duitku.enabled')) {
            abort(403, 'Duitku payment gateway is disabled.');
        }
    }

    /**
     * Handle callback (notifikasi server-to-server dari Duitku).
     *
     * Endpoint ini tidak dilindungi auth atau CSRF karena dipanggil oleh
     * server Duitku, bukan browser. Validasi dilakukan via signature Duitku.
     */
    public function callback(Request $request): Response
    {
        Log::info('[Duitku Central] Callback diterima', [
            'merchantOrderId' => $request->input('merchantOrderId'),
            'resultCode' => $request->input('resultCode'),
        ]);

        try {
            // Ambil merchantOrderId dari POST body (sebelum library validate)
            $rawMerchantOrderId = $request->input('merchantOrderId', '');

            [$tenantId, $invoiceCode] = $this->parseMerchantOrderId($rawMerchantOrderId);

            // --- NEW: Handle Central Tenant Registration ---
            $isRegCallback = (str_starts_with($rawMerchantOrderId, 'INV-REG-') || ($tenantId === 'REG' && $invoiceCode));

            if ($isRegCallback) {
                if (str_starts_with($rawMerchantOrderId, 'INV-REG-')) {
                    $registration = TenantRegistration::where('invoice_code', $rawMerchantOrderId)->first();
                } else {
                    $registration = TenantRegistration::find($invoiceCode);
                }

                if (!$registration) {
                    Log::warning('[Duitku Central] Registration not found', ['reg_id' => $rawMerchantOrderId]);
                    return response('REG_NOT_FOUND', 200);
                }

                if (in_array($registration->status, ['paid', 'created', 'failed'])) {
                    return response('ALREADY_PROCESSED', 200);
                }

                // Verify signature using DuitkuService
                $duitkuService = new DuitkuService();
                $notif = $duitkuService->handleCallback();
                $resultCode = $notif['resultCode'] ?? null;

                if ($resultCode === '00') {
                    $registration->update(['status' => 'paid']);

                    // Create Tenant
                    try {
                        $domainUrl = $registration->tenant_id . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
                        Artisan::call('tenant:create', [
                            'name' => $registration->store_name,
                            '--id' => $registration->tenant_id,
                            '--type' => $registration->store_type,
                            '--domain' => $domainUrl,
                            '--plan' => $registration->plan,
                        ]);

                        $plainPassword = $registration->password; // Retrieve plain password
                        $createdTenant = Tenant::find($registration->tenant_id);
                        $createdTenant?->run(function () use ($registration, $plainPassword) {
                            User::firstOrCreate(
                                ['email' => $registration->email],
                                [
                                    'name' => $registration->owner_name,
                                    'password' => $plainPassword, // Set plain password (Laravel casts handles the hashing)
                                    'role' => 'manager'
                                ]
                            );
                        });

                        // Securely hash the password inside the central DB now that store is ready
                        $registration->update([
                            'status' => 'created',
                            'password' => Hash::make($plainPassword)
                        ]);

                        // Send Welcome Email
                        $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
                        $emailBody = "Halo $registration->owner_name,\n\nTerima kasih atas pembayaran Anda! Sistem kasir toko Anda ($registration->store_name) telah selesai disiapkan dengan Paket " . ucfirst($registration->plan) . ".\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://$domainUrl/auth/login\nEmail: $registration->email\nPassword: $plainPassword\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";

                        Mail::to($registration->email)->send(
                            new SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://$domainUrl/auth/login")
                        );

                        Log::info('[Duitku Central] Tenant Registration Success', ['tenant_id' => $registration->tenant_id]);
                    } catch (Exception $e) {
                        Log::error('[Duitku Central] Failed to create tenant after payment', ['error' => $e->getMessage()]);

                        // Send Failure Email
                        $emailTitle = "Pendaftaran Toko Gagal";
                        $emailBody = "Halo $registration->owner_name,\n\nTerima kasih atas pembayaran Anda. Namun, mohon maaf terjadi kesalahan sistem saat menyiapkan toko Anda ($registration->store_name). Tim kami sedang menelusuri masalah ini secara manual.\n\nSilakan hubungi tim support kami dengan melampirkan email ini agar segera ditindaklanjuti.\n\nSalam,\nTim Pakaiapp";

                        try {
                            Mail::to($registration->email)->send(
                                new SystemEmail($emailTitle, $emailBody, 'Hubungi Support', "https://wa.me/6285172441544")
                            );
                        } catch (Exception $mailEx) {
                            Log::error('[Duitku Central] Failed to send failure email: ' . $mailEx->getMessage());
                        }
                    }
                } elseif ($resultCode === '01') {
                    $registration->update(['status' => 'failed']);
                }

                return response('OK', 200);
            }
            // --- END NEW ---

            if (!$tenantId || !$invoiceCode) {
                Log::warning('[Duitku Central] Format merchantOrderId tidak valid', [
                    'raw' => substr($rawMerchantOrderId, 0, 100),
                ]);
                return response('INVALID', 400);
            }

            // Cari tenant — validasi keberadaan tenant
            $tenant = Tenant::find($tenantId);

            if (!$tenant) {
                Log::warning('[Duitku Central] Tenant tidak ditemukan', ['tenantId' => $tenantId]);
                // Return 200 agar Duitku tidak retry terus untuk order yang tidak dikenal
                return response('OK', 200);
            }

            // Execute dalam konteks tenant — switch ke database tenant
            $tenant->run(function () use ($invoiceCode, $request) {
                $this->processCallback($invoiceCode);
            });

            return response('OK', 200);

        } catch (Throwable $e) {
            Log::error('[Duitku Central] Callback error', [
                'error' => $e->getMessage(),
            ]);

            return response('ERROR', 400);
        }
    }

    /**
     * Handle return URL — customer diredirect kesini setelah bayar di Duitku.
     *
     * Format merchantOrderId di query string: "{tenantId}~{invoiceCode}"
     */
    public function return(Request $request): RedirectResponse|View
    {
        $rawMerchantOrderId = $request->query('merchantOrderId', '');

        // Validasi karakter yang diperbolehkan
        if (!preg_match('/^[A-Za-z0-9\-~_]+$/', $rawMerchantOrderId)) {
            abort(400, 'Invalid order ID format.');
        }

        // Handle Central Tenant Registration return directly
        if (str_starts_with($rawMerchantOrderId, 'INV-REG-')) {
            return redirect()->route('register.status', ['invoice_code' => $rawMerchantOrderId]);
        }

        [$tenantId, $invoiceCode] = $this->parseMerchantOrderId($rawMerchantOrderId);

        if ($tenantId === 'REG' && $invoiceCode) {
            $registration = TenantRegistration::find($invoiceCode);
            if ($registration) {
                return redirect()->route('register.status', ['invoice_code' => $registration->invoice_code]);
            }
        }

        if ($tenantId && $invoiceCode) {
            $tenant = Tenant::find($tenantId);

            if ($tenant) {
                // Ambil domain utama tenant
                $domain = $tenant->domains->first()?->domain;
                if (!$domain) {
                    // Fallback ke subdomain default jika domain kosong
                    $centralDomain = config('tenancy.central_domains')[2] ?? 'pakaiapp.online';
                    $domain = $tenantId . '.' . $centralDomain;
                }

                $scheme = $request->secure() ? 'https' : 'http';

                // Redirect langsung ke invoice di domain tenant
                return redirect()->away("$scheme://$domain/invoice/$invoiceCode");
            }
        }

        abort(404, 'Order atau Tenant tidak ditemukan.');
    }

    /**
     * Cek status transaksi — endpoint untuk polling dari frontend atau kasir.
     *
     * Format invoiceCode: "{tenantId}~{invoiceCode}" atau hanya "{invoiceCode}"
     * jika sudah ada context (akan dicek dari semua tenant — tidak efisien).
     *
     * Gunakan format lengkap untuk performa terbaik.
     */
    public function status(string $invoiceCode): JsonResponse
    {
        // Validasi format
        if (!preg_match('/^[A-Za-z0-9\-~_]+$/', $invoiceCode)) {
            return response()->json(['message' => 'Format invoice code tidak valid.'], 400);
        }

        [$tenantId, $realInvoiceCode] = $this->parseMerchantOrderId($invoiceCode);

        if (!$tenantId || !$realInvoiceCode) {
            return response()->json(['message' => 'Format invoice code harus {tenantId}~{invoiceCode}.'], 400);
        }

        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            return response()->json(['message' => 'Tenant tidak ditemukan.'], 404);
        }

        $order = null;

        $tenant->run(function () use ($realInvoiceCode, &$order) {
            $order = Order::where('invoice_code', $realInvoiceCode)->first();
        });

        if (!$order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        // Jika sudah final, return dari DB
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return response()->json([
                'status' => $order->status,
                'invoice_code' => $order->invoice_code,
                'payment_url' => $order->duitku_payment_url,
            ]);
        }

        // Jika masih pending dan ada reference, cek ke Duitku
        if ($order->duitku_reference) {
            try {
                // DuitkuService tidak butuh tenant context untuk check status
                $duitkuService = new DuitkuService();
                // merchantOrderId ke Duitku adalah format {tenantId}~{invoiceCode}
                $statusData = $duitkuService->checkTransactionStatus($invoiceCode);

                return response()->json([
                    'status' => $order->status,
                    'invoice_code' => $order->invoice_code,
                    'payment_url' => $order->duitku_payment_url,
                    'duitku' => [
                        'statusCode' => $statusData['statusCode'] ?? null,
                        'statusMessage' => $statusData['statusMessage'] ?? null,
                    ],
                ]);
            } catch (Throwable $e) {
                Log::error('[Duitku Central] checkTransactionStatus error', [
                    'invoice_code' => $invoiceCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status' => $order->status,
            'invoice_code' => $order->invoice_code,
            'payment_url' => $order->duitku_payment_url,
        ]);
    }

    /**
     * Proses logika callback di dalam tenant context.
     *
     *  Method ini dipanggil oleh $tenant->run() sehingga sudah dalam DB tenant.
     * @param string $invoiceCode
     * @return void
     * @throws Throwable
     */
    private function processCallback(string $invoiceCode): void
    {
        // Validasi format invoiceCode — only alphanumeric + dash
        if (!preg_match('/^[A-Za-z0-9\-]+$/', $invoiceCode)) {
            Log::warning('[Duitku Central] invoiceCode tidak valid di processCallback', [
                'invoiceCode' => substr($invoiceCode, 0, 50),
            ]);
            return;
        }

        // Validasi signature Duitku via HMAC-SHA256 sebelum melakukan apapun
        $duitkuService = new DuitkuService();
        $notif = $duitkuService->handleCallback();

        $resultCode = $notif['resultCode'] ?? null;

        // Semua mutasi order + wallet dibungkus dalam satu DB transaction —
        // jika salah satu gagal, semuanya rollback agar data tetap konsisten.
        DB::transaction(function () use ($invoiceCode, $notif, $resultCode) {
            // lockForUpdate: cegah race condition jika Duitku retry callback bersamaan
            $order = Order::where('invoice_code', $invoiceCode)->lockForUpdate()->first();

            if (!$order) {
                Log::warning('[Duitku Central] Order tidak ditemukan di tenant DB', [
                    'invoiceCode' => $invoiceCode,
                ]);
                return;
            }

            // ──── IDEMPOTENCY GUARD ────────────────────────────────────────────
            // Jika order sudah bukan 'pending', callback sudah diproses sebelumnya.
            // Duitku kadang mengirim callback berulang — hentikan di sini.
            if ($order->status !== 'pending') {
                Log::info('[Duitku Central] Callback diabaikan — order sudah diproses', [
                    'invoiceCode' => $invoiceCode,
                    'currentStatus' => $order->status,
                ]);
                return;
            }
            // ──────────────────────────────────────────────────────────────────

            if ($resultCode === '00') {
                // Ambil jumlah yang benar-benar dibayar customer dari notifikasi Duitku
                $amountPaid = (int)($notif['amount'] ?? $order->total_price);

                // --- MITIGASI FRAUD: Cek apakah nominal bayar sesuai ---
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
                    return;
                }

                $order->update([
                    'status' => 'paid',
                    'payment_method' => $this->mapPaymentMethod($notif['paymentCode'] ?? ''),
                    'duitku_reference' => $notif['reference'] ?? $order->duitku_reference,
                    'amount_paid' => $amountPaid,
                ]);

                // ── KREDIT WALLET ──────────────────────────────────────────────
                // Uang dari customer yang berhasil masuk via Duitku dikreditkan ke
                // wallet tenant sebagai catatan kas masuk digital.
                $walletService = app(TenantWalletService::class);

                $walletService->addBalance(
                    $amountPaid,
                    $order,
                    "Pendapatan Duitku masuk untuk pesanan $order->invoice_code"
                );

                // Potong biaya layanan pakaiapp dengan sistem dinamis
                $billingService = app(BillingService::class);
                $billingService->chargeTransactionFee($order);
                // ──────────────────────────────────────────────────────────────

                Log::info('[Duitku Central] Pembayaran berhasil, wallet dikreditkan', [
                    'invoiceCode' => $invoiceCode,
                    'amountPaid' => $amountPaid,
                ]);

            } elseif ($resultCode === '01') {
                $order->update([
                    'status' => 'cancelled',
                    'cancellation_note' => 'Pembayaran Duitku gagal (resultCode: 01)',
                ]);

                Log::info('[Duitku Central] Pembayaran gagal/dibatalkan', ['invoiceCode' => $invoiceCode]);

            } else {
                Log::warning('[Duitku Central] resultCode tidak dikenal, tidak ada perubahan', [
                    'invoiceCode' => $invoiceCode,
                    'resultCode' => $resultCode,
                ]);
            }
        });
    }

    /**
     * Parse merchantOrderId format "{tenantId}~{invoiceCode}".
     *
     * @return array{0: string|null, 1: string|null}  [tenantId, invoiceCode]
     */
    private function parseMerchantOrderId(string $merchantOrderId): array
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
    private function mapPaymentMethod(string $paymentCode): string
    {
        if (in_array(strtoupper($paymentCode), ['QRIS', 'QRISC', 'SP', 'NQ', 'LQ', 'GQ'], true)) {
            return 'qris';
        }

        return 'transfer';
    }
}
