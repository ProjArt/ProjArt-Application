<?php 

namespace App\Traits;

trait HttpResponses
{

    private function response($message, $data = [], $success = false)
    {
        return [
            'success' => $success,
            'message' => $message,
            'data' => $data,
        ];
    }

    protected function success($message, $data = [], $status = 200)
    {
        return response()->json($this->response($message, $data, true), $status);
    }

    protected function failure($message, $data = [], $status = 401)
    {
        return response()->json($this->response($message, $data, false), $status);
    }
}