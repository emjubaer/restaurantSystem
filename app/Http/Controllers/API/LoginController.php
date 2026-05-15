<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\RegisterServices;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function register(RegisterRequest $request, RegisterServices $register)
    {
        $user = $register->register($request->validated());

        $user->load('profile');

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => new UserResource($user),
        ], 201);
    }

    public function login(LoginRequest $request, RegisterServices $registerServices)
    {
        try {
            $result = $registerServices->login($request->validated());

            return response()->json([
                'success'    => true,
                'message'    => 'Login successful',
                'data' => [
                    'user' => $result['user'],
                    'token'      => $result['access_token'],
                    'token_type' => $result['token_type'],
                ]

            ]);
            
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
