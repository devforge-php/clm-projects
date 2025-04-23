<?php

namespace App\Services;

use App\Models\SocialUserName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class SocialMediaServices
{
    // Cache TTL (time-to-live) in minutes
    private const TTL = 10;

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
        // Agar foydalanuvchida allaqachon profil mavjud bo'lsa, NULL qaytariladi
        if (SocialUserName::where('user_id', $userId)->exists()) {
            return null; 
        }

        // Yangi ijtimoiy tarmoq profilini yaratish
        $model = SocialUserName::create(['user_id' => $userId] + $data);

        // Cache'ni tozalash, yangilash
        Cache::forget("social_media_user_{$userId}");

        return $model;  // Yangi profil qaytariladi
    }

    // Profilni yangilash
    public function updateForUser(SocialUserName $model, array $data): SocialUserName
    {
        $model->fill($data);  // Ma'lumotlarni to'ldirish
        if ($model->isDirty()) {  // Agar ma'lumotlar o'zgargan bo'lsa
            $model->save();  // Saqlash
            Cache::forget("social_media_user_{$model->user_id}");  // Cache yangilanadi
        }

        return $model;  // Yangilangan model qaytariladi
    }
}
