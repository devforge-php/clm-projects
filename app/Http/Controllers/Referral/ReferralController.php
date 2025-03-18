<?php

namespace App\Http\Controllers\Referral;

use App\Http\Controllers\Controller;
use App\Services\ReferralService;
use App\Services\ReferralServices;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    protected $referralService;

    public function __construct(ReferralServices $referralService)
    {
        $this->referralService = $referralService;
    }

    // Faqat foydalanuvchining o‘ziga tegishli referral kodlarni olish
    public function index()
    {
        $referrals = $this->referralService->getUserReferrals();
        return response()->json($referrals);
    }

    public function useReferralCode(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:referrals,referral_code'
        ]);

        $result = $this->referralService->useReferralCode($request->referral_code);

        return response()->json(['message' => $result['message'] ?? $result['error']], $result['status']);
    }
}
