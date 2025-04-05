<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * Xatolikni qaytarish va foydalanuvchiga aniqlik berish
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 422);
        }
    
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'message' => 'Requested resource not found.'
            ], 404);
        }
    
        if (config('app.debug')) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    
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

        // Bir nechta chat ID'lar
        $chatIds = [
            '5345557148', // Admin/guruh chat ID
            '7848881961', // Boshqa admin/guruh (o‘z chat ID'ingni yoz)
        ];

        // Xatolik haqida xabar tayyorlash
        $message = "🚨 *Xatolik yuz berdi!*\n\n📌 *Xatolik matni:* " . $exception->getMessage();

        // Telegramga xabar yuborish
        foreach ($chatIds as $chatId) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown' // Matnni formatlash
            ]);
        }
    }
}
