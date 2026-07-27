<?php

declare(strict_types=1);

namespace App\Tenant\Controllers\Api;

use App\Central\Services\DuitkuService;
use App\Http\Controllers\Controller;
use App\Shared\Traits\ApiResponserTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

class DuitkuApiController extends Controller
{
    use ApiResponserTrait;

    public function __construct(
        protected readonly DuitkuService $duitkuService
    ) {}

    public function getPaymentMethods(Request $request): JsonResponse
    {
        if (!config('duitku.enabled')) {
            return $this->errorResponse(errors: [], message: 'Duitku payment gateway is disabled.', code: ResponseAlias::HTTP_FORBIDDEN);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        try {
            $methods = $this->duitkuService->getPaymentMethods((int) $request->amount);

            return $this->successResponse(data: $methods);
        } catch (Throwable $e) {
            Log::error('[Duitku] getPaymentMethods error', ['error' => $e->getMessage()]);

            return $this->errorResponse(errors: [], message: 'Gagal mengambil metode pembayaran.', code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
