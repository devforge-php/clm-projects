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
        $username = $user->username;
        $phone = $user->phone;

        // Barcha foydalanuvchilar soni
        $userCount = User::count();

        // Telegramga yuborish
        $botToken = "7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA";
        $chatId = "5345557148";

        $message1 = "📌 Yangi foydalanuvchi ro'yxatdan o'tdi:\n👤 Username: $username\n📞 Telefon: $phone";
        $message2 = "📊 Umumiy foydalanuvchilar soni: $userCount ta";

        // Telegram API orqali xabar yuborish
        Http::post("https://api.telegram.org/bot$botToken/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message1,
        ]);

        Http::post("https://api.telegram.org/bot$botToken/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message2,
        ]);
    }
}
