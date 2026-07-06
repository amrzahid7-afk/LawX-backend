<?php

use Illuminate\Http\JsonResponse;

if (!function_exists('sendResponse')) {
    function sendResponse($data, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data
        ], $code);
    }
}
if (!function_exists('sendError')) {
    function sendError(string $error, array $errorDetails = [], int $code = 400): JsonResponse
    {
        $response = [
            'status'  => 'error',
            'message' => $error,
        ];
        if (!empty($errorDetails) && config('app.debug')) {
            $response['details'] = $errorDetails;
        }
        $httpCode = (int) $code;
        if ($httpCode < 100 || $httpCode > 599) {
            $httpCode = 500;
        }
        return response()->json($response, $httpCode);
    }
}
