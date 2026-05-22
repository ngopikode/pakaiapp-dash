<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderHistoryApiController extends Controller
{
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
            return response()->json(['success' => true, 'data' => []]);
        }

        $orders = Order::with('items')
            ->whereIn('invoice_code', $invoiceCodes)
            ->orderBy('created_at', 'desc')
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

        return response()->json([
            'success' => true,
            'data' => $orders
        ]);
    }
}
