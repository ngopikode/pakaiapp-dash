<?php

declare(strict_types=1);

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Central\Services\DuitkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class DuitkuApiController extends Controller
{
    public function __construct(
        protected readonly DuitkuService $duitkuService
    )
    {
    }

    public function getPaymentMethods(Request $request): JsonResponse
    {
        if (!config('duitku.enabled')) {
            return response()->json([
                'success' => false,
                'message' => 'Duitku payment gateway is disabled.'
            ], 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $methods = $this->duitkuService->getPaymentMethods((int)$request->amount);
            return response()->json([
                'success' => true,
                'data' => $methods
            ]);
        } catch (Throwable $e) {
            Log::error('[Duitku] getPaymentMethods error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil metode pembayaran.'
            ], 500);
        }
    }
}
