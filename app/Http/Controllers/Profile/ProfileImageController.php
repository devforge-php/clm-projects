<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ProfileImageController extends Controller
{
    public function index()
    {
        try {
            $userId   = auth()->id();
            $cacheKey = "profile_image_{$userId}";
            $profile  = Cache::get($cacheKey);

            if (! $profile) {
                $profile = Profile::where('user_id', $userId)->first();
                if ($profile && $profile->image) {
                    Cache::put($cacheKey, $profile, now()->addMinutes(10));
                }
            }

            if (! $profile || ! $profile->image) {
                return response()->json(['error' => 'Rasm topilmadi'], 404);
            }

            return response()->json([
                'image_url' => asset('storage/' . $profile->image),
            ]);
        } catch (\Throwable $e) {
            Log::error("ProfileImageController@index error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if (config('app.debug')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'Server xatosi'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $userId  = auth()->id();
            $profile = Profile::where('user_id', $userId)->first();

            if ($profile && $profile->image) {
                return response()->json(['error' => 'Sizda allaqachon rasm mavjud.'], 400);
            }

            if (! $request->hasFile('image')) {
                return response()->json(['error' => 'Rasm fayli topilmadi'], 400);
            }

            $imagePath = $request->file('image')->store('profiles', 'public');

            if ($profile) {
                if ($profile->image) {
                    Storage::disk('public')->delete($profile->image);
                }
                $profile->image = $imagePath;
                $profile->save();
            } else {
                Profile::create([
                    'user_id' => $userId,
                    'image'   => $imagePath,
                ]);
            }

            Cache::forget("profile_image_{$userId}");

            return response()->json(['message' => 'Rasm muvaffaqiyatli yuklandi.']);
        } catch (\Throwable $e) {
            Log::error("ProfileImageController@store error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if (config('app.debug')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'Server xatosi'], 500);
        }
    }

    public function destroy()
    {
        try {
            $userId  = auth()->id();
            $profile = Profile::where('user_id', $userId)->first();

            if (! $profile || ! $profile->image) {
                return response()->json(['error' => 'Rasm topilmadi.'], 404);
            }

            Storage::disk('public')->delete($profile->image);
            $profile->image = null;
            $profile->save();

            Cache::forget("profile_image_{$userId}");

            return response()->json(['message' => 'Rasm muvaffaqiyatli o\'chirildi.']);
        } catch (\Throwable $e) {
            Log::error("ProfileImageController@destroy error: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if (config('app.debug')) {
                return response()->json(['error' => $e->getMessage()], 500);
            }

            return response()->json(['error' => 'Server xatosi'], 500);
        }
    }
}
