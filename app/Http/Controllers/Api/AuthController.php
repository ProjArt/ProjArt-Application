<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;

class AuthController extends Controller
{
    use HttpResponses;

    public function register(AuthUserRequest $request)
    {

        $user = User::firstOrCreate($request->validated());
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success("Register validated", [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(AuthUserRequest $request)
    {
        $user = User::whereUsername($request->username)->first();

        if (!$user || (decrypt($user->first()->password) != $request->password)) { // user not found or password is wrong
            return $this->failure('Invalid login details');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success("Login validated", [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request)
    {
        return $this->success("User found", [
            'user' => $request->user(),
        ]);
    }
}