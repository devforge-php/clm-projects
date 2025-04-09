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
    // Rasmni olish (GET)
    public function index()
    {
        $userId = auth()->id();
        $cacheKey = "profile_image_{$userId}";

        $profile = Cache::get($cacheKey);

        if (!$profile) {
            $profile = Profile::where('user_id', $userId)->first();

            if ($profile && $profile->image) {
                Cache::put($cacheKey, $profile, now()->addMinutes(10));
            }
        }

        if (!$profile || !$profile->image) {
            return response()->json(['error' => 'Rasm topilmadi'], 404);
        }

        return new UserProfileResource($profile);
    }

    // Rasm qo‘shish yoki yangilash (PUT)
    public function update(Request $request)
    {
        $userId = auth()->id();

        $profile = Profile::where('user_id', $userId)->first();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profiles', 'public');

            if ($profile) {
                if ($profile->image) {
                    Storage::disk('public')->delete($profile->image);
                }

                $profile->image = $imagePath;
                $profile->save();
            } else {
                $profile = Profile::create([
                    'user_id' => $userId,
                    'image' => $imagePath
                ]);
            }

            Cache::forget("profile_image_{$userId}");

            return new UserProfileResource($profile);
        }

        return response()->json(['error' => 'Rasm fayli topilmadi'], 400);
    }

    // Rasmni o‘chirish (DELETE)
    public function destroy()
    {
        $userId = auth()->id();
        $profile = Profile::where('user_id', $userId)->first();

        if (!$profile || !$profile->image) {
            return response()->json(['error' => 'Rasm topilmadi.'], 404);
        }

        Storage::disk('public')->delete($profile->image);

        $profile->image = null;
        $profile->save();

        Cache::forget("profile_image_{$userId}");

        return response()->json(['message' => 'Rasm muvaffaqiyatli o‘chirildi.']);
    }
}
