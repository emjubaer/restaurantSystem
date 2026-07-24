<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthServices;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use ApiResponse;
    public function register(RegisterRequest $request, AuthServices $register)
    {
        $user = $register->register($request->validated());

        $user->load('profile');

        // Todo: Make a trait with name ApiResponse then create success and error method in that trait, then inherit in controller then use it here
        return $this->success('User registered successfully', new UserResource($user), 201);
    }

    public function login(LoginRequest $request, AuthServices $AuthServices)
    {
        try {

            $result = $AuthServices->login($request->validated());

            return $this->success('Login successful', [
                'token' => $result['access_token'],
                'token_type' => $result['token_type'],
            ], 200);
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), null, 401);
            // return $this->error("Login failed", null, 401);
            // return response()->json([
            //     'success' => false,
            //     'message' => $e->getMessage(),
            //     'data' => null,
            // ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success('Logout successfull', null, 200);
    }

    public function profile(Request $request, AuthServices $authServices)
    {
        $data = $authServices->getProfile($request->user());
        return $this->success("Profile fetched successfully", new UserResource($request->user()), 200);
    }

    public function updateProfile(ProfileUpdateRequest $updateRequest, AuthServices $authServices)
    {
        $user = $authServices->updateProfile($updateRequest->user(), $updateRequest->validated());

        return $this->success("Profile updated successfully", new UserResource($user), 200);
    }
}
