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
            // Foydalanuvchi allaqachon to'lov qilgan bo'lsa, yangi URL yaratmaslik
            $paymentUrl = $this->clickService->generatePaymentUrl($quantity);
        
            if (!$paymentUrl) {
                return response()->json(['message' => 'Sotib olish limiti oshib ketdi yoki noto‘g‘ri miqdor kiritildi!'], 403);
            }
        
            return response()->json(['payment_url' => $paymentUrl]);
        
        } catch (\Exception $e) {
            return response()->json(['message' => 'To‘lovni yaratishda xatolik yuz berdi, iltimos keyinroq urinib ko‘ring.'], 500);
        }
    }
    
    
    public function paymentCallback(Request $request)
    {
        try {
            // So'rov turini tekshirish va parametrlarni olish
            $paymentStatus = $request->input('payment_status'); // Click-dan kelgan holat
            $transactionParam = $request->input('transaction_param'); // Transaction ID
            $amount = $request->input('amount'); // To'lov miqdori
    
            // Agar payment_status mavjud bo'lmasa, xato qaytarish
            if (!$paymentStatus || !$transactionParam || !$amount) {
                return response()->json(['message' => 'To‘lov ma’lumotlari yetarli emas!'], 400);
            }
    
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
