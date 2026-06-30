<?php

declare(strict_types=1);

namespace App\Tenant\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Central\Services\DuitkuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Shared\Traits\ApiResponserTrait;

class DuitkuApiController extends Controller
{
    use ApiResponserTrait;
    public function __construct(
        protected readonly DuitkuService $duitkuService
    )
    {
    }

    public function getPaymentMethods(Request $request): JsonResponse
    {
        if (!config('duitku.enabled')) {
            return $this->errorResponse([], 'Duitku payment gateway is disabled.', 403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            $methods = $this->duitkuService->getPaymentMethods((int)$request->amount);
            return $this->successResponse($methods);
        } catch (Throwable $e) {
            Log::error('[Duitku] getPaymentMethods error', ['error' => $e->getMessage()]);
            return $this->errorResponse([], 'Gagal mengambil metode pembayaran.', 500);
        }
    }
}
