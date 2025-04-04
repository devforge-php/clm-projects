<?php

namespace App\Jobs;

use App\Models\User;
use App\Events\TelegramAdmin;
use App\Events\ProfileEvent;
use App\Events\ReferralEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Hash;

class ProcessUserRegistration implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        $user = User::create([
            'firstname' => $this->data['firstname'],
            'lastname' => $this->data['lastname'],
            'username' => $this->data['username'],
            'city' => $this->data['city'],
            'phone' => $this->data['phone'],
            'email' => $this->data['email'],
            'password' => Hash::make($this->data['password']),
            'role' => $this->data['role'] ?? 'user', // Rolega default qiymat qo'yish
        ]);

        // Eventlarni yuborish
        TelegramAdmin::dispatch($user);
        ProfileEvent::dispatch($user);
        ReferralEvent::dispatch($user);
    }
}
