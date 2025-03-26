<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClickService
{
    private $serviceId;
    private $merchantId;
    private $secretKey;

    public function __construct()
    {
        $this->serviceId = env('CLICK_SERVICE_ID');
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->secretKey = env('CLICK_SECRET_KEY');
    }

    public function generatePaymentUrl($quantity)
    {
        // Faqat 5 ta tanga olish mumkin
        if ($quantity != 5) {
            return false;
        }

        // Foydalanuvchi haftasiga 4 martadan ortiq sotib ololmaydi
        $user = auth()->user();
        $oneWeekAgo = Carbon::now()->subDays(7);
        $purchaseCount = Payment::where('user_id', $user->id)
            ->where('created_at', '>=', $oneWeekAgo)
            ->count();

        if ($purchaseCount >= 4) {
            return false;
        }

        // To'lov ma'lumotlarini yaratish
        $amount = $quantity * 5000;
        $transaction_id = Str::uuid();

        Payment::create([
            'user_id' => $user->id,
            'type' => 'gold',
            'quantity' => $quantity,
            'amount' => $amount,
            'transaction_id' => $transaction_id,
            'status' => 'pending'
        ]);

        $returnUrl = route('payment.callback', [], true);

        return "https://my.click.uz/services/pay?service_id={$this->serviceId}&merchant_id={$this->merchantId}&amount={$amount}&transaction_param={$transaction_id}&return_url={$returnUrl}";
    }

    public function processPayment($request)
    {
        if (!$this->verifySignature($request)) {
            Log::warning("Invalid Click signature", $request->all());
            return false;
        }

        $payment = Payment::where('transaction_id', $request->transaction_param)
                          ->where('amount', $request->amount)
                          ->first();

        if (!$payment || $request->status !== "success") {
            return false;
        }

        // **Bu yerda foydalanuvchini olish kerak, chunki auth ishlamaydi**
        $user = User::find($payment->user_id);
        if (!$user) {
            Log::error("User not found for payment ID: " . $payment->id);
            return false;
        }

        $payment->update(['status' => 'completed']);

        // Foydalanuvchining profiliga tangalar qo'shamiz
        $profile = Profile::firstOrCreate(['user_id' => $user->id]);
        $profile->gold += $payment->quantity;
        $profile->save();

        return true;
    }

    private function verifySignature($request)
    {
        $generatedSignature = md5($this->merchantId . $request->transaction_param . $request->amount . $this->secretKey);
        return $generatedSignature === $request->sign_string;
    }
}
