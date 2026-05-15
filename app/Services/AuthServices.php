<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthServices
{
    /**
     * Register User
     */
    public function register(array $data)
    {
        return DB::transaction(function () use ($data) {

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

    /**
     * Login User
     */
    public function login(array $data)
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new \Exception('Invalid credentials');
        }

        if ($user->status !== 'active') {
            throw new \Exception('User account is not active');
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        $user->load('profile');

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function getProfile(User $user)
    {
        return $user->load('profile');

    }

    public function updateProfile(User $user, array $data)
    {
        DB::transaction(function () use ($user, $data) {

            $user->update([
                'name' => $data['name'] ?? $user->name,
                'email' => $data['email'] ?? $user->email,
            ]);

            if (isset($data['profile'])) {
                $user->profile()->update([
                    'bio' => $data['profile']['bio'] ?? $user->profile?->bio,
                    'phone' => $data['profile']['phone'] ?? $user->profile?->phone,
                    'address' => $data['profile']['address'] ?? $user->profile?->address,
                    'date_of_birth' => $data['profile']['date_of_birth'] ?? $user->profile?->date_of_birth,
                    'gender' => $data['profile']['gender'] ?? $user->profile?->gender,
                    'avatar' => $data['profile']['avatar'] ?? $user->profile?->avatar,

                ]);
            }
        });

        return $user->fresh('profile');
    }
}
