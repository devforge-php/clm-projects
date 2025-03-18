<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Services\ProfileServices;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileServices $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show()
    {
        $userId = auth()->id(); // User ID ni auth orqali olamiz
        $profile = $this->profileService->getProfileByUserId($userId);
        return response()->json(new ProfileResource($profile));
    }
    
    public function update(Request $request)
    {
        $userId = auth()->id();
        
        // Faqat image maydonini qabul qilish
        $data = $request->only(['image']); // faqat image maydonini olamiz
        
        // Agar file bo'lsa, tasvirni yuklab olamiz va saqlaymiz
        if ($request->hasFile('image')) {
            // Faylni storage/public/profiles papkasiga saqlaymiz
            $imagePath = $request->file('image')->store('profiles', 'public');
            
            // Tasvirni saqlash
            $data['image'] = $imagePath;
        }
        
        // Tasvirni yangilash
        $profile = $this->profileService->updateProfile($userId, $data);
        
        return response()->json($profile);
    }
    
    


}
