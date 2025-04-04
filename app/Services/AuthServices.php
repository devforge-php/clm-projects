<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessUserRegistration;

class AuthServices
{
    public function register(array $data)
    {
        try {
            // Email unikal bo'lishi kerak
            if (User::where('email', $data['email'])->exists()) {
                abort(422, 'Email already taken');
            }

            // Phone unikal bo'lishi kerak
            if (User::where('phone', $data['phone'])->exists()) {
                abort(422, 'Phone number already taken');
            }

            // Username unikal bo'lishi kerak
            if (User::where('username', $data['username'])->exists()) {
                abort(422, 'Username already taken');
            }

            // Agar barcha unikal tekshiruvlar muvaffaqiyatli bo'lsa, foydalanuvchi yaratish
            ProcessUserRegistration::dispatch($data);

        } catch (\Exception $e) {
            throw new \Exception('User creation failed: ' . $e->getMessage());
        }
    }

    public function login(array $credentials): ?string
    {
        try {
            if (!Auth::attempt($credentials)) {
                return null;
            }

            // Token yaratishda userni oldindan yuklash
            $user = Auth::user()->load('tokens');
            return $user->createToken('authToken')->plainTextToken;

        } catch (\Exception $e) {
            throw new \Exception('Login failed: ' . $e->getMessage());
        }
    }

    public function logout(): void
    {
        try {
            Auth::user()->tokens()->delete();
        } catch (\Exception $e) {
            throw new \Exception('Logout failed: ' . $e->getMessage());
        }
    }

    public function deleteAccount(): void
    {
        try {
            $user = Auth::user();

            if (!$user) {
                abort(401, 'Unauthorized');
            }

            // Foydalanuvchini o‘chirish
            $user->tokens()->delete();
            $user->delete();

        } catch (\Exception $e) {
            throw new \Exception('Failed to delete account: ' . $e->getMessage());
        }
    }
}
