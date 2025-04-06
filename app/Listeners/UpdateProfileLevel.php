<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateProfileLevel
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProfileUpdated $event)
    {
        $profile = $event->profile;
        
        // Yangi levelni hisoblash
        $level = (int) $profile->gold;  // Gold miqdori bo'yicha levelni aniqlash
        Log::info("ProfileUpdated fired: gold={$profile->gold}, current_level={$profile->level}, new_level={$level}");
        // Agar level o'zgargan bo'lsa, update qilamiz
        if ($profile->level !== $level) {
            $profile->update(['level' => $level]);
        }
    }
    
    
}
