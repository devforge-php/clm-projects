<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Xatolik yuz berganda, foydalanuvchiga qaytariladigan javob
     */
    public function render($request, Throwable $exception)
    {
        return response()->json([
            'message' => 'Xatolik yuz berdi, iltimos keyinroq urinib ko‘ring.'
        ], 500);
    }

    /**
     * Xatolikni qayd etish va Telegramga yuborish
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);

        // Telegram bot tokeni
        $token = '7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA';

        // Bir nechta chat ID
        $chatIds = [
            '5345557148', // Birinchi admin/guruh
            '7848881961', // Ikkinchi admin/guruh (o‘z chat ID'ingni yoz)
        ];

        // Xatolik haqida xabar
        $message = "🚨 *Xatolik yuz berdi!*\n\n📌 *Xatolik matni:* " . $exception->getMessage();

        // Har bir chat ID'ga xabar yuborish
        foreach ($chatIds as $chatId) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown' // Matnni formatlash
            ]);
        }
    }
}
