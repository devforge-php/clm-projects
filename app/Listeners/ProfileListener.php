<?php

namespace App\Listeners;

use App\Events\ProfileEvent;
use App\Models\Profile;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ProfileListener
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
    public function handle(ProfileEvent $event): void
    {
        $event->user->profile()->create([
            'gold' => 0,
            'silver' => 0,
            'diamond' => 0,
            'level' => 1,
        ]);
        
    }
}
