<?php

namespace App\Services;

use App\Models\SocialUserName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class SocialMediaServices
{
    private const TTL = 10; // Cache TTL (time-to-live) in minutes

    // Foydalanuvchi uchun barcha ijtimoiy tarmoq ma'lumotlarini olish
    public function getAllForUser(int $userId): Collection
    {
        return Cache::remember("social_media_user_{$userId}", self::TTL * 60, function () use ($userId) {
            return SocialUserName::where('user_id', $userId)
                ->select('id', 'user_id', 'telegram_user_name', 'instagram_user_name', 'facebook_user_name', 'youtube_user_name', 'twitter_user_name')
                ->get();
        });
    }

    // Yangi profil yaratish
    public function createForUser(int $userId, array $data): ?SocialUserName
    {
        // Foydalanuvchida allaqachon profil mavjudmi?
        if (SocialUserName::where('user_id', $userId)->exists()) {
            return null;
        }

        // Yangi profil yaratish
        $model = SocialUserName::create(['user_id' => $userId] + $data);

        // Cache'ni yangilash
        Cache::forget("social_media_user_{$userId}");

        return $model;
    }

    // Profilni yangilash
    public function updateForUser(SocialUserName $model, array $data): SocialUserName
    {
        $model->fill($data);
        if ($model->isDirty()) {
            $model->save();
            // Cache'ni yangilash
            Cache::forget("social_media_user_{$model->user_id}");
        }

        return $model;
    }

    // Profilni o'chirish
    public function deleteForUser(SocialUserName $socialUser): bool
    {
        // Profilni o'chirish
        $deleted = $socialUser->delete();

        // Cache'ni yangilash
        if ($deleted) {
            Cache::forget("social_media_user_{$socialUser->user_id}");
        }

        return $deleted;
    }
}
