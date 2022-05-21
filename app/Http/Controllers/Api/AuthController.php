<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(AuthUserRequest $request)
    {


        // Check if validation pass then create user and auth token. Return the auth token
        $user = User::create($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(AuthUserRequest $request)
    {
        $user = User::whereUsername($request->username)->first();
        if (!$user) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 404);
        }

        if (decrypt($user->first()->password) != $request->password) {
            return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
