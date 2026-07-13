<?php

namespace App\Shared\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;

trait ApiResponserTrait
{
    protected function successResponse(
        mixed  $data = [],
        string $message = 'Data fetched successfully',
        int    $code = ResponseAlias::HTTP_OK,
        array  $headers = []
    ): JsonResponse
    {
        // Auto-convert objects (e.g. Spatie Data) to array to prevent "Cannot use object as array" error
        if (is_object($data) && method_exists($data, 'toArray')) {
            $data = $data->toArray();
        }

        if (is_array($data) && isset($data['wrapper-v2']) && isset($data['headers']) && is_array($data['headers'])) {
            $headers = array_merge($headers, $data['headers']);
            $data = $data['records'];
        }

        return response()->json([
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code)->withHeaders($headers);
    }

    protected function failResponse(
        mixed   $errors = [],
        int     $code = ResponseAlias::HTTP_UNPROCESSABLE_ENTITY,
        ?string $message = null
    ): JsonResponse
    {
        return response()->json([
            'success' => false,
            'status' => 'error',
            'message' => $message ?? 'Unprocessable Entity',
            'errors' => $errors
        ], $code);
    }

    protected function errorResponse(
        mixed   $errors = [],
        string  $message = "Internal Server Error",
        int     $code = ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
        Request $request = null
    ): JsonResponse
    {
        $logContext = [
            'code' => $code,
        ];

        // Gather request context if available
        $currentRequest = $request ?? (function_exists('request') ? request() : null);
        if ($currentRequest instanceof Request) {
            $logContext['url'] = $currentRequest->fullUrl();
            $logContext['method'] = $currentRequest->method();
            $logContext['ip'] = $currentRequest->ip();
            $logContext['input'] = $currentRequest->except(['password', 'password_confirmation', 'credential', 'token']);
        }

        // Log the error with appropriate exception detail
        if ($errors instanceof Throwable) {
            Log::error("API Error: {$message} - " . $errors->getMessage(), array_merge($logContext, [
                'exception' => get_class($errors),
                'file' => $errors->getFile(),
                'line' => $errors->getLine(),
                'trace' => $errors->getTraceAsString(),
            ]));
        } else {
            Log::error("API Error: {$message}", array_merge($logContext, [
                'errors' => $errors,
            ]));
        }

        $response = [
            'success' => false,
            'status' => 'error',
            'message' => $message,
        ];

        if (config('app.debug') && ($errors instanceof Throwable)) {
            $response['debug'] = [
                'class' => get_class($errors),
                'file' => $errors->getFile(),
                'line' => $errors->getLine(),
                'message' => $errors->getMessage(),
            ];
        }

        return response()->json($response, $code);
    }
}
