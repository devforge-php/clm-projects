<?php

namespace App\Http\Controllers\Click;

use App\Http\Controllers\Controller;
use App\Services\ClickService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private ClickService $clickService;

    public function __construct(ClickService $clickService)
    {
        $this->clickService = $clickService;
    }

    // Frontendga beriladigan URL
    public function initiatePayment(Request $request)
    {
        $url = $this->clickService->generatePaymentUrl($request->user(), $request->quantity ?? 1);
        return response()->json(['payment_url' => $url]);
    }

    // CLICK callback backendga yuboradi
    public function paymentCallback(Request $request)
    {
        // Validatsiya: Ma'lumotlarni tekshiramiz
        $request->validate([
            'user_id'       => ['required', 'exists:users,id'], // Foydalanuvchi mavjudligini tekshiramiz
            'payment_id'    => ['required', 'string'],          // Transaction ID
            'payment_status'=> ['required', 'in:1,2'],         // To'lov holati
        ]);

        // Callbackni qayta ishlash
        $success = $this->clickService->processCallback(
            $request->payment_status,
            $request->payment_id,
            $request->user_id
        );

        return response()->json([
            'message' => $success ? 'Payment successful.' : 'Payment failed.'
        ], $success ? 200 : 400);
    }
    /////// whatttt
}