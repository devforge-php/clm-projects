<?php

namespace App\Http\Controllers\Click;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $clickService;

    public function __construct(ClickService $clickService)
    {
        $this->clickService = $clickService;
    }

    public function initiatePayment(Request $request)
    {
        $quantity = $request->input('quantity', 5); // Default 5 tanga

        try {
            $paymentUrl = $this->clickService->generatePaymentUrl($quantity);

            if (!$paymentUrl) {
                return response()->json(['message' => 'Sotib olish limiti oshib ketdi yoki noto‘g‘ri miqdor kiritildi!'], 403);
            }

            return response()->json(['payment_url' => $paymentUrl]);

        } catch (\Exception $e) {
            Log::error("To‘lov yaratishda xatolik: " . $e->getMessage());
            return response()->json(['message' => 'To‘lovni yaratishda xatolik yuz berdi, iltimos keyinroq urinib ko‘ring.'], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        try {
            // So'rov turini tekshirish va parametrlarni olish
            $paymentStatus = $request->method() === 'GET' ? $request->query('payment_status') : $request->input('payment_status');
            $transactionParam = $request->method() === 'GET' ? $request->query('transaction_param') : $request->input('transaction_param');
            $amount = $request->method() === 'GET' ? $request->query('amount') : $request->input('amount');
            $signString = $request->method() === 'GET' ? $request->query('sign_string') : $request->input('sign_string');

            // Kerakli parametrlar mavjudligini tekshirish
            if (!$paymentStatus || !$transactionParam || !$amount || !$signString) {
                Log::warning("Callback received incomplete data", $request->all());
                return response()->json(['message' => 'To‘lov ma’lumotlari yetarli emas!'], 400);
            }

            // Parametrlarni request obyektiga qo'shish
            $request->merge([
                'payment_status' => $paymentStatus,
                'transaction_param' => $transactionParam,
                'amount' => $amount,
                'sign_string' => $signString,
            ]);

            // To'lovni qayta ishlash
            $result = $this->clickService->processPayment($request);

            if ($result) {
                return response()->json(['message' => 'To‘lov muvaffaqiyatli amalga oshdi']);
            }

            return response()->json(['message' => 'To‘lov amalga oshmadi!'], 400);

        } catch (\Exception $e) {
            Log::error("To‘lovni qayta ishlashda xatolik: " . $e->getMessage());
            return response()->json(['message' => 'To‘lovni qayta ishlashda xatolik yuz berdi, iltimos keyinroq urinib ko‘ring.'], 500);
        }
    }
}