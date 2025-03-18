<?php

namespace App\Listeners;

use App\Events\ReferralEvent;
use App\Models\Referral;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class ReferralListener
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
    public function handle(ReferralEvent $event): void
    {
        Referral::create([
            'user_id' => $event->user->id,
            'referral_code' => strtoupper(Str::random(6)) // Masalan: ABC123
        ]);
    }
}
