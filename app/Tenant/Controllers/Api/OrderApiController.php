<?php

namespace App\Tenant\Controllers\Api;

use App\Central\Services\DuitkuService;
use App\Central\Services\MidtransService;
use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\Product;
use App\Tenant\Models\Core\TenantUser;
use App\Tenant\Services\OrderService;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class OrderApiController extends Controller
{
    use ApiResponserTrait;

    public function store(Request $request): JsonResponse
    {
        $gateway = $this->determineGateway($request->input('payment_method', 'CASH'));

        if ($error = $this->validateGateway($gateway)) return $this->failResponse(
            code: ResponseAlias::HTTP_FORBIDDEN,
            message: $error['message']
        );

        $this->validateOrderRequest($request, $gateway);

        if ($error = $this->checkProductAvailability($request->items)) return $this->failResponse(
            errors: ['unavailable_ids' => $error['unavailable_ids']],
            message: $error['message']
        );

        try {
            $order = $this->createOrderInTransaction($request, $gateway);
            return $this->processPaymentGateway($order, $request, $gateway);
        } catch (Throwable $e) {
            Log::error('[OrderAPI] Failed to create order: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->errorResponse(
                errors: $e,
                message: 'Failed to create order.',
                request: $request
            );
        }
    }

    private function determineGateway(string $rawMethod): array
    {
        $raw = strtoupper($rawMethod);
        $isManual = in_array(strtolower($raw), ['cash', 'manual'], true);
        $isMidtrans = strtolower($raw) === 'digital';
        $isDuitku = !$isManual && !$isMidtrans;

        $dbMethod = strtolower($rawMethod);
        $duitkuMethod = null;

        if ($isDuitku) {
            $isQris = in_array($raw, ['QRIS', 'QRISC', 'NQ', 'SP', 'LQ', 'GQ'], true) || str_contains($raw, 'QRIS');
            $dbMethod = $isQris ? 'qris' : 'transfer';
            $duitkuMethod = $raw;
        } elseif ($isMidtrans) {
            // "transfer" digunakan untuk menghindari error ENUM (MySQL Data Truncated)
            // karena orders.payment_method hanya menerima 'cash', 'qris', 'transfer'
            $dbMethod = 'transfer';
        }

        return [
            'type' => $isMidtrans ? 'midtrans' : ($isDuitku ? 'duitku' : 'manual'),
            'raw_method' => $raw,
            'db_method' => $dbMethod,
            'duitku_method' => $duitkuMethod,
        ];
    }

    private function validateGateway(array $gateway): ?array
    {
        if ($gateway['type'] === 'midtrans' && !config('midtrans.server_key')) {
            return ['success' => false, 'message' => 'Pembayaran digital Midtrans sedang tidak aktif.'];
        }

        if ($gateway['type'] === 'duitku' && !config('duitku.enabled')) {
            return ['success' => false, 'message' => 'Pembayaran digital Duitku sedang tidak aktif.'];
        }

        return null;
    }

    private function validateOrderRequest(Request $request, array $gateway): void
    {
        $requiresEmail = $gateway['type'] !== 'manual';

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => $requiresEmail ? 'required|email|max:255' : 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'nullable|string',
            'order_info' => 'nullable|string|max:100',
            'total_price' => 'required|numeric|min:1',
            'payment_method' => 'nullable|string|max:20',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.variant_ids' => 'nullable|array',
            'items.*.variant_ids.*' => 'integer',
            'items.*.extra_ids' => 'nullable|array',
            'items.*.extra_ids.*' => 'integer',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
    }

    private function checkProductAvailability(array $items): ?array
    {
        $productIds = collect($items)->pluck('product_id')->unique()->values();
        $activeIds = Product::whereIn('id', $productIds)->where('is_active', true)->pluck('id');
        $unavailableIds = $productIds->diff($activeIds)->values();

        if ($unavailableIds->isNotEmpty()) {
            return [
                'success' => false,
                'message' => 'Beberapa produk dalam pesanan sudah tidak tersedia atau habis.',
                'unavailable_ids' => $unavailableIds,
            ];
        }

        return null;
    }

    /**
     * @param Request $request
     * @param array $gateway
     * @return Order
     * @throws Throwable
     */
    private function createOrderInTransaction(Request $request, array $gateway): Order
    {
        return DB::transaction(function () use ($request, $gateway) {
            $invoiceCode = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            $mappedOrderType = in_array($request->order_type, ['retail', 'dinein', 'takeaway', 'online', 'delivery'])
                ? $request->order_type : 'retail';

            $orderData = [
                'invoice_code' => $invoiceCode,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'order_type' => $mappedOrderType,
                'is_online' => true,
                'table_number' => $mappedOrderType === 'dinein' ? $request->order_info : null,
                'notes' => $mappedOrderType !== 'dinein' ? $request->order_info : null,
                'payment_method' => $gateway['db_method'],
                'duitku_payment_method' => $gateway['duitku_method'],
                'status' => 'pending',
                'user_id' => Auth::id() ?? null,
            ];

            $orderService = app(OrderService::class);
            return $orderService->processOrder($orderData, $request->items);
        });
    }

    private function processPaymentGateway(Order $order, Request $request, array $gateway): JsonResponse
    {
        if ($gateway['type'] === 'manual') {
            return $this->successResponse(
                data: $order,
                message: 'Order created successfully.',
                code: ResponseAlias::HTTP_CREATED
            );
        }

        $order->load('items');
        $customerDetail = $this->buildCustomerDetail($request);

        try {
            if ($gateway['type'] === 'midtrans') {
                return $this->processMidtrans($order, $customerDetail);
            }

            return $this->processDuitku($order, $customerDetail, $gateway['duitku_method']);

        } catch (Throwable $e) {
            $order->update(['status' => 'cancelled', 'cancellation_note' => ucfirst($gateway['type']) . ' payment initialization failed.']);
            Log::error("[{$gateway['type']}] Payment init failed", [
                'invoice' => $order->invoice_code,
                'error' => $e->getMessage()
            ]);

            return $this->errorResponse(
                message: 'Gagal memproses pembayaran. Silakan coba lagi.',
                code: ResponseAlias::HTTP_BAD_GATEWAY
            );
        }
    }

    private function buildCustomerDetail(Request $request): array
    {
        $customerEmail = $request->customer_email;
        if (empty($customerEmail)) {
            $manager = TenantUser::where('role', 'manager')->first() ?? TenantUser::first();
            $customerEmail = $manager?->email ?? 'noreply@pakaiapp.online';
        }

        return [
            'firstName' => $request->customer_name,
            'lastName' => '',
            'email' => $customerEmail,
            'phoneNumber' => $request->customer_phone ?? '',
            'address' => 'Indonesia',
            'city' => 'Jakarta',
            'postalCode' => '00000',
        ];
    }

    /**
     * @throws Exception
     */
    private function processMidtrans(Order $order, array $customerDetail): JsonResponse
    {
        $service = new MidtransService();
        $snapToken = $service->createSnapToken($order, $customerDetail, tenant()->getTenantKey());

        $order->update(['midtrans_snap_token' => $snapToken]);

        return $this->successResponse(
            data: [
                'order_id' => $order->id,
                'invoice_code' => $order->invoice_code,
                'snap_token' => $snapToken,
            ],
            message: 'Order berhasil dibuat.',
            code: ResponseAlias::HTTP_CREATED
        );
    }

    /**
     * @throws ConnectionException
     */
    private function processDuitku(Order $order, array $customerDetail, string $method): JsonResponse
    {
        $service = app(DuitkuService::class);
        $result = $service->createInvoice($order, $customerDetail, $method, tenant()->getTenantKey());

        $order->update([
            'duitku_reference' => $result->reference,
            'duitku_payment_url' => $result->paymentUrl,
            'duitku_va_number' => $result->vaNumber,
        ]);

        return $this->successResponse(
            data: [
                'order_id' => $order->id,
                'invoice_code' => $order->invoice_code,
                'payment_url' => $result->paymentUrl,
                'va_number' => $result->vaNumber,
                'reference' => $result->reference,
            ],
            message: 'Order berhasil dibuat.',
            code: ResponseAlias::HTTP_CREATED
        );
    }
}
