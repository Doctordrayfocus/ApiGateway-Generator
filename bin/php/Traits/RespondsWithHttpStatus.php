<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait RespondsWithHttpStatus
{
    protected function success($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data
        ], $status);
    }

    protected function failure($message, $status = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ], $status);
    }

    protected function validationFail($errors = [], $status = 422)
    {
        return response()->json([
            'success' => false,
            'errors' => $errors
        ], $status);
    }
}
