<?php

namespace App\Listeners;

use App\Events\ProfileUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        
        // Agar level o'zgargan bo'lsa, update qilamiz
        if ($profile->level !== $level) {
            $profile->update(['level' => $level]);
        }
    }
    
    
}
