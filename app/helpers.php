<?php

if (!function_exists('httpSuccess')) {
    function httpSuccess($message = '', $data = [], $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
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
        ], $code);
    }
}

if (!function_exists('random_color')) {
    function random_color()
    {
        return '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
    }
}
