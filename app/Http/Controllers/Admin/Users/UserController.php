<?php

namespace App\Http\Controllers\Admin\Users;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserProfileResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    /**
     * Index - barcha foydalanuvchilarni olish, cache'langan.
     */
    public function index()
    {
        // Cache yordamida foydalanuvchilarni olish
        $users = Cache::remember('users', 60, function () {
            return User::all(); // Barcha foydalanuvchilarni olish
        });

        return UserProfileResource::collection($users); // Resurs orqali yuborish
    }

    /**
     * Show - bitta foydalanuvchini olish, cache'langan.
     */
    public function show($id)
    {
        // Foydalanuvchini ID bo'yicha cache'dan olish
        $user = Cache::remember("user_{$id}", 60, function () use ($id) {
            return User::findOrFail($id); // Foydalanuvchini topish
        });

        return new UserProfileResource($user); // Resurs orqali yuborish
    }

    /**
     * Delete - foydalanuvchini o'chirish va cache'ni yangilash.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Foydalanuvchini o'chirish
        $user->delete();

        // Cache'dan foydalanuvchini o'chirish
        Cache::forget("user_{$id}");

        // Barcha foydalanuvchilarni cache'dan o'chirish
        Cache::forget('users');

        return response()->json(['message' => 'Foydalanuvchi o\'chirildi!']);
    }
}
