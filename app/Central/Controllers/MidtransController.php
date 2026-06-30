<?php

namespace App\Central\Controllers;

use App\Central\Services\MidtransService;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MidtransController extends Controller
{

    public function __construct(protected readonly MidtransService $midtransService)
    {
    }

    /**
     * Handle notification/webhook dari Midtrans.
     * Endpoint ini diletakkan di central domain.
     */
    public function notification(Request $request): JsonResponse
    {
        Log::info('[Midtrans] Notification webhook received', $request->all());

        try {
            $this->midtransService->handleWebhook($request->all());

            return response()->json(['message' => 'OK']);
        } catch (Throwable $e) {
            Log::error('[Midtrans] Notification processing error', [
                'error' => $e->getMessage()
            ]);

            $status = $e->getCode() ?: 500;
            // Midtrans stops retrying if we send 200, so we send 200 for known handled cases that we want to stop
            if (in_array($e->getMessage(), ['TENANT_NOT_FOUND', 'REG_NOT_FOUND'])) {
                return response()->json(['message' => $e->getMessage()]);
            }

            return response()->json(['message' => $e->getMessage()], $status == 0 ? 500 : $status);
        }
    }
}
