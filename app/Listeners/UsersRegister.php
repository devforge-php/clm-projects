<?php

namespace App\Listeners;

use App\Events\AdminEvent;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class UsersRegister
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
    public function handle(AdminEvent $event): void
    {
        $user = $event->user;
        $chatId = '5345557148';
        $botToken = '7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA';

        // Jami userlar sonini olish
        $totalUsers = User::count();

        // Xabar matnini tayyorlash
        $message = "🟢 Yangi foydalanuvchi ro‘yxatdan o‘tdi!\n\n";
        $message .= "🆔 ID: {$user->id}\n";
        $message .= "👤 Username: {$user->username}\n";
        $message .= "👤 Phone: {$user->phone}\n";
        $message .= "👤 Email: {$user->email}\n";
        $message .= "📅 Ro‘yxatdan o‘tgan vaqti: {$user->created_at->format('Y-m-d H:i:s')}\n";
        $message .= "👥 Jami foydalanuvchilar: {$totalUsers} ta";

        // Telegram botga xabar yuborish
        Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
            'parse_mode' => 'Markdown'
        ]);
    }
}
