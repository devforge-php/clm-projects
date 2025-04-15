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
        $quantity = $request->input('quantity', 5); // Default qiymat: 5 tanga

        try {
            $paymentUrl = $this->clickService->generatePaymentUrl($quantity);

            if (!$paymentUrl) {
                return response()->json([
                    'message' => 'Sotib olish limiti oshib ketdi yoki noto‘g‘ri miqdor kiritildi!'
                ], 403);
            }

            return response()->json(['payment_url' => $paymentUrl]);

        } catch (\Exception $e) {
            Log::error("To‘lov yaratishda xatolik: " . $e->getMessage());
            return response()->json([
                'message' => 'To‘lovni yaratishda xatolik yuz berdi, iltimos keyinroq urinib ko‘ring.'
            ], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        try {
            $paymentStatus    = $request->get('payment_status');
            $paymentId        = $request->get('payment_id');
    
            if (!$paymentStatus || !$paymentId) {
                Log::warning("Yetarli ma’lumot yo‘q", $request->all());
                return response()->json(['message' => 'To‘lov ma’lumotlari yetarli emas!'], 400);
            }
    
            $request->merge([
                'payment_status'    => $paymentStatus,
                'transaction_param' => $paymentId, // Biz payment_id ni transaction_param o‘rniga qo‘llayapmiz
            ]);
    
            $result = $this->clickService->processPayment($request);
    
            return response()->json([
                'message' => $result ? 'To‘lov muvaffaqiyatli amalga oshdi' : 'To‘lov amalga oshmadi!'
            ], $result ? 200 : 400);
    
        } catch (\Exception $e) {
            Log::error("Callback xatosi: " . $e->getMessage());
            return response()->json([
                'message' => 'To‘lovni qayta ishlashda xatolik yuz berdi.'
            ], 500);
        }
    }
    
}
