<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    
    public function register(array $data): array
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user->load('role'),
            'token' => $token,
        ];
    }

    
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou password incorect'],
            ]);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user'  => $user->load('role'),
            'token' => $token,
        ];
    }


    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }

    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }


    public function changePassword(User $user, array $data): void
    {
        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['mode passe incorect'],
            ]);
        }

        $user->update([
            'password' => Hash::make($data['new_password']),
        ]);

        $user->tokens()->delete();
    }

    
    public function profile(User $user): User
    {
        return $user->load('role', 'skills', 'assignments.project');
    }
}