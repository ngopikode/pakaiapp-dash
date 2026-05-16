<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
        if (isset($data['wrapper-v2']) && isset($data['headers']) && is_array($data['headers'])) {
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
