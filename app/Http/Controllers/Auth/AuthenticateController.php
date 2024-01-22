<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticateController extends Controller
{
    /**
     * Handle the login action.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $user = \App\Models\User::whereEmail($request->only('email'))->first();
            $token = $user->createToken('auth_token')->plainTextToken;

            return $this->responseJsonData([
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user_information' => $user,
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized',
            'errors' => [
                'email' => ['The provided credentials do not match our records.'],
            ]
        ], 422);
    }

    /**
     * Handle the logout action.
     */
    public function logout(Request $request): JsonResponse
    {
        request()->user()->currentAccessToken()->delete();

        return $this->responseJsonMessage('Logged out');
    }
}
