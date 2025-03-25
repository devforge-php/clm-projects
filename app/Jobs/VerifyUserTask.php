<?php

namespace App\Jobs;

use App\Models\Profile;
use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyUserTask implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $task;

    public function __construct($user, $task)
    {
        $this->user = $user;
        $this->task = $task;
    }

    public function handle()
    {
        $profile = Profile::firstOrCreate(['user_id' => $this->user->id]);

        $profile->gold = (int) $profile->gold + 1;
        $profile->tasks = (int) $profile->tasks + 1;
        $profile->save();
    }
}

