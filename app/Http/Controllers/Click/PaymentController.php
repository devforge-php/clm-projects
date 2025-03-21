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
        $request->validate([
            'type' => 'required|in:gold,silver,diamond',
            'quantity' => 'required|integer|min:1|max:10',
        ]);

        $paymentUrl = $this->clickService->generatePaymentUrl($request->type, $request->quantity);

        return response()->json(['payment_url' => $paymentUrl]);
    }

    public function paymentCallback(Request $request)
    {
        if ($this->clickService->processPayment($request->status, $request->amount)) {
            return response()->json(['message' => 'To‘lov muvaffaqiyatli amalga oshdi']);
        }

        return response()->json(['message' => 'To‘lov amalga oshmadi!'], 400);
    }
}
