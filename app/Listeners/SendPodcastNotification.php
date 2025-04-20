<?php

namespace App\Listeners;

use App\Events\TelegramAdmin;
use App\Models\Profile;
use Illuminate\Support\Facades\Http;

class SendPodcastNotification
{
    /**
     * Handle the event.
     */
    public function handle(TelegramAdmin $event): void
    {
        // Event’dan kelgan User
        $user = $event->user;

        // Foydalanuvchining profili
        $profile = Profile::where('user_id', $user->id)->first();

        // Agar profil topilsa va barcha shartlar bajarilgan bo‘lsa
        if (
            $profile
            && $profile->tasks     == 1
            && $profile->refferals == 2
            && $profile->level     == 3
        ) {
            // Telegram bot token va chat ID’lar
            $botToken = '7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA';
            $chatIds  = [
                '5345557148',
                '7848881961',
            ];

            // Xabar matni
            $message  = "✅ Foydalanuvchi barcha shartlarni bajardi:\n"
                      . "👤 Ism: {$user->firstname} {$user->lastname}\n"
                      . "📧 Email: {$user->email}\n"
                      . "🆔 Username: {$user->username}\n"
                      . "📱 Telefon: {$user->phone}\n"
                      . "🏙️ Shahar: {$user->city}\n\n"
                      . "🔰 Level: {$profile->level}\n"
                      . "✅ Tasks: {$profile->tasks}\n"
                      . "🔗 Refferals: {$profile->refferals}\n"
                      . "💰 Gold: {$profile->gold}";

            // Orginal va count xabarlarini yuborish
            foreach ($chatIds as $chatId) {
                Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text'    => $message,
                ]);
            }
        }
    }
}
