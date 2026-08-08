<?php

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use App\Tenant\Data\CreatePreOrderData;
use App\Tenant\Models\Core\DeliveryZone;
use App\Tenant\Models\Core\StoreSetting;
use App\Tenant\Services\PreOrderService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class PreOrderApiController extends Controller
{
    use ApiResponserTrait;

    protected ?PreOrderService $preOrderService = null;

    protected function preOrderService(): PreOrderService
    {
        return $this->preOrderService ??= app(PreOrderService::class);
    }

    /**
     * Kembalikan konfigurasi pre-order: earliest delivery date, cutoff time, dan daftar zona aktif.
     */
    public function config(): JsonResponse
    {
        $setting = StoreSetting::cached();

        return $this->successResponse(data: [
            'earliest_delivery_date' => $this->preOrderService()
                ->resolveEarliestDeliveryDate($setting)
                ->toDateString(),
            'cutoff_time' => $setting?->cutoff_time,
            'zones' => DeliveryZone::where('is_active', true)->get(),
        ]);
    }

    /**
     * Kembalikan slot pengiriman aktif beserta ketersediaan kuota untuk tanggal tertentu.
     */
    public function slots(Request $request): JsonResponse
    {
        $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
        ]);

        return $this->successResponse(
            data: $this->preOrderService()->getSlotAvailability(Carbon::parse($request->date))
        );
    }

    /**
     * Buat pre-order baru. Hanya bisa diakses jika tenant dalam mode direct_wa.
     */
    public function store(Request $request): JsonResponse
    {
        $setting = StoreSetting::cached();

        if (!$setting || (!$setting->isWaCheckoutActive() && !$setting->isPreorderActive())) {
            return $this->failResponse(
                code: ResponseAlias::HTTP_FORBIDDEN,
                message: 'Fitur pre-order tidak aktif untuk toko ini.'
            );
        }

        $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_address' => ['required', 'string', 'max:1000'],
            'delivery_date' => [$setting->isPreorderActive() ? 'required' : 'nullable', 'date', 'date_format:Y-m-d'],
            'delivery_slot_id' => [$setting->isPreorderActive() ? 'required' : 'nullable', 'integer'],
            'delivery_zone_id' => [$setting->isPreorderActive() ? 'required' : 'nullable', 'integer'],
            'payment_method' => ['required', 'string', 'in:qris,cash'],
            'notes' => ['nullable', 'string', 'max:500'],
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.variant_id' => ['nullable', 'integer'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'items.*.note' => ['nullable', 'string', 'max:255'],
        ]);

        try {
            $dto = new CreatePreOrderData(
                customerName: $request->customer_name,
                customerAddress: $request->customer_address,
                deliveryDate: $request->delivery_date ?? Carbon::today('Asia/Jakarta')->toDateString(),
                deliverySlotId: (int)$request->delivery_slot_id,
                deliveryZoneId: (int)$request->delivery_zone_id,
                paymentMethod: $request->payment_method,
                customerPhone: $request->customer_phone,
                notes: $request->notes,
            );

            $order = $this->preOrderService()->createPreOrder($dto, $request->items);

            $waMessage = $this->preOrderService()->buildWaMessage($order);
            $waNumber = preg_replace('/[^0-9]/', '', $setting->whatsapp_number ?? '');

            // Format URL dasar tanpa encode pesannya. Frontend akan meng-encodeURIComponent.
            $waUrl = $waNumber ? 'https://wa.me/' . $waNumber . '?text=' : null;

            return $this->successResponse(
                data: [
                    'order_id' => $order->id,
                    'invoice_code' => $order->invoice_code,
                    'wa_url' => $waUrl,
                    'wa_message' => $waMessage,
                ],
                message: 'Pesanan berhasil dibuat.',
                code: ResponseAlias::HTTP_CREATED
            );
        } catch (Throwable $e) {
            Log::error('[PreOrderAPI] Failed to create pre-order: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse(
                errors: $e,
                message: $e->getMessage(),
                code: ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
                request: $request
            );
        }
    }
}
