<?php

namespace App\Http\Controllers\SocailMedia;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialMediaStoreRequest;
use App\Http\Requests\SocialMediaUpdateRequest;
use App\Http\Resources\SocialMediaResource;
use App\Models\SocialUserName;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SocialMediaController extends Controller
{
    public function __construct()
    {
        $this->middleware('throttle:60,1');
    }

    // Profilni olish
    public function index(): JsonResponse
    {
        // Cache'dan o'qish
        $data = Cache::remember('social_user_' . auth()->id(), now()->addMinutes(10), function () {
            return SocialUserName::where('user_id', auth()->id())->get();
        });

        // Agar profil topilmasa, xatolik qaytariladi
        if ($data->isEmpty()) {
            return response()->json(['message' => 'Sizning profilingiz topilmadi.'], 404);
        }

        return response()->json(SocialMediaResource::collection($data));
    }

    // Yangi profil yaratish
    public function store(SocialMediaStoreRequest $request): JsonResponse
    {
        $dto = $request->validated();

        // Agar foydalanuvchi allaqachon profil yaratgan bo'lsa, xatolik qaytariladi
        $existingProfile = SocialUserName::where('user_id', auth()->id())->first();
        if ($existingProfile) {
            return response()->json([
                'message' => 'Siz allaqachon ijtimoiy tarmoqlar profilingizni kiritgansiz.'
            ], 409);
        }

        // Profil yaratish
        $socialUser = SocialUserName::create([
            'user_id' => auth()->id(),
            'telegram_user_name' => $dto['telegram_user_name'] ?? null,
            'instagram_user_name' => $dto['instagram_user_name'] ?? null,
            'facebook_user_name' => $dto['facebook_user_name'] ?? null,
            'youtube_user_name' => $dto['youtube_user_name'] ?? null,
            'twitter_user_name' => $dto['twitter_user_name'] ?? null,
        ]);

        // Cacheni yangilash
        Cache::forget('social_user_' . auth()->id());

        return response()->json(new SocialMediaResource($socialUser), 201);
    }

    // Profilni yangilash
    public function update(SocialMediaUpdateRequest $request, $socialUserId): JsonResponse
    {
        // Foydalanuvchining ID sini olish
        $userId = auth()->id();

        // So'rov yuborilgan socialUserId modelini tekshiramiz
        $socialUser = SocialUserName::where('id', $socialUserId)->where('user_id', $userId)->first();

        // Agar topilmasa xatolik qaytariladi
        if (!$socialUser) {
            return response()->json(['message' => 'Siz faqat o\'z profilingizni yangilay olasiz.'], 403);
        }

        // Yangilash metodini chaqiramiz
        $socialUser->update($request->validated());

        // Cacheni yangilash
        Cache::forget('social_user_' . auth()->id());

        return response()->json(['message' => 'Yangilandi muvaffaqiyatli!']);
    }

    // Profilni o'chirish
    public function destroy($socialUserId): JsonResponse
    {
        $userId = auth()->id();
        
        // So'rov yuborilgan socialUserId modelini tekshiramiz
        $socialUser = SocialUserName::where('id', $socialUserId)->where('user_id', $userId)->first();

        if (!$socialUser) {
            return response()->json(['message' => 'Siz faqat o\'z profilingizni o\'chira olasiz.'], 403);
        }

        // Profilni o'chirish
        $socialUser->delete();

        // Cacheni yangilash
        Cache::forget('social_user_' . auth()->id());

        return response()->json(['message' => 'Profil o\'chirildi.']);
    }
}
