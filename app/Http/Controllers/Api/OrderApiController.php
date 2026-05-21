<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\TenantUser;
use App\Services\DuitkuService;
use App\Traits\ApiResponserTrait;
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
        // Tentukan apakah payment method ini perlu diproses via Duitku (dilakukan sebelum validasi
        // agar bisa pakai conditional required)
        $rawPaymentMethod = strtoupper($request->input('payment_method', 'CASH'));
        $isManual         = in_array(strtolower($rawPaymentMethod), ['cash', 'qris', 'transfer'], true);
        $willUseDuitku    = !$isManual;

        if ($willUseDuitku && !config('duitku.enabled')) {
            return response()->json([
                'success' => false,
                'message' => 'Pembayaran digital Duitku sedang tidak aktif.',
            ], 403);
        }

        // Toko online = order_type 'online' — email wajib sebagai bukti pembayaran.
        // Kasir (retail/dinein/takeaway) — email opsional, kasir nggak perlu nanya email customer.
        $isOnlineOrder = strtolower($request->input('order_type', '')) === 'online';

        $request->validate([
            'customer_name'      => 'required|string|max:255',
            'customer_email'     => $isOnlineOrder && $willUseDuitku
                                    ? 'required|email|max:255'
                                    : 'nullable|email|max:255',
            'customer_phone'     => 'nullable|string|max:20',
            'order_type'         => 'nullable|string',
            'order_info'         => 'nullable|string|max:100',
            'total_price'        => 'required|numeric|min:1',
            'payment_method'     => 'nullable|string|max:20',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.name'       => 'required|string|max:255',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.price'      => 'required|numeric|min:0',
        ]);

        // Pastikan semua produk yang dipesan masih aktif
        $productIds = collect($request->items)->pluck('product_id')->unique()->values();
        $activeIds = Product::whereIn('id', $productIds)->where('is_active', true)->pluck('id');
        $unavailableIds = $productIds->diff($activeIds)->values();

        if ($unavailableIds->isNotEmpty()) {
            return response()->json([
                'message' => 'Beberapa produk dalam pesanan sudah tidak tersedia atau habis. Silakan perbarui keranjang Anda.',
                'unavailable_ids' => $unavailableIds,
            ], 422);
        }

        // Tentukan apakah payment method ini perlu diproses via Duitku
        $requestedMethod = $rawPaymentMethod; // sudah strtoupper di atas
        $isDuitku        = $willUseDuitku;

        if ($isDuitku) {
            // Deteksi jenis QRIS Duitku secara dinamis
            $isQrisType = in_array($requestedMethod, ['QRIS', 'QRISC', 'NQ', 'SP', 'LQ', 'GQ'], true) || str_contains($requestedMethod, 'QRIS');
            $dbPaymentMethod = $isQrisType ? 'qris' : 'transfer';
        } else {
            $dbPaymentMethod = strtolower($rawPaymentMethod);
        }

        try {
            $order = DB::transaction(function () use ($request, $dbPaymentMethod, $requestedMethod, $isDuitku) {
                // Generate Invoice Code — unik per hari + random suffix
                $invoiceCode = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

                $mappedOrderType = in_array($request->order_type, ['retail', 'dinein', 'takeaway', 'online'])
                    ? $request->order_type
                    : 'online';

                $order = Order::create([
                    'invoice_code' => $invoiceCode,
                    'customer_name' => $request->customer_name,
                    'customer_phone' => $request->customer_phone,
                    'order_type' => $mappedOrderType,
                    'table_number' => $request->order_info,
                    'subtotal' => $request->total_price,
                    'total_price' => $request->total_price,
                    'status' => 'pending',
                    'payment_method' => $dbPaymentMethod,
                    // Simpan kode metode Duitku yang dipilih jika ada
                    'duitku_payment_method' => $isDuitku ? $requestedMethod : null,
                ]);

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity'],
                    ]);
                }

                return $order;
            });

            // Jika payment via Duitku, buat invoice dan kembalikan payment URL
            if ($isDuitku) {
                // Load items untuk dikirim ke Duitku
                $order->load('items');

                // Resolve email: pakai customer_email jika ada,
                // fallback ke email manager/owner tenant agar tidak error di Duitku.
                $customerEmail = $request->customer_email;
                if (empty($customerEmail)) {
                    $manager = TenantUser::where('role', 'manager')->first()
                        ?? TenantUser::first();
                    $customerEmail = $manager?->email ?? 'noreply@pakaiapp.online';

                    Log::info('[Duitku] Email customer kosong, fallback ke email manager', [
                        'fallback_email' => $customerEmail,
                    ]);
                }

                $customerDetail = [
                    'firstName' => $request->customer_name,
                    'lastName' => '',
                    'email' => $customerEmail,
                    'phoneNumber' => $request->customer_phone ?? '',
                    'address' => 'Indonesia',
                    'city' => 'Jakarta',
                    'postalCode' => '00000',
                ];

                try {
                    $duitkuService = new DuitkuService();
                    // Pass tenant()->id agar merchantOrderId = "{tenantId}~{invoiceCode}"
                    $duitkuResult = $duitkuService->createInvoice(
                        $order,
                        $customerDetail,
                        $requestedMethod,
                        tenant()->getTenantKey() // ID tenant dari Stancl Tenancy
                    );

                    // Simpan reference dan payment URL ke order
                    $order->update([
                        'duitku_reference' => $duitkuResult['reference'],
                        'duitku_payment_url' => $duitkuResult['payment_url'],
                        'duitku_va_number' => $duitkuResult['va_number'],
                    ]);

                    return response()->json([
                        'success' => true,
                        'message' => 'Order berhasil dibuat. Lanjutkan ke halaman pembayaran.',
                        'data' => [
                            'order_id' => $order->id,
                            'invoice_code' => $order->invoice_code,
                            'payment_url' => $duitkuResult['payment_url'],
                            'va_number' => $duitkuResult['va_number'],
                            'reference' => $duitkuResult['reference'],
                        ],
                    ], 201);

                } catch (Throwable $e) {
                    // Jika Duitku gagal, batalkan order agar tidak ada order pending tanpa payment URL
                    $order->update(['status' => 'cancelled', 'cancellation_note' => 'Duitku invoice gagal dibuat']);
                    Log::error('[Duitku] Gagal buat invoice, order dibatalkan', [
                        'invoice_code' => $order->invoice_code,
                        'error' => $e->getMessage(),
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal memproses pembayaran. Silakan coba lagi.',
                    ], 502);
                }
            }

            // Untuk pembayaran non-Duitku (cash), return seperti biasa
            return $this->successResponse($order, 'Order created successfully.', 201);

        } catch (Throwable $e) {
            return $this->errorResponse($e, 'Failed to create order.', 500, $request);
        }
    }
}
