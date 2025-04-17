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
        // Input validation: only allow integer quantity = 5
        $request->validate([
            'quantity' => ['required', 'integer', 'in:5'],
        ]);

        $quantity = $request->input('quantity');

        try {
            $url = $this->clickService->generatePaymentUrl($quantity);

            if (!$url) {
                return response()->json([
                    'message' => 'Limit oshgan yoki noto‘g‘ri miqdor kiritildi.'
                ], 403);
            }

            return response()->json(['payment_url' => $url]);
        } catch (\Throwable $e) {
            Log::error("To‘lov boshlanishida xato: " . $e->getMessage());
            return response()->json([
                'message' => 'Xatolik yuz berdi. Iltimos, keyinroq urinib ko‘ring.'
            ], 500);
        }
    }

    public function paymentCallback(Request $request)
    {
        // Validate required callback parameters and their formats
        $request->validate([
            'transaction_param' => ['required', 'uuid'],
            'payment_status'    => ['required', 'in:1,2'],
            // assuming signature field name 'sign_string'
            'sign_string'       => ['required', 'string'],
        ]);

        $transactionId = $request->get('transaction_param');
        $statusCode    = $request->get('payment_status');

        // Verify signature for security
        if (! $this->clickService->verifySignature($request->all())) {
            Log::warning('Click callback signature mismatch', $request->all());
            return response()->json(['message' => 'Unauthorized callback'], 403);
        }

        try {
            $ok = $this->clickService->processPayment($request);

            return response()->json([
                'message' => $ok ? 'To‘lov muvaffaqiyatli amalga oshirildi.' : 'To‘lov amalga oshmadi.'
            ], $ok ? 200 : 400);
        } catch (\Throwable $e) {
            Log::error("Callback jarayonida xato: " . $e->getMessage());
            return response()->json([
                'message' => 'To‘lovni qayta ishlashda xato yuz berdi.'
            ], 500);
        }
    }
}