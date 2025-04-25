<?php

namespace App\Listeners;

use App\Events\TaskEvent;
use App\Jobs\EmailJobUsers;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class TaskListener
{
    public function handle(TaskEvent $event): void
    {
        // Yangi task voqeasini olish
        $task = $event->task;

        // Jobni yuborish
        EmailJobUsers::dispatch($task);
    }
}
