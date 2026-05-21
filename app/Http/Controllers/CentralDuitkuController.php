<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Tenant;
use App\Services\DuitkuService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
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
            'resultCode'      => $request->input('resultCode'),
        ]);

        try {
            // Ambil merchantOrderId dari POST body (sebelum library validate)
            $rawMerchantOrderId = $request->input('merchantOrderId', '');

            // Parse tenantId dan invoiceCode dari format "{tenantId}~{invoiceCode}"
            [$tenantId, $invoiceCode] = $this->parseMerchantOrderId($rawMerchantOrderId);

            if (! $tenantId || ! $invoiceCode) {
                Log::warning('[Duitku Central] Format merchantOrderId tidak valid', [
                    'raw' => substr($rawMerchantOrderId, 0, 100),
                ]);
                return response('INVALID', 400);
            }

            // Cari tenant — validasi keberadaan tenant
            $tenant = Tenant::find($tenantId);

            if (! $tenant) {
                Log::warning('[Duitku Central] Tenant tidak ditemukan', ['tenantId' => $tenantId]);
                // Return 200 agar Duitku tidak retry terus untuk order yang tidak dikenal
                return response('OK', 200);
            }

            // Execute dalam konteks tenant — switch ke database tenant
            $tenant->run(function () use ($invoiceCode, $request) {
                $this->processCallback($invoiceCode, $request);
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
    public function return(Request $request): \Illuminate\View\View
    {
        $rawMerchantOrderId = $request->query('merchantOrderId', '');

        // Validasi karakter yang diperbolehkan
        if (! preg_match('/^[A-Za-z0-9\-~_]+$/', $rawMerchantOrderId)) {
            abort(400, 'Invalid order ID format.');
        }

        [$tenantId, $invoiceCode] = $this->parseMerchantOrderId($rawMerchantOrderId);

        $order = null;

        if ($tenantId && $invoiceCode) {
            $tenant = Tenant::find($tenantId);

            if ($tenant) {
                $tenant->run(function () use ($invoiceCode, &$order) {
                    $order = Order::where('invoice_code', $invoiceCode)->first();
                });
            }
        }

        // TODO(security): Jangan expose detail sensitif order ke customer.
        // Hanya tampilkan status dan invoice code, bukan data payment.
        return view('pages.tenant.payment.return', compact('order'));
    }

    /**
     * Cek status transaksi — endpoint untuk polling dari frontend atau kasir.
     *
     * Format invoiceCode: "{tenantId}~{invoiceCode}" atau hanya "{invoiceCode}"
     * jika sudah ada context (akan dicek dari semua tenant — tidak efisien).
     *
     * Gunakan format lengkap untuk performa terbaik.
     */
    public function status(string $invoiceCode): \Illuminate\Http\JsonResponse
    {
        // Validasi format
        if (! preg_match('/^[A-Za-z0-9\-~_]+$/', $invoiceCode)) {
            return response()->json(['message' => 'Format invoice code tidak valid.'], 400);
        }

        [$tenantId, $realInvoiceCode] = $this->parseMerchantOrderId($invoiceCode);

        if (! $tenantId || ! $realInvoiceCode) {
            return response()->json(['message' => 'Format invoice code harus {tenantId}~{invoiceCode}.'], 400);
        }

        $tenant = Tenant::find($tenantId);

        if (! $tenant) {
            return response()->json(['message' => 'Tenant tidak ditemukan.'], 404);
        }

        $order = null;

        $tenant->run(function () use ($realInvoiceCode, &$order) {
            $order = Order::where('invoice_code', $realInvoiceCode)->first();
        });

        if (! $order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        // Jika sudah final, return dari DB
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return response()->json([
                'status'       => $order->status,
                'invoice_code' => $order->invoice_code,
                'payment_url'  => $order->duitku_payment_url,
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
                    'status'       => $order->status,
                    'invoice_code' => $order->invoice_code,
                    'payment_url'  => $order->duitku_payment_url,
                    'duitku'       => [
                        'statusCode'    => $statusData['statusCode'] ?? null,
                        'statusMessage' => $statusData['statusMessage'] ?? null,
                    ],
                ]);
            } catch (Throwable $e) {
                Log::error('[Duitku Central] checkTransactionStatus error', [
                    'invoice_code' => $invoiceCode,
                    'error'        => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'status'       => $order->status,
            'invoice_code' => $order->invoice_code,
            'payment_url'  => $order->duitku_payment_url,
        ]);
    }

    /**
     * Proses logika callback di dalam tenant context.
     *
     * Method ini dipanggil oleh $tenant->run() sehingga sudah dalam DB tenant.
     */
    private function processCallback(string $invoiceCode, Request $request): void
    {
        // Validasi format invoiceCode — only alphanumeric + dash
        if (! preg_match('/^[A-Za-z0-9\-]+$/', $invoiceCode)) {
            Log::warning('[Duitku Central] invoiceCode tidak valid di processCallback', [
                'invoiceCode' => substr($invoiceCode, 0, 50),
            ]);
            return;
        }

        // Library Duitku validasi signature. Kita panggil callback() untuk validasi.
        // Namun karena kita sudah di dalam tenant->run(), config tetap dibaca dari central.
        $duitkuService = new DuitkuService();
        $notif         = $duitkuService->handleCallback();

        $resultCode  = $notif['resultCode'] ?? null;
        $order       = Order::where('invoice_code', $invoiceCode)->first();

        if (! $order) {
            Log::warning('[Duitku Central] Order tidak ditemukan di tenant DB', [
                'invoiceCode' => $invoiceCode,
            ]);
            return;
        }

        if ($resultCode === '00') {
            $order->update([
                'status'           => 'paid',
                'payment_method'   => $this->mapPaymentMethod($notif['paymentCode'] ?? ''),
                'duitku_reference' => $notif['reference'] ?? $order->duitku_reference,
                'amount_paid'      => (int) ($notif['amount'] ?? $order->total_price),
            ]);

            Log::info('[Duitku Central] Pembayaran berhasil', ['invoiceCode' => $invoiceCode]);

        } elseif ($resultCode === '01') {
            $order->update([
                'status'            => 'cancelled',
                'cancellation_note' => 'Pembayaran Duitku gagal (resultCode: 01)',
            ]);

            Log::info('[Duitku Central] Pembayaran gagal', ['invoiceCode' => $invoiceCode]);

        } else {
            Log::warning('[Duitku Central] resultCode tidak dikenal', [
                'invoiceCode' => $invoiceCode,
                'resultCode'  => $resultCode,
            ]);
        }
    }

    /**
     * Parse merchantOrderId format "{tenantId}~{invoiceCode}".
     *
     * @return array{0: string|null, 1: string|null}  [tenantId, invoiceCode]
     */
    private function parseMerchantOrderId(string $merchantOrderId): array
    {
        if (! str_contains($merchantOrderId, '~')) {
            return [null, null];
        }

        $parts = explode('~', $merchantOrderId, 2);

        $tenantId    = $parts[0] ?? null;
        $invoiceCode = $parts[1] ?? null;

        // Validasi: tenantId hanya boleh alfanumerik + dash + underscore (UUID format)
        if ($tenantId && ! preg_match('/^[A-Za-z0-9\-_]+$/', $tenantId)) {
            return [null, null];
        }

        // Validasi: invoiceCode hanya boleh alfanumerik + dash
        if ($invoiceCode && ! preg_match('/^[A-Za-z0-9\-]+$/', $invoiceCode)) {
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
