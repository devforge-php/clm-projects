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
    
        // Oldingi levelni olish
        $level = $profile->level;
    
        // Yangi levelni hisoblash
        if ($profile->gold >= 5) {
            $level += 5;
        }
    
    
        // Agar level o'zgargan bo'lsa, update qilamiz
        if ($profile->level !== $level) {
            $profile->update(['level' => $level]);
        }
    }
    
}
