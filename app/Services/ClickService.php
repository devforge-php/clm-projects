<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
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
        try {
            if ($quantity != 5) {
                return false;
            }

            $user = auth()->user();
            $cacheKey = "user_{$user->id}_last_purchase";

            // Foydalanuvchi allaqachon to'lov qilgan bo'lsa, yangi URL yaratmaslik kerak
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            $oneWeekAgo = Carbon::now()->subDays(7);
            $purchaseCount = Payment::where('user_id', $user->id)
                ->where('created_at', '>=', $oneWeekAgo)
                ->count();

            // Agar foydalanuvchi 4 ta to'lovdan ko'p qilgan bo'lsa, yangi to'lov yaratmaslik kerak
            if ($purchaseCount >= 4) {
                return false;
            }

            DB::beginTransaction();
            $amount = $quantity * 200;
            $transaction_id = Str::uuid();

            // Yangi to'lov yaratish
            $payment = Payment::create([
                'user_id' => $user->id,
                'type' => 'gold',
                'quantity' => $quantity,
                'amount' => $amount,
                'transaction_id' => $transaction_id,
                'status' => 'pending'
            ]);

            $returnUrl = route('payment.callback', [], true);
            $paymentUrl = "https://my.click.uz/services/pay?service_id={$this->serviceId}&merchant_id={$this->merchantId}&amount={$amount}&transaction_param={$transaction_id}&return_url={$returnUrl}";

            // URLni Cache'ga saqlash
            Cache::put($cacheKey, $paymentUrl, 86400); // 24 soat davomida saqlash

            DB::commit();
            return $paymentUrl;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Payment yaratishda xatolik: " . $e->getMessage());
            return false;
        }
    }

    public function processPayment($request)
    {
        try {
            // Signature tekshirish
            if (!$this->verifySignature($request)) {
                Log::warning("Invalid Click signature", $request->all());
                return false;
            }

            // To'lovni topish
            $payment = Payment::where('transaction_id', $request->transaction_param)
                              ->where('amount', $request->amount)
                              ->first();

            if (!$payment) {
                Log::error("Payment not found: " . $request->transaction_param);
                return false;
            }

            // To'lov holatini qayta ishlash
            $status = $request->input('payment_status') === '2' ? 'success' : 'failed';
            return $this->handlePaymentStatus($payment, $status);

        } catch (\Exception $e) {
            Log::error("To‘lovni qayta ishlashda xatolik: " . $e->getMessage());
            return false;
        }
    }

    private function handlePaymentStatus($payment, $status)
    {
        DB::beginTransaction();
        try {
            if ($status === "success") {
                if ($payment->status !== 'paid') {
                    $payment->update(['status' => 'paid']);
                    $this->addGoldToUser($payment);
                }
            } else {
                $payment->update(['status' => 'failed']);
            }

            DB::commit();
            return $status === "success";

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("To‘lovni qayta ishlashda xatolik: " . $e->getMessage());
            return false;
        }
    }

    private function addGoldToUser($payment)
    {
        $user = User::find($payment->user_id);
        if ($user) {
            $profile = Profile::firstOrCreate(['user_id' => $user->id]);
            $profile->gold += $payment->quantity;
            $profile->save();
        } else {
            Log::error("User not found for payment: " . $payment->id);
        }
    }

    private function verifySignature($request)
    {
        $generatedSignature = md5(
            $this->merchantId .
            $request->transaction_param .
            $request->amount .
            $this->secretKey
        );

        return $generatedSignature === $request->sign_string;
    }
}