<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterServices
{
    /**
     * Create a new class instance.
     */
    public function register(array $data)
    {
        return DB::transaction(function () use ($data){

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'customer',
                'status' => 'active',
            ]);

            $user->profile()->create([
                'user_id' => $user->id,
            ]);

            return $user;
        });

    }

    public function login(array $data){
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
            ], 401);
        }

        if($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'User account is not active',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('profile');
        
        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }


}
