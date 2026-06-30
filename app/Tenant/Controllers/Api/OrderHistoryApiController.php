<?php

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Tenant\Models\Core\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Shared\Traits\ApiResponserTrait;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OrderHistoryApiController extends Controller
{
    use ApiResponserTrait;

    public function index(Request $request): JsonResponse
    {
        $invoiceCodes = $request->input('invoices', []);
        
        if (empty($invoiceCodes) || !is_array($invoiceCodes)) {
            // Attempt to parse if sent as JSON string in GET
            if (is_string($request->input('invoices'))) {
                $decoded = json_decode($request->input('invoices'), true);
                if (is_array($decoded)) {
                    $invoiceCodes = $decoded;
                }
            }
        }
        
        if (!is_array($invoiceCodes) || empty($invoiceCodes)) {
            return $this->successResponse(data: []);
        }

        // Limit maximum invoices to prevent payload spam/DoS
        $invoiceCodes = array_slice($invoiceCodes, 0, 50);

        // Sanitize to only string values to prevent injection
        $invoiceCodes = array_filter($invoiceCodes, 'is_string');

        if (empty($invoiceCodes)) {
            return $this->successResponse(data: []);
        }

        $orders = Order::with('items')
            ->whereIn('invoice_code', $invoiceCodes)
            ->orderBy('created_at', 'desc')
            ->limit(50) // Double protection on DB query
            ->get()
            ->map(function ($order) {
                return [
                    'invoiceCode' => $order->invoice_code,
                    'date' => $order->created_at->toIso8601String(),
                    'totalRaw' => (float)$order->total_price,
                    'orderType' => $order->order_type,
                    'status' => $order->status,
                    'paymentMethod' => $order->payment_method,
                    'paymentName' => $order->formatted_payment_method,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'name' => $item->product_name,
                            'qty' => $item->quantity,
                            'price' => (float)$item->price,
                        ];
                    }),
                ];
            });

        return $this->successResponse(data: $orders);
    }
}
