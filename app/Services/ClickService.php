<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\Payment;
use Illuminate\Support\Str;

class ClickService
{
    private string $serviceId;
    private string $merchantId;
    private string $secretKey;

    public function __construct()
    {
        $this->serviceId  = env('CLICK_SERVICE_ID');
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->secretKey  = env('CLICK_SECRET_KEY');
    }

    public function generatePaymentUrl($user, int $quantity = 1): string
    {
        $amount = 200 * $quantity;
        $transactionId = (string) Str::uuid();
        $signTime = time();

        $signature = hash('sha256', $this->merchantId . $transactionId . $amount . $signTime . $this->secretKey);

        $params = [
            'service_id'        => $this->serviceId,
            'merchant_id'       => $this->merchantId,
            'amount'            => $amount,
            'transaction_param' => $transactionId,
            'callback_url'      => route('payment.callback', [], true),
            'return_url'        => 'https://clmgo.org?status=success&payment_id=' . $transactionId,
            'sign_time'         => $signTime,
            'sign_string'       => $signature,
        ];

        return 'https://my.click.uz/services/pay?' . http_build_query($params);
    }

    public function processCallback(string $status, string $paymentId): bool
    {
        if ($status !== '2') return false;

        // Find payment by transaction_id (payment_id)
        $payment = Payment::where('transaction_param', $paymentId)->first();

        if (!$payment) {
            // Handle invalid payment ID if not found
            return false;
        }

        // Mark the payment as successful
        $payment->markAsPaid();

        // Update user profile, adding gold
        $profile = Profile::firstOrCreate(['user_id' => $payment->user_id]);
        $profile->increment('gold', 2); // Example: Add 2 coins for successful payment

        return true;
    }
}
