<?php

if (!function_exists('httpSuccess')) {
    function httpSuccess($message = '', $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status' => $code,
        ], $code);
    }
}

if (!function_exists('httpError')) {
    function httpError($message = '', $data = [], $code = 401)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => $data,
            'status' => $code,
        ], 200);
    }
}
