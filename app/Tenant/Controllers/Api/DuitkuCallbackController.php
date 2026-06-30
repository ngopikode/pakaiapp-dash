<?php

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tenant\Models\Core\Order;
use App\Central\Services\DuitkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * DuitkuCallbackController
 *
 * Menangani notifikasi server-to-server dari Duitku (callback) dan
 * redirect customer setelah selesai bayar (return).
 *
 * Docs: https://docs.duitku.com/api/id/#callback
 */
class DuitkuCallbackController extends Controller
{
    /**
     * Handle callback (notifikasi server-to-server dari Duitku).
     *
     * Duitku akan POST ke endpoint ini setiap kali ada update status transaksi.
     * Endpoint ini TIDAK perlu auth karena dipanggil oleh server Duitku,
     * tapi validasi signature dilakukan oleh library Duitku.
     *
     * TODO(security): Pertimbangkan whitelist IP server Duitku di middleware/firewall
     * untuk layer keamanan tambahan.
     */
    public function callback(Request $request): Response
    {
        Log::info('[Duitku] Callback diterima', [
            'merchantOrderId' => $request->input('merchantOrderId'),
            'resultCode'      => $request->input('resultCode'),
            // Jangan log amount atau data sensitif lainnya
        ]);

        // Whitelist IP server Duitku jika diaktifkan di konfigurasi
        if (config('duitku.ip_whitelist_enabled', false)) {
            $clientIp = $request->ip();
            $allowedIps = config('duitku.sandbox', true)
                ? ['182.23.85.11', '182.23.85.12', '103.177.101.187', '103.177.101.188']
                : ['182.23.85.8', '182.23.85.9', '182.23.85.10', '182.23.85.13', '182.23.85.14',
                   '103.177.101.184', '103.177.101.185', '103.177.101.186', '103.177.101.189', '103.177.101.190'];

            if (! in_array($clientIp, $allowedIps, true)) {
                Log::warning('[Duitku] IP callback tidak terdaftar', [
                    'ip' => $clientIp,
                ]);
                return response('INVALID_IP', 403);
            }
        }

        try {
            $duitkuService = new DuitkuService();
            $notif         = $duitkuService->handleCallback();

            $merchantOrderId = $notif['merchantOrderId'] ?? null;
            $resultCode      = $notif['resultCode'] ?? null;

            if (empty($merchantOrderId)) {
                Log::warning('[Duitku] Callback: merchantOrderId kosong');
                return response('INVALID', 400);
            }

            // Extract invoice code if it is in multi-tenant format "{tenantId}~{invoiceCode}"
            $realInvoiceCode = $merchantOrderId;
            if (str_contains($merchantOrderId, '~')) {
                $parts = explode('~', $merchantOrderId, 2);
                $realInvoiceCode = $parts[1] ?? $merchantOrderId;
            }

            // Cari order berdasarkan invoice_code (bukan id, sesuai yang kita kirim ke Duitku)
            // Validasi format untuk mencegah injection — invoice code hanya alfanumerik + dash
            if (! preg_match('/^[A-Za-z0-9\-]+$/', $realInvoiceCode)) {
                Log::warning('[Duitku] Callback: format realInvoiceCode tidak valid', [
                    'realInvoiceCode' => substr($realInvoiceCode, 0, 50),
                ]);
                return response('INVALID', 400);
            }

            $order = Order::where('invoice_code', $realInvoiceCode)->first();

            if (! $order) {
                Log::warning('[Duitku] Callback: order tidak ditemukan', [
                    'merchantOrderId' => $merchantOrderId,
                ]);
                // Tetap return 200 ke Duitku agar tidak retry terus
                return response('OK', 200);
            }

            if ($resultCode === '00') {
                // Pembayaran berhasil
                $order->update([
                    'status'           => 'paid',
                    'payment_method'   => $this->mapPaymentMethod($notif['paymentCode'] ?? ''),
                    'duitku_reference' => $notif['reference'] ?? $order->duitku_reference,
                    'amount_paid'      => (int) ($notif['amount'] ?? $order->total_price),
                ]);

                Log::info('[Duitku] Pembayaran berhasil', [
                    'invoice_code' => $merchantOrderId,
                    'result_code'  => $resultCode,
                ]);

            } elseif ($resultCode === '01') {
                // Pembayaran gagal
                $order->update([
                    'status'            => 'cancelled',
                    'cancellation_note' => 'Pembayaran Duitku gagal (resultCode: 01)',
                ]);

                Log::info('[Duitku] Pembayaran gagal', [
                    'invoice_code' => $merchantOrderId,
                    'result_code'  => $resultCode,
                ]);
            } else {
                Log::warning('[Duitku] Callback: resultCode tidak dikenal', [
                    'invoice_code' => $merchantOrderId,
                    'result_code'  => $resultCode,
                ]);
            }

            // Duitku mengharapkan response "OK" (plaintext) jika berhasil diproses
            return response('OK', 200);

        } catch (Throwable $e) {
            Log::error('[Duitku] Callback error', [
                'error' => $e->getMessage(),
                // JANGAN log stack trace lengkap ke production log yang bisa diakses publik
            ]);

            // Return 400 agar Duitku retry
            return response('ERROR', 400);
        }
    }

    /**
     * Handle return URL — customer diredirect kesini setelah bayar di halaman Duitku.
     *
     * Endpoint ini menampilkan halaman sukses/gagal kepada customer.
     * Status aktual transaksi sebaiknya dicek via callback, bukan dari return URL ini
     * karena return URL bisa di-manipulasi oleh user.
     */
    public function return(Request $request): RedirectResponse|View
    {
        $merchantOrderId = $request->query('merchantOrderId', '');

        // Validasi format — hanya izinkan alfanumerik + dash
        if (! preg_match('/^[A-Za-z0-9\-]*$/', $merchantOrderId)) {
            abort(400, 'Invalid order ID format.');
        }

        $order = null;

        if (! empty($merchantOrderId)) {
            $order = Order::where('invoice_code', $merchantOrderId)->first();
        }

        // TODO(security): Jangan tampilkan detail sensitif order kepada user yang tidak berhak.
        // Untuk MVP, kita hanya tampilkan status tanpa data sensitif.

        return view('pages.tenant.payment.return', compact('order'));
    }

    /**
     * Cek status transaksi (endpoint untuk polling dari frontend).
     *
     * Diproteksi oleh middleware auth agar hanya user yang login bisa cek status.
     *
     * @param  string $invoiceCode  Invoice code order
     */
    public function status(string $invoiceCode): JsonResponse
    {
        // Validasi format invoice code — hanya izinkan alfanumerik + dash
        if (! preg_match('/^[A-Za-z0-9\-]+$/', $invoiceCode)) {
            return response()->json(['message' => 'Format invoice code tidak valid.'], 400);
        }

        $order = Order::where('invoice_code', $invoiceCode)->first();

        if (! $order) {
            return response()->json(['message' => 'Order tidak ditemukan.'], 404);
        }

        // Jika order sudah paid/cancelled, return dari DB saja tanpa hit Duitku API
        if (in_array($order->status, ['paid', 'cancelled'])) {
            return response()->json([
                'status'       => $order->status,
                'invoice_code' => $order->invoice_code,
                'payment_url'  => $order->duitku_payment_url,
            ]);
        }

        // Jika masih pending dan ada duitku reference, cek ke Duitku API
        if ($order->duitku_reference) {
            try {
                $duitkuService = new DuitkuService();
                $statusData    = $duitkuService->checkTransactionStatus($order->invoice_code);

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
                Log::error('[Duitku] checkTransactionStatus error', [
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
     * Endpoint untuk mengambil daftar metode pembayaran Duitku yang tersedia.
     */
    public function paymentMethods(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $duitkuService = new DuitkuService();
            $methods       = $duitkuService->getPaymentMethods((int) $request->amount);

            return response()->json([
                'success' => true,
                'data'    => $methods,
            ]);

        } catch (Throwable $e) {
            Log::error('[Duitku] getPaymentMethods error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil metode pembayaran.',
            ], 500);
        }
    }

    /**
     * Map kode payment method Duitku ke nilai enum orders.payment_method.
     */
    private function mapPaymentMethod(string $paymentCode): string
    {
        $qrisCodes = ['QRIS', 'QRISC'];

        if (in_array(strtoupper($paymentCode), $qrisCodes, true)) {
            return 'qris';
        }

        // VA, transfer, e-wallet semuanya masuk 'transfer'
        return 'transfer';
    }
}
