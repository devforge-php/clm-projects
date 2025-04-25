<?php

namespace App\Jobs;

use App\Models\Task;
use App\Notifications\TaskUsersEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use App\Models\User;

class EmailJobUsers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function handle(): void
    {
        // Taskni olish
        $task = $this->task;

        // Foydalanuvchilarni olish
        $users = User::all(); // Bu yerda barcha foydalanuvchilarni olish mumkin

        // Har bir foydalanuvchiga email yuborish
        Notification::send($users, new TaskUsersEmail($task));
    }
}
