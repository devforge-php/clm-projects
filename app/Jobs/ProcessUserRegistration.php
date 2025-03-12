<?php

namespace App\Jobs;

use App\Models\User;
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
        User::create([
            'firstname' => $this->data['firstname'],
            'lastname' => $this->data['lastname'],
            'username' => $this->data['username'],
            'city' => $this->data['city'],
            'phone' => $this->data['phone'],
            'email' => $this->data['email'],
            'password' => Hash::make($this->data['password'], ['rounds' => 8]),
        ]);
    }
}
