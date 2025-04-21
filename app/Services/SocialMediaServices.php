<?php

namespace App\Services;

use App\Http\Resources\SocialMediaResource;
use App\Models\SocialUserName;
use Illuminate\Support\Facades\Cache;

class SocialMediaServices   
{
    public function getAllSocialUsers()
    {
        return SocialMediaResource::collection(
            SocialUserName::with(['user:id,firstname,lastname'])
                ->where('user_id', auth()->id()) // Faqat hozirgi foydalanuvchini olish
                ->select(['id', 'user_id', 'telegram_user_name', 'instagram_user_name', 'facebook_user_name', 'youtube_user_name', 'twitter_user_name'])
                ->get()
        );
    }
    
    

    public function createSocialUser(array $data, $userId)
    {
        // Foydalanuvchi uchun yozuv bor-yo‘qligini tekshiramiz
        $existingSocialUser = SocialUserName::where('user_id', $userId)->first();

        if ($existingSocialUser) {
            return null; // Conflict holati
        }

        $socialUser = SocialUserName::create(array_merge($data, [
            'user_id' => $userId,
        ]));

        Cache::forget('social_user_names');
        return new SocialMediaResource($socialUser);
    }

    public function updateSocialUser(string $id, array $data)
    {
        $socialUser = SocialUserName::findOrFail($id);
        $socialUser->update($data);
        
        Cache::forget('social_user_names');
        return $socialUser;
    }
}
