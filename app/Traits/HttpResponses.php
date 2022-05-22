<?php 

namespace App\Traits;

trait HttpResponses
{
    protected function success($message, $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function failure($message, $status = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], $status);
    }
}