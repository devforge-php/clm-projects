<?php

namespace App\Services;

use App\Models\SocialUserName;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class SocialMediaServices
{
    // Cache TTL in minutes
    private const TTL = 10;

    public function getAllForUser(int $userId): Collection
    {
        return Cache::remember("social_media_user_{$userId}", self::TTL * 60, function () use ($userId) {
            return SocialUserName::where('user_id', $userId)
                ->select('id', 'user_id', 'telegram_user_name', 'instagram_user_name', 'facebook_user_name', 'youtube_user_name', 'twitter_user_name')
                ->get();
        });
    }

    public function createForUser(int $userId, array $data): ?SocialUserName
    {
        if (SocialUserName::where('user_id', $userId)->exists()) {
            return null;  // Foydalanuvchi allaqachon profil yaratgan bo'lsa, NULL qaytariladi
        }

        $model = SocialUserName::create(['user_id' => $userId] + $data);
        Cache::forget("social_media_user_{$userId}");

        return $model;  // Yangi profil yaratilib, saqlanadi
    }

    public function updateForUser(SocialUserName $model, array $data): SocialUserName
    {
        $model->fill($data);  // O'zgartirishlar qo'llanadi
        if ($model->isDirty()) {  // Agar o'zgartirishlar amalga oshirilgan bo'lsa
            $model->save();  // O'zgartirishlar saqlanadi
            Cache::forget("social_media_user_{$model->user_id}");  // Cache yangilanadi
        }

        return $model;  // Yangilangan model qaytariladi
    }
}
