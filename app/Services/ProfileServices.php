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

    public function updateProfileImage($userId, $data)
    {
        $cacheKey = "profile_image_update_{$userId}";

        // 1 kunda 1 marta rasm yangilashni tekshirish
        if (Cache::has($cacheKey)) {
            return response()->json(['error' => 'Siz 1 kunda faqat 1 marta rasmni yangilashingiz mumkin'], 429);
        }

        $profile = Profile::where('user_id', $userId)->first();

        if ($profile && isset($data['image'])) {
            $imagePath = $data['image']->store('profiles', 'public');

            // Eski rasmni o‘chirish
            if ($profile->image) {
                Storage::disk('public')->delete($profile->image);
            }

            $profile->image = $imagePath;
            $profile->save();

            // Cache'ni yangilash
            Cache::forget("profile_{$userId}");
            Cache::put($cacheKey, true, now()->addDay()); // 1 kunga cache qo‘shish
        }

        return $profile;
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
