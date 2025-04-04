<?php 

namespace App\Services;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProfileServices
{
    public function getProfileByUserId($userId)
    {
        $cacheKey = "profile_{$userId}";

        return Cache::remember($cacheKey, 3600, function () use ($userId) {
            return Profile::where('user_id', $userId)->with('user')->first();
        });
    }

 

    public function updateUserProfile($userId, $data)
    {
        $user = User::findOrFail($userId);
        $user->update($data);

        // Cache’ni yangilash
        Cache::forget("profile_{$userId}");
        Cache::put("profile_{$userId}", $user->profile->load('user'), 3600);

        return $user;
    }
}
