<?php

namespace App\Http\Controllers\Click;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\Request;

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

        $paymentUrl = $this->clickService->generatePaymentUrl($quantity);

        if (!$paymentUrl) {
            return response()->json(['message' => 'Sotib olish limiti oshib ketdi yoki noto‘g‘ri miqdor kiritildi!'], 403);
        }

        return response()->json(['payment_url' => $paymentUrl]);
    }

    public function paymentCallback(Request $request)
    {
        $result = $this->clickService->processPayment($request);

        if ($result) {
            return response()->json(['message' => 'To‘lov muvaffaqiyatli amalga oshdi']);
        }

        return response()->json(['message' => 'To‘lov amalga oshmadi!'], 400);
    }
}
