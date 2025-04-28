<?php

namespace App\GraphQL\Queries;

use App\Models\User;

class UserQuery
{
    public function topUsers()
    {
        return User::with('profile') // Profile bog'liqligini yuklash
            ->whereHas('profile', function ($query) {
                $query->orderByDesc('gold'); // Profile.gold bo'yicha kamayish tartibida saralash
            })
            ->take(10) // Faqat 10 ta foydalanuvchi
            ->get(['id', 'firstname', 'lastname', 'username']); // Kerakli maydonlarni tanlash
    }
}