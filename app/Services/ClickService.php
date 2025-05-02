<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ClickService
{
    private string $serviceId;
    private string $merchantId;
    private string $secretKey;

    public function __construct()
    {
        $this->serviceId  = '66131';
        $this->merchantId = '17518';
        $this->secretKey  = 'JMGCujQ1zL1Du9q';
    }

    public function generatePaymentUrl($user, int $quantity = 1): string
    {
        $amount = 1600 * $quantity; // 1 tanga = 1600 so‘m
        $transactionId = uniqid(); // Unique transaction ID
        $signTime = time();
    
        $signature = hash('sha256', $this->merchantId . $transactionId . $amount . $signTime . $this->secretKey);
    
        $params = [
            'service_id'        => $this->serviceId,
            'merchant_id'       => $this->merchantId,
            'amount'            => $amount,
            'transaction_param' => $transactionId,
            'callback_url'      => route('payment.callback', [], true),
            'return_url'        => 'https://clmgo.org/paymentResult',
            'sign_time'         => $signTime,
            'sign_string'       => $signature,
        ];
    
        return 'https://my.click.uz/services/pay?' . http_build_query($params);
    }
    
    
    public function processCallback(string $status, string $paymentId, int $userId): bool
    {
        // 1. Cache orqali qayta callback ni oldini olish
        $cacheKeyUser = "user_payment_{$userId}";
        $cacheKeyPayment = "payment_id_{$paymentId}";

        if (Cache::has($cacheKeyUser) || Cache::has($cacheKeyPayment)) {
            // Agar allaqachon to'lov qilingan bo'lsa, rad etamiz
            return false;
        }

        // 2. Agar to'lov muvaffaqiyatli bo'lmagan bo'lsa
        if ($status !== '2') {
            return false;
        }

        // 3. Cache-ga user_id va payment_id ni saqlash (48 soat)
        Cache::put($cacheKeyUser, true, now()->addHours(48));
        Cache::put($cacheKeyPayment, true, now()->addHours(48));

        // 4. User profile ni topamiz yoki yangi profile yaratamiz
        $profile = DB::table('profiles')->where('user_id', $userId)->first();

        if (!$profile) {
            // Agar profile yo'q bo'lsa, yangi profile yaratamiz
            DB::table('profiles')->insert([
                'user_id' => $userId,
                'gold'    => 5, // Boshlang'ich gold miqdori
                'level'   => 5, // Boshlang'ich level
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Agar profile mavjud bo'lsa, gold va level ni yangilaymiz
            DB::table('profiles')
                ->where('user_id', $userId)
                ->update([
                    'gold'  => DB::raw('gold + 5'), // Gold ni 5 ga oshiramiz
                    'level' => DB::raw('level * 5'), // Level ni 5 ga ko'paytiramiz
                    'updated_at' => now(),
                ]);
        }

        return true;
    }
}