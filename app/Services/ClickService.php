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
    private string $serviceId;
    private string $merchantId;
    private string $secretKey;

    public function __construct()
    {
        $this->serviceId  = env('CLICK_SERVICE_ID');
        $this->merchantId = env('CLICK_MERCHANT_ID');
        $this->secretKey  = env('CLICK_SECRET_KEY');
    }

    public function generatePaymentUrl(int $quantity): string|false
    {
        // Business logic validation: only allow fixed quantity
        if ($quantity !== 5) {
            return false;
        }

        $user     = auth()->user();
        $cacheKey = "user_{$user->id}_last_purchase";

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $weekAgo = Carbon::now()->subDays(7);
        $count   = Payment::where('user_id', $user->id)
                         ->where('created_at', '>=', $weekAgo)
                         ->count();

        if ($count >= 4) {
            return false;
        }

        DB::beginTransaction();
        try {
            $amount        = $quantity * 200;
            $transactionId = (string) Str::uuid();

            Payment::create([
                'user_id'        => $user->id,
                'type'           => 'gold',
                'quantity'       => $quantity,
                'amount'         => $amount,
                'transaction_id' => $transactionId,
                'status'         => 'pending',
            ]);

            $callbackUrl = route('payment.callback', [], true);
            $returnUrl   = 'https://clmgo.org';

            $paymentUrl = 'https://my.click.uz/services/pay?' . http_build_query([
                'service_id'        => $this->serviceId,
                'merchant_id'       => $this->merchantId,
                'amount'            => $amount,
                'transaction_param' => $transactionId,
                'callback_url'      => $callbackUrl,
                'return_url'        => $returnUrl,
            ]);

            Cache::put($cacheKey, $paymentUrl, now()->addDay());
            DB::commit();

            return $paymentUrl;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("To‘lov URL yaratishda xato: " . $e->getMessage());
            return false;
        }
    }

    public function processPayment($request): bool
    {
        try {
            $transactionId = $request->get('transaction_param');
            $payment       = Payment::where('transaction_id', $transactionId)->first();

            if (! $payment) {
                Log::error("To‘lov topilmadi: {$transactionId}");
                return false;
            }

            $paymentId = $request->get('payment_id');
            $status    = $request->get('payment_status') === '2' ? 'paid' : 'failed';

            $payment->external_payment_id = $paymentId;
            $payment->status              = $status;
            $payment->save();

            if ($status === 'paid') {
                $this->addGoldToUser($payment);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("To‘lovni qayta ishlashda xato: " . $e->getMessage());
            return false;
        }
    }

    public function verifySignature(array $data): bool
    {
        // Ensure required fields for signature
        if (empty($data['merchant_id']) || empty($data['service_id']) || empty($data['transaction_param']) || empty($data['amount']) || empty($data['sign_time']) || empty($data['sign_string'])) {
            return false;
        }

        // Build signature string according to Click API spec
        $payload = $data['merchant_id']
                 . $data['service_id']
                 . $data['transaction_param']
                 . $data['amount']
                 . $data['sign_time']
                 . $this->secretKey;

        $calculated = hash('sha256', $payload);
        return hash_equals($calculated, $data['sign_string']);
    }

    private function addGoldToUser(Payment $payment): void
    {
        $user = User::find($payment->user_id);
        if ($user) {
            $profile = Profile::firstOrCreate(['user_id' => $user->id]);
            $profile->increment('gold', $payment->quantity);
        } else {
            Log::error("Foydalanuvchi topilmadi (ID: {$payment->user_id})");
        }
    }
}
