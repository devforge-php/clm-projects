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
        $userId   = auth()->id();
        $cacheKey = "profile_image_{$userId}";

        // Teg bilan o‘qish
        $profile = Cache::tags('profiles')->get($cacheKey);

        if (! $profile) {
            $profile = Profile::where('user_id', $userId)->first();

            if ($profile && $profile->image) {
                // Teg bilan saqlash (10 daqiqa)
                Cache::tags('profiles')->put($cacheKey, $profile, now()->addMinutes(10));
            }
        }

        if (! $profile || ! $profile->image) {
            return response()->json(['error' => 'Rasm topilmadi'], 404);
        }

        return new UserProfileResource($profile);
    }

    // Rasm qo‘shish yoki yangilash (PUT)
    public function update(Request $request)
    {
        $userId   = auth()->id();
        $cacheKey = "profile_image_{$userId}";

        if (! $request->hasFile('image')) {
            return response()->json(['error' => 'Rasm fayli topilmadi'], 400);
        }

        // Yangi yoki mavjud profilni olish
        $profile = Profile::firstOrNew(['user_id' => $userId]);

        // Faylni saqlash
        $imagePath = $request->file('image')->store('profiles', 'public');

        // Eskisini o‘chirish
        if ($profile->exists && $profile->image) {
            Storage::disk('public')->delete($profile->image);
        }

        // Profilda yangilash
        $profile->image = $imagePath;
        $profile->save();

        // Teg bilan yangilash
        Cache::tags('profiles')->put($cacheKey, $profile, now()->addMinutes(10));

        return new UserProfileResource($profile);
    }

    // Rasmni o‘chirish (DELETE)
    public function destroy()
    {
        $userId   = auth()->id();
        $cacheKey = "profile_image_{$userId}";

        $profile = Profile::where('user_id', $userId)->first();

        if (! $profile || ! $profile->image) {
            return response()->json(['error' => 'Rasm topilmadi.'], 404);
        }

        // Faylni o‘chirish
        Storage::disk('public')->delete($profile->image);

        // Profilda tozalash
        $profile->image = null;
        $profile->save();

        // Teg bilan o‘chirish
        Cache::tags('profiles')->forget($cacheKey);

        return response()->json(['message' => 'Rasm muvaffaqiyatli o‘chirildi.']);
    }
}
