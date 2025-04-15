<?php

namespace App\Http\Controllers\Click;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(private ClickService $clickService) {}

    public function initiatePayment(Request $request)
    {
        $quantity = $request->input('quantity', 5);

        try {
            $url = $this->clickService->generatePaymentUrl($quantity);
            if (!$url) {
                return response()->json([
                    'message' => 'Limit oshdi yoki miqdor noto‘g‘ri!'
                ], 403);
            }
            return response()->json(['payment_url' => $url]);
        } catch (\Exception $e) {
            Log::error("Initiate xato: " . $e->getMessage());
            return response()->json([
                'message' => 'Xatolik, keyinroq urinib ko‘ring.'
            ], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        try {
            $status = $request->get('payment_status');
            $id     = $request->get('payment_id');

            if (!$status || !$id) {
                Log::warning("Incomplete callback", $request->all());
                return response()->json(['message' => 'Ma’lumot yetarli emas'], 400);
            }

            // ClickService.processPayment uchun parametr nomini moslaymiz
            $request->merge(['transaction_param' => $id]);

            $ok = $this->clickService->processPayment($request);

            return response()->json([
                'message' => $ok ? 'To‘lov muvaffaqiyatli' : 'To‘lov muvaffaqiyatsiz'
            ], $ok ? 200 : 400);

        } catch (\Exception $e) {
            Log::error("Callback xato: " . $e->getMessage());
            return response()->json([
                'message' => 'Callback ishlashda xato'
            ], 500);
        }
    }
}
