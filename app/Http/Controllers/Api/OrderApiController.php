<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Traits\ApiResponserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class OrderApiController extends Controller
{
    use ApiResponserTrait;

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'customer_name' => 'required|string',
            'order_type' => 'nullable|string',
            'order_info' => 'nullable|string',
            'total_price' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        // Pastikan semua produk yang dipesan masih aktif
        $productIds = collect($request->items)->pluck('product_id')->unique()->values();
        $activeIds = Product::whereIn('id', $productIds)->where('is_active', true)->pluck('id');
        $unavailableIds = $productIds->diff($activeIds)->values();
        if ($unavailableIds->isNotEmpty()) {
            return response()->json([
                'message'         => 'Beberapa produk dalam pesanan sudah tidak tersedia atau habis. Silakan perbarui keranjang Anda.',
                'unavailable_ids' => $unavailableIds,
            ], 422);
        }

        try {
            $order = DB::transaction(function () use ($request) {
                // Generate Invoice Code
                $invoiceCode = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                
                // Map order type. Assuming frontend sends dinein, takeaway, online
                $mappedOrderType = in_array($request->order_type, ['retail', 'dinein', 'takeaway', 'online']) 
                    ? $request->order_type 
                    : 'online';

                $order = Order::create([
                    'invoice_code' => $invoiceCode,
                    'customer_name' => $request->customer_name,
                    'order_type' => $mappedOrderType,
                    'table_number' => $request->order_info, // Map order_info to table_number
                    'subtotal' => $request->total_price,
                    'total_price' => $request->total_price,
                    'status' => 'pending', // Awaiting payment
                    'payment_method' => 'cash',
                ]);

                foreach ($request->items as $item) {
                    $order->items()->create([
                        'product_id' => $item['product_id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'subtotal' => $item['price'] * $item['quantity']
                    ]);
                }

                return $order;
            });

            return $this->successResponse($order, 'Order created successfully.', 201);

        } catch (Throwable $e) {
            return $this->errorResponse($e, 'Failed to create order.', 500, $request);
        }
    }
}
