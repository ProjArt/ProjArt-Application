<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


/**
 * @group Authentication
 *
 * APIs pour gérer les authentifications
 */
class AuthController extends Controller
{

    public function register(AuthUserRequest $request)
    {
        $user = User::whereUsername($request->username)->first();

        if ($user) {
            return httpError("This username is already taken");
        }

        $user = User::create($request->all());
        $token = $user->createToken('auth_token')->plainTextToken;

        return httpSuccess("Register validated", [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(AuthUserRequest $request)
    {
        $user = User::whereUsername($request->username)->first();

        if (!$user || ($user->password != $request->password)) { // user not found or password is wrong

            return httpError('Invalid login details');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return httpSuccess("Login validated", [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function me(Request $request)
    {
        return httpSuccess("User found", [
            'user' => $request->user(),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return httpSuccess("Logout succeed");
    }
}
