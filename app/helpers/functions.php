<?php

use Illuminate\Http\JsonResponse;
// use Throwable;

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
            'error_details' => $errorDetails,

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
if (! function_exists('handleException')) {

    function handleException(Throwable $th)
    {
        $code = $th->getCode();

        if (!is_int($code) || $code < 100 || $code > 599) {
            $code = 500;
        }

        if ($code === 500) {
            $errorMessage = 'حدث خطأ داخلي في الخادم، يرجى المحاولة لاحقاً.';
            $errorDetails = [
                'message' => $th->getMessage(),
                'file'    => $th->getFile(),
                'line'    => $th->getLine()
            ];
        } else {
            $errorMessage = $th->getMessage();
            $errorDetails = [];
        }

        return sendError($errorMessage, $errorDetails, $code);
    }
}
