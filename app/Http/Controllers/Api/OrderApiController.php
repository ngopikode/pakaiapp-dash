<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\TenantUser;
use App\Services\DuitkuService;
use App\Services\MidtransService;
use App\Traits\ApiResponserTrait;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class OrderApiController extends Controller
{
    use ApiResponserTrait;

    public function store(Request $request): JsonResponse
    {
        $gateway = $this->determineGateway($request->input('payment_method', 'CASH'));

        if ($error = $this->validateGateway($gateway)) {
            return $this->failResponse([], 403, $error['message']);
        }

        $this->validateOrderRequest($request, $gateway);

        if ($error = $this->checkProductAvailability($request->items)) {
            return $this->failResponse(['unavailable_ids' => $error['unavailable_ids']], 422, $error['message']);
        }

        try {
            $order = $this->createOrderInTransaction($request, $gateway);
            return $this->processPaymentGateway($order, $request, $gateway);
        } catch (Throwable $e) {
            Log::error('[OrderAPI] Failed to create order: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return $this->errorResponse($e, 'Failed to create order.', 500, $request);
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

            $storeSetting = StoreSetting::first();
            $taxRate = $storeSetting && $storeSetting->is_tax_active ? (float)$storeSetting->tax_rate : 0.00;
            $serviceRate = $storeSetting && $storeSetting->is_service_charge_active ? (float)$storeSetting->service_charge_rate : 0.00;

            $recalculatedItems = [];
            $realSubtotal = 0;

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                $basePrice = (float)$product->price;
                $cost = 0;
                $variantIds = $item['variant_ids'] ?? [];
                if (!empty($item['variant_id']) && !in_array($item['variant_id'], $variantIds)) {
                    $variantIds[] = $item['variant_id'];
                }

                $validVariantsObjects = [];
                if (!empty($variantIds)) {
                    $variants = \App\Models\ProductVariant::with('recipes.rawMaterial')
                        ->whereIn('id', $variantIds)
                        ->where('product_id', $product->id)
                        ->lockForUpdate()
                        ->get();
                    
                    if ($product->selection_type === 'multiple') {
                        $validVariantsObjects = $variants;
                    } else {
                        $firstVariant = $variants->first();
                        if ($firstVariant) {
                            $basePrice = (float)($firstVariant->active_discount_price ?? $firstVariant->price);
                            $cost = $firstVariant->cost;
                            $validVariantsObjects = collect([$firstVariant]);
                        }
                    }
                }

                $extraPrice = 0;
                $validExtraIds = [];
                if (!empty($item['extra_ids'])) {
                    $extras = \App\Models\ProductExtra::whereIn('id', $item['extra_ids'])
                        ->where('product_id', $product->id)
                        ->get();
                    $extraPrice = $extras->sum('price');
                    $validExtraIds = $extras->pluck('id')->toArray();
                }

                $realItemPrice = $basePrice + $extraPrice;
                $realItemSubtotal = $realItemPrice * (int)$item['quantity'];
                
                $realSubtotal += $realItemSubtotal;

                $recalculatedItems[] = [
                    'product_id' => $product->id,
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'quantity' => (int)$item['quantity'],
                    'price' => $realItemPrice,
                    'cost' => $cost,
                    'subtotal' => $realItemSubtotal,
                    'selected_variants' => collect($validVariantsObjects)->pluck('id')->toArray(),
                    'selected_extras' => $validExtraIds,
                    'valid_variants_objects' => collect($validVariantsObjects)
                ];
            }

            if (empty($recalculatedItems)) {
                throw new \Exception('Semua produk dalam pesanan tidak valid atau tidak ditemukan.');
            }

            $serviceChargeAmount = round(($serviceRate / 100) * $realSubtotal);
            $taxAmount = round(($taxRate / 100) * ($realSubtotal + $serviceChargeAmount));
            $totalPrice = $realSubtotal + $serviceChargeAmount + $taxAmount;

            $order = Order::create([
                'invoice_code' => $invoiceCode,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'order_type' => $mappedOrderType,
                'is_online' => true,
                'table_number' => $mappedOrderType === 'dinein' ? $request->order_info : null,
                'notes' => $mappedOrderType !== 'dinein' ? $request->order_info : null,
                'subtotal' => $realSubtotal,
                'tax_amount' => $taxAmount,
                'service_charge_amount' => $serviceChargeAmount,
                'tax_percentage' => $taxRate,
                'service_charge_percentage' => $serviceRate,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'payment_method' => $gateway['db_method'],
                'duitku_payment_method' => $gateway['duitku_method'],
            ]);

            foreach ($recalculatedItems as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'cost' => $item['cost'],
                    'subtotal' => $item['subtotal'],
                    'selected_variants' => json_encode($item['selected_variants']),
                    'selected_extras' => json_encode($item['selected_extras']),
                ]);

                foreach ($item['valid_variants_objects'] as $variant) {
                    $variant->decrement('stock', $item['quantity']);

                    if (tenant('store_type') === 'resto') {
                        foreach ($variant->recipes as $recipe) {
                            if ($recipe->rawMaterial) {
                                $recipe->rawMaterial->decrement('stock', $recipe->quantity_used * $item['quantity']);
                            }
                        }
                    }
                }
            }

            return $order;
        });
    }

    private function processPaymentGateway(Order $order, Request $request, array $gateway): JsonResponse
    {
        if ($gateway['type'] === 'manual') {
            return $this->successResponse($order, 'Order created successfully.', 201);
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

            return $this->errorResponse([], 'Gagal memproses pembayaran. Silakan coba lagi.', 502);
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

        return $this->successResponse([
            'order_id' => $order->id,
            'invoice_code' => $order->invoice_code,
            'snap_token' => $snapToken,
        ], 'Order berhasil dibuat.', 201);
    }

    /**
     * @throws ConnectionException
     */
    private function processDuitku(Order $order, array $customerDetail, string $method): JsonResponse
    {
        $service = new DuitkuService();
        $result = $service->createInvoice($order, $customerDetail, $method, tenant()->getTenantKey());

        $order->update([
            'duitku_reference' => $result['reference'],
            'duitku_payment_url' => $result['payment_url'],
            'duitku_va_number' => $result['va_number'],
        ]);

        return $this->successResponse([
            'order_id' => $order->id,
            'invoice_code' => $order->invoice_code,
            'payment_url' => $result['payment_url'],
            'va_number' => $result['va_number'],
            'reference' => $result['reference'],
        ], 'Order berhasil dibuat.', 201);
    }
}
