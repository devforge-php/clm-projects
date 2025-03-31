<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileServices $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show()
    {
        $userId = auth()->id();
        $profile = $this->profileService->getProfileByUserId($userId);

        if (!$profile) {
            return response()->json(['error' => 'Profile topilmadi'], 404);
        }

        return response()->json(new ProfileResource($profile));
    }
    
    public function update(Request $request)
    {
        $userId = auth()->id();
        $data = $request->only(['image']);

        $profile = $this->profileService->updateProfileImage($userId, $data);

        if (!$profile) {
            return response()->json(['error' => 'Profil rasmni yangilashda xatolik yuz berdi'], 400);
        }

        return response()->json(['data' => new ProfileResource($profile)]);
    }

    public function updateprofile(Request $request)
    {
        $userId = auth()->id();
        $data = $request->only(['firstname', 'lastname', 'username', 'city', 'phone', 'email']);

        $user = $this->profileService->updateUserProfile($userId, $data);

        if (!$user) {
            return response()->json(['error' => 'Foydalanuvchi topilmadi yoki yangilashda xatolik yuz berdi'], 400);
        }

        return response()->json(['data' => new ProfileResource($user->profile)]);
    }
}
