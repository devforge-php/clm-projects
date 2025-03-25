<?php 

namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Cache;

class ProfileServices
{
    public function getProfileByUserId($userId)
    {
        $profile = Profile::where('user_id', $userId)->first();
    
        if (!$profile) {
            return null;
        }
    
        $cacheKey = "profile_{$userId}_{$profile->updated_at->timestamp}"; // updated_at bo‘yicha cache key
        return Cache::remember($cacheKey, 3600, function () use ($profile) {
            return $profile->load('user');
        });
    }
    
    

    public function updateProfile($userId, $data)
    {
        $profile = Profile::where('user_id', $userId)->first();
        
        if ($profile) {
            $profile->image = $data['image'] ?? $profile->image;
            $profile->save();
            
            // Cache’ni yangilash uchun eski key'ni o‘chiramiz
            Cache::forget("profile_{$userId}_{$profile->updated_at->timestamp}");
    
            // Yangi cache qo‘shamiz
            $newCacheKey = "profile_{$userId}_{$profile->updated_at->timestamp}";
            Cache::put($newCacheKey, $profile->load('user'), 3600);
        }
        
        return $profile;
    }
    
 
}
