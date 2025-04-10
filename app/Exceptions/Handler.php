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
    //    code saved messageres
    }
}
