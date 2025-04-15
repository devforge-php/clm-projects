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
        $this->serviceId  = env('CLICK_SERVICE_ID');
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->secretKey  = env('CLICK_SECRET_KEY');
    }

    public function generatePaymentUrl($quantity)
    {
        if ($quantity != 5) {
            return false;
        }

        $user     = auth()->user();
        $cacheKey = "user_{$user->id}_last_purchase";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $oneWeekAgo = Carbon::now()->subDays(7);
        $count = Payment::where('user_id', $user->id)
            ->where('created_at', '>=', $oneWeekAgo)
            ->count();

        if ($count >= 4) {
            return false;
        }

        DB::beginTransaction();
        try {
            $amount = $quantity * 200;
            // Biz yaratamiz: Click ga yuboriladigan payment_id
            $clickPaymentId = Str::uuid();

            $payment = Payment::create([
                'user_id'          => $user->id,
                'type'             => 'gold',
                'quantity'         => $quantity,
                'amount'           => $amount,
                'click_payment_id' => $clickPaymentId,
                'status'           => 'pending',
            ]);

            $returnUrl = route('payment.callback', [], true);
            $paymentUrl = "https://my.click.uz/services/pay?" . http_build_query([
                'service_id'        => $this->serviceId,
                'merchant_id'       => $this->merchantId,
                'amount'            => $amount,
                'transaction_param' => $clickPaymentId, 
                'return_url'        => $returnUrl,
            ]);

            Cache::put($cacheKey, $paymentUrl, 86400);
            DB::commit();
            return $paymentUrl;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("To‘lov URL yaratishda xato: " . $e->getMessage());
            return false;
        }
    }

    public function processPayment($request)
    {
        try {
            $clickPaymentId = $request->get('transaction_param');
            $payment = Payment::where('click_payment_id', $clickPaymentId)->first();

            if (!$payment) {
                Log::error("Payment topilmadi: {$clickPaymentId}");
                return false;
            }

            $status = $request->get('payment_status') === '2' ? 'success' : 'failed';
            return $this->handlePaymentStatus($payment, $status);

        } catch (\Exception $e) {
            Log::error("To‘lovni qayta ishlash xato: " . $e->getMessage());
            return false;
        }
    }

    private function handlePaymentStatus(Payment $payment, string $status): bool
    {
        DB::beginTransaction();
        try {
            if ($status === 'success' && $payment->status !== 'paid') {
                $payment->update(['status' => 'paid']);
                $this->addGoldToUser($payment);
            } else {
                $payment->update(['status' => 'failed']);
            }
            DB::commit();
            return $status === 'success';
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Holat yangilash xato: " . $e->getMessage());
            return false;
        }
    }

    private function addGoldToUser(Payment $payment): void
    {
        $user = User::find($payment->user_id);
        if ($user) {
            $profile = Profile::firstOrCreate(['user_id' => $user->id]);
            $profile->increment('gold', $payment->quantity);
        } else {
            Log::error("Foydalanuvchi topilmadi (ID: {$payment->id})");
        }
    }
}
