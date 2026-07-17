<?php

namespace App\Central\Controllers;

use App\Central\Data\GetPaymentMethodsInputData;
use App\Central\Models\Tenant;
use App\Central\Models\TenantRegistration;
use App\Central\Services\DuitkuService;
use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use App\Tenant\Models\Core\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * DuitkuController
 *
 * Menangani semua komunikasi Duitku yang masuk melalui central domain
 * (api.pakaiapp.online), bukan melalui subdomain tenant.
 *
 * Ini adalah solusi untuk sistem multi-tenancy: satu callback URL untuk
 * semua merchant. Tenant diidentifikasi dari prefix di merchantOrderId.
 *
 * Format merchantOrderId: "{tenantId}~{invoiceCode}"
 * Contoh: "abc-123~INV-20260521-ABCDEF"
 */
class DuitkuController extends Controller
{
    use ApiResponserTrait;
    
    public function __construct(protected readonly DuitkuService $duitkuService)
    {
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
            // Ambil merchantOrderId dari POST body
            $rawMerchantOrderId = (string)$request->input('merchantOrderId', '');

            $this->duitkuService->handleWebhook($rawMerchantOrderId);

            return response('OK');

        } catch (Throwable $e) {
            Log::error('[Duitku Central] Callback error', [
                'error' => $e->getMessage(),
            ]);

            // Duitku stops retrying on 200, but for unknown errors we should send 400
            // For known errors like INVALID, send 400.
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

        [$tenantId, $invoiceCode] = $this->duitkuService->parseMerchantOrderId($rawMerchantOrderId);

        if ($tenantId === 'REG' && $invoiceCode && TenantRegistration::find($invoiceCode)) {
            return redirect()->route('register.status', ['invoice_code' => $invoiceCode]);
        }

        if (!$tenantId || !$invoiceCode) {
            abort(404, 'Order atau Tenant tidak ditemukan.');
        }

        $tenant = Tenant::find($tenantId);
        if (!$tenant) {
            abort(404, 'Order atau Tenant tidak ditemukan.');
        }

        // Ambil domain utama tenant atau fallback ke subdomain default
        $domain = $tenant->domains->first()?->domain
            ?? ($tenantId . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online'));

        $scheme = $request->secure() ? 'https' : 'http';

        return redirect()->away("$scheme://$domain/invoice/$invoiceCode");
    }

    /**
     * Cek status transaksi — endpoint untuk polling dari frontend atau kasir.
     *
     * Gunakan format lengkap {tenantId}~{invoiceCode} untuk performa terbaik.
     */
    public function status(string $invoiceCode): JsonResponse
    {
        // Validasi format
        if (!preg_match('/^[A-Za-z0-9\-~_]+$/', $invoiceCode)) {
            return response()->json(['message' => 'Format invoice code tidak valid.'], 400);
        }

        [$tenantId, $realInvoiceCode] = $this->duitkuService->parseMerchantOrderId($invoiceCode);

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

        $response = [
            'status' => $order->status,
            'invoice_code' => $order->invoice_code,
            'payment_url' => $order->duitku_payment_url,
        ];

        // Jika sudah final, return dari DB
        if (
            in_array($order->status, ['paid', 'cancelled'])
        ) return response()->json($response);

        // Jika masih pending dan ada reference, cek ke Duitku
        if ($order->duitku_reference) {
            try {
                $statusData = $this->duitkuService->checkTransactionStatus($invoiceCode);

                $response['duitku'] = [
                    'statusCode' => $statusData->statusCode ?? null,
                    'statusMessage' => $statusData->statusMessage ?? null,
                ];
            } catch (Throwable $e) {
                Log::error('[Duitku Central] checkTransactionStatus error', [
                    'invoice_code' => $invoiceCode,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json($response);
    }

    /**
     * Fetch Duitku Payment Methods for Onboarding
     */
    public function getPaymentMethods(GetPaymentMethodsInputData $input): JsonResponse
    {
        try {
            $methods = $this->duitkuService->getPaymentMethods((int)$input->amount);
            return $this->successResponse(data: $methods);
        } catch (Throwable $e) {
            Log::error('[Duitku Central] getPaymentMethods error', ['error' => $e->getMessage()]);
            return $this->errorResponse(message: 'Gagal mengambil metode pembayaran.');
        }
    }
}
