<?php

namespace App\Listeners;

use App\Events\UserEmailNotifactionEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserEmailNotifactionListener
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
    public function handle(UserEmailNotifactionEvent $event): void
    {
        //
    }
}
