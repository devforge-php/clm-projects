<?php 

namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Cache;

class ProfileServices
{
    public function getProfileByUserId($userId)
    {
        return Cache::remember("profile_{$userId}", 3600, function () use ($userId) {
            return Profile::with('user') // Profile bilan Userni olish
                ->where('user_id', $userId)
                ->first();
        });
    }
    

    public function updateProfile($userId, $data)
    {
        $profile = Profile::where('user_id', $userId)->first();
        
        if ($profile) {
            $profile->image = $data['image'] ?? $profile->image;
            $profile->save();
    
            // Eski cache’ni o‘chiramiz
            Cache::forget("profile_{$userId}");
    
            // Yangi cache’ni yaratamiz
            Cache::put("profile_{$userId}", $profile->load('user'), 3600);
        }
        
        return $profile;
    }
 
}
