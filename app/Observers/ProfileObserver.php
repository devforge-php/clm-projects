<?php

namespace App\Observers;

use App\Models\Profile;
use Illuminate\Support\Facades\Cache;

class ProfileObserver
{
    /**
     * Handle the Profile "created" event.
     */
    public function created(Profile $profile): void
    {
        //
    }

    public function updated(Profile $profile)
    {
        $userId = $profile->user_id;
        
        // Eski cache key'ni o‘chiramiz
        Cache::forget("profile_{$userId}_{$profile->getOriginal('updated_at')->timestamp}");

        // Yangi cache qo‘shamiz
        $newCacheKey = "profile_{$userId}_{$profile->updated_at->timestamp}";
        Cache::put($newCacheKey, $profile->load('user'), 3600);
    }

    /**
     * Handle the Profile "deleted" event.
     */
    public function deleted(Profile $profile): void
    {
        //
    }

    /**
     * Handle the Profile "restored" event.
     */
    public function restored(Profile $profile): void
    {
        //
    }

    /**
     * Handle the Profile "force deleted" event.
     */
    public function forceDeleted(Profile $profile): void
    {
        //
    }
}
