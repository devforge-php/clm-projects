<?php

namespace App\Services;

use App\Models\Profile;
use Illuminate\Support\Facades\Auth;

class ClickService
{
    private $serviceId = "";
    private $merchantId = "";
    private $secretKey = "";

    public function generatePaymentUrl($type, $quantity)
    {
        $amount = $quantity * 1000; // 1 tanga = 1000 so‘m
        $transaction_id = uniqid();

        session([
            'payment' => [
                'user_id' => Auth::id(),
                'type' => $type,
                'quantity' => $quantity,
                'amount' => $amount,
                'transaction_id' => $transaction_id
            ]
        ]);

        return "https://my.click.uz/services/pay?service_id={$this->serviceId}&merchant_id={$this->merchantId}&amount={$amount}&transaction_param={$transaction_id}&return_url=" . route('payment.callback');
    }

    public function processPayment($status, $amount)
    {
        $paymentData = session('payment');

        if (!$paymentData || $status !== "success" || $amount != $paymentData['amount']) {
            return false;
        }

        $profile = Profile::firstOrCreate(['user_id' => $paymentData['user_id']]);
        $profile->{$paymentData['type']} += $paymentData['quantity'];
        $profile->save();

        session()->forget('payment');

        return true;
    }
}
