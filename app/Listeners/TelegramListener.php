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

        // Telegram bot token
        $botToken = "7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA";

        // Ikki xil chat ID
        $chatIds = [
            "5345557148", // Birinchi admin/guruh
            "7848881961", // Ikkinchi admin/guruh (o‘z chat ID'ingni yoz)
        ];

        $message1 = "📌 Yangi foydalanuvchi ro'yxatdan o'tdi:\n👤 Username: $username\n📞 Telefon: $phone";
        $message2 = "📊 Umumiy foydalanuvchilar soni: $userCount ta";

        // Har bir chat ID ga xabar yuborish
        foreach ($chatIds as $chatId) {
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
}
