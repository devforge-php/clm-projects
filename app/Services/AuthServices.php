<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Jobs\ProcessUserRegistration;

class AuthServices
{
    public function register(array $data): User
    {
        // Email unikal tekshirishni cache qilish
        if (Cache::has('email_' . $data['email'])) {
            abort(422, 'Email already taken');
        }

        if (User::where('email', $data['email'])->exists()) {
            Cache::put('email_' . $data['email'], true, 60);
            abort(422, 'Email already taken');
        }

        // Background jobga user yaratish
        ProcessUserRegistration::dispatch($data);

        return new User([
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'username' => $data['username'],
            'city' => $data['city'],
            'phone' => $data['phone'],
            'email' => $data['email']
        ]);
    }

    public function login(array $credentials): ?string
    {
        if (!Auth::attempt($credentials)) {
            return null;
        }

        // Token yaratishda userni oldindan yuklash
        $user = Auth::user()->load('tokens');
        return $user->createToken('authToken')->plainTextToken;
    }

    public function logout(): void
    {
        Auth::user()->tokens()->delete();
    }
}
