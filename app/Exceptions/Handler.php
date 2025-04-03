<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Http;
use Throwable;
use Symfony\Component\HttpFoundation\Response;

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
        // Agar foydalanuvchi noto‘g‘ri ma’lumot yuborgan bo‘lsa, shunchaki JSON javob qaytaramiz
        if ($this->shouldNotReportToTelegram($exception)) {
            return response()->json([
                'message' => 'Xatolik yuz berdi, iltimos keyinroq urinib ko‘ring.'
            ], $this->getStatusCode($exception));
        }

        return parent::render($request, $exception);
    }

    /**
     * Xatolikni qayd etish va faqat Laravel frameworkdagi xatolarni Telegramga yuborish
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);

        // Foydalanuvchi tomonidan keltirilgan xatolarni yubormaymiz
        if ($this->shouldNotReportToTelegram($exception)) {
            return;
        }

        // Telegram bot tokeni
        $token = '7955493307:AAFPiLc7DtJx3iBIkkRAiDxvlIcJjMeyWrA';

        // Bir nechta chat ID
        $chatIds = [
            '5345557148', // Birinchi admin/guruh
            '7848881961', // Ikkinchi admin/guruh (o‘z chat ID'ingni yoz)
        ];

        // Xatolik haqida xabar
        $message = "🚨 *Laravel Xatolik Yuz Berdi!*\n\n📌 *Xatolik matni:* " . $exception->getMessage();

        // Har bir chat ID'ga xabar yuborish
        foreach ($chatIds as $chatId) {
            Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown' // Matnni formatlash
            ]);
        }
    }

    /**
     * Ushbu xatoni Telegramga yubormaslik kerakmi?
     */
    private function shouldNotReportToTelegram(Throwable $exception): bool
    {
        $statusCode = $this->getStatusCode($exception);

        // Agar xato 400 yoki 401 bo'lsa, Telegramga yubormaymiz
        return in_array($statusCode, [400, 401, 403, 404, 422]);
    }

    /**
     * Exception'dan HTTP status kodini olish
     */
    private function getStatusCode(Throwable $exception): int
    {
        return $exception instanceof \Symfony\Component\HttpKernel\Exception\HttpException
            ? $exception->getStatusCode()
            : Response::HTTP_INTERNAL_SERVER_ERROR;
    }
}