<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserProfileResource;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ProfileImageController extends Controller
{
    // Rasmni olish
    public function index()
    {
        $userId = auth()->id();
        
        // Keshdan foydalanuvchi rasm ma'lumotlarini olish
        $cacheKey = "profile_image_{$userId}";
        $profile = Cache::get($cacheKey);
    
        // Agar keshda mavjud bo'lmasa, bazadan olish
        if (!$profile) {
            $profile = Profile::where('user_id', $userId)->first();
            
            if ($profile && $profile->image) {
                // Keshga saqlash
                Cache::put($cacheKey, $profile, now()->addMinutes(10));  // 10 daqiqa davomida saqlash
            }
        }
    
        if (!$profile || !$profile->image) {
            return response()->json(['error' => 'Rasm topilmadi'], 404);
        }
    
        return new UserProfileResource($profile);
    }

    // Yangi rasm qo'shish
    public function store(Request $request)
    {
        $userId = auth()->id();
    
        // Foydalanuvchi profili borligini tekshirish
        $profile = Profile::where('user_id', $userId)->first();
    
        // Agar rasm mavjud bo'lsa, yangisini yaratmasin
        if ($profile && $profile->image) {
            return response()->json(['error' => 'Sizda allaqachon rasm mavjud. Faqat bitta rasm saqlanadi.'], 400);
        }
    
        // Yangi rasmni saqlash
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');
    
            // Agar profil mavjud bo'lsa, rasmni yangilash
            if ($profile) {
                // Eski rasmni o'chirish
                if ($profile->image) {
                    Storage::disk('public')->delete($profile->image);
                }
    
                // Yangi rasmni saqlash
                $profile->image = $imagePath;
                $profile->save();
            } else {
                // Agar profil bo'lmasa, yangi profil yaratish
                Profile::create([
                    'user_id' => $userId,
                    'image' => $imagePath
                ]);
            }
    
            // Keshni yangilash
            Cache::forget("profile_image_{$userId}");  // Eski keshni o'chirish
            // Yangilangan profilni resurs sifatida qaytarish
            return new UserProfileResource(Profile::where('user_id', $userId)->first());
        }
    
        return response()->json(['error' => 'Rasm fayli topilmadi'], 400);
    }
    

    // Rasmni yangilash
 
    

    public function destroy()
    {
        $userId = auth()->id();
        $profile = Profile::where('user_id', $userId)->first();
    
        if (!$profile || !$profile->image) {
            return response()->json(['error' => 'Rasm topilmadi.'], 404);
        }
    
        // Eski rasmni o'chirish
        Storage::disk('public')->delete($profile->image);
    
        // Profilni yangilash
        $profile->image = null;
        $profile->save();
    
        // Keshni yangilash
        Cache::forget("profile_image_{$userId}");  // Keshni o'chirish
    
        return response()->json(['message' => 'Rasm muvaffaqiyatli o\'chirildi.']);
    }
    
}
