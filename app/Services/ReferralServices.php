<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\Referral;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;

class ReferralServices
{
    public function getUserReferrals()
    {
        $user = Auth::user();
        return Referral::where('user_id', $user->id)->get();
    }

    public function useReferralCode($referralCode)
    {
        $user = Auth::user();

        // O'zining referral kodidan foydalana olmaydi
        $userReferral = Referral::where('user_id', $user->id)->first();
        if ($userReferral && $userReferral->referral_code === $referralCode) {
            return ['error' => 'Siz o\'z referal kodingizni ishlata olmaysiz!', 'status' => 403];
        }

        // Foydalanuvchi oldin referal kod ishlatganmi? (Cache orqali tekshiramiz)
        if (Cache::has("user_{$user->id}_used_referral")) {
            return ['error' => 'Siz allaqachon referal kod ishlatgansiz!', 'status' => 403];
        }

        // Referal kod egasini topish
        $referral = Referral::where('referral_code', $referralCode)->first();
        if (!$referral) {
            return ['error' => 'Referal kod topilmadi!', 'status' => 404];
        }

        $referrer = $referral->user;
        if (!$referrer) {
            return ['error' => 'Referal kod egasi topilmadi!', 'status' => 404];
        }

        // Referrerga 1 gold qo'shish va refferals ni oshirish
        $profile = Profile::where('user_id', $referrer->id)->first();
        if ($profile) {
            $profile->gold += 1;
            $profile->refferals += 1;
            $profile->save();
        } else {
            Profile::create([
                'user_id' => $referrer->id,
                'gold' => 1,
                'tasks' => 0,
                'refferals' => 1,
                'level' => 0
            ]);
        }

        // ❗️ Profil cache'ni yangilaymiz
        Cache::forget("profile_{$referrer->id}");

        // Foydalanuvchi ushbu koddan foydalanganini belgilaymiz (cache bilan)
        Cache::put("user_{$user->id}_used_referral", true, now()->addDays(30));

        return [
            'message' => 'Referal kod muvaffaqiyatli ishlatildi, foydalanuvchiga 1 gold qo\'shildi!',
            'status' => 200
        ];
    }
}
