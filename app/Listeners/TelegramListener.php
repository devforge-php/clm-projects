<?php

namespace App\Listeners;

use App\Events\TelegramAdmin;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class TelegramListener
{
    /**
     * Handle the event.
     */
    public function handle(TelegramAdmin $event): void
    {
        // Foydalanuvchi ma'lumotlari
        $user = $event->user;

        // Telegram bot token
        $botToken = "7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA";

        // Chat ID lar (Admin yoki guruh ID'lari)
        $chatIds = [
            "5345557148", // Birinchi admin/guruh
            "7848881961", // Ikkinchi admin/guruh
        ];

        // Yuboriladigan xabar
        $message = "📥 Yangi foydalanuvchi ro'yxatdan o'tdi:\n" .
                   "👤 Ism: {$user->firstname} {$user->lastname}\n" .
                   "📧 Email: {$user->email}\n" .
                   "🆔 Username: {$user->username}\n" .
                   "📱 Telefon: {$user->phone}\n" .
                   "🏙️ Shahar: {$user->city}";

        // Jami foydalanuvchilar soni
        $userCount = User::count();
        $countMessage = "👥 Umumiy foydalanuvchilar soni: {$userCount} ta";

        // Har bir chatga xabarlarni yuborish
        foreach ($chatIds as $chatId) {
            Http::post("https://api.telegram.org/bot$botToken/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
            ]);

            Http::post("https://api.telegram.org/bot$botToken/sendMessage", [
                'chat_id' => $chatId,
                'text' => $countMessage,
            ]);
        }
    }
}
