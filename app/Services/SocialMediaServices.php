<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\SocialMediaResource;

class SocialMediaServices
{
    public function getAllSocialUsers()
    {
        $userId = auth()->id();

        return Cache::remember("social_user_names_user_{$userId}", 600, function () use ($userId) {
            $results = DB::table('social_user_names')
                ->join('users', 'users.id', '=', 'social_user_names.user_id')
                ->select(
                    'social_user_names.id',
                    'social_user_names.user_id',
                    'social_user_names.telegram_user_name',
                    'social_user_names.instagram_user_name',
                    'social_user_names.facebook_user_name',
                    'social_user_names.youtube_user_name',
                    'social_user_names.twitter_user_name',
                    'users.firstname',
                    'users.lastname'
                )
                ->where('social_user_names.user_id', $userId)
                ->get();

            return SocialMediaResource::collection($results);
        });
    }

    public function createSocialUser(array $data, $userId)
    {
        $exists = DB::table('social_user_names')->where('user_id', $userId)->exists();

        if ($exists) {
            return null; // allaqachon mavjud
        }

        $data['user_id'] = $userId;
        $id = DB::table('social_user_names')->insertGetId($data);

        Cache::forget("social_user_names_user_{$userId}");

        $newData = DB::table('social_user_names')
            ->join('users', 'users.id', '=', 'social_user_names.user_id')
            ->select(
                'social_user_names.*',
                'users.firstname',
                'users.lastname'
            )
            ->where('social_user_names.id', $id)
            ->first();

        return new SocialMediaResource($newData);
    }

    public function updateSocialUser(string $id, array $data)
    {
        $userId = auth()->id();

        $exists = DB::table('social_user_names')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {
            return false; // boshqa user yozuvini o‘zgartirishga urinish
        }

        DB::table('social_user_names')->where('id', $id)->update($data);

        Cache::forget("social_user_names_user_{$userId}");

        return true;
    }
}
