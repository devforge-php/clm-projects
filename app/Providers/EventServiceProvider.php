<?php

namespace App\Providers;

use App\Events\AdminEvent;
use App\Events\ProfileEvent;
use App\Events\ProfileUpdated;
use App\Events\ReferralEvent;
use App\Events\TelegramAdmin;
use App\Listeners\ProfileListener;
use App\Listeners\ReferralListener;
use App\Listeners\TelegramListener;
use App\Listeners\UpdateProfileLevel;
use App\Listeners\UsersRegister;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
       ProfileEvent::class => [
        ProfileListener::class,
       ],
       ReferralEvent::class => [
        ReferralListener::class,
    ],
    // 'App\Events\ProfileUpdated' => [
    //     'App\Listeners\UpdateProfileLevel',
    // ],
    TelegramAdmin::class => [
        TelegramListener::class,
    ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
