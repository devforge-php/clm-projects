<?php

namespace App\Http\Controllers\SocailMedia;

use App\Http\Controllers\Controller;
use App\Http\Requests\SocialMediaStoreRequest;
use App\Http\Requests\SocialMediaUpdateRequest;
use App\Http\Resources\SocialMediaResource;
use App\Models\SocialUserName;
use App\Services\SocialMediaServices;
use Illuminate\Http\JsonResponse;

class SocialMediaController extends Controller
{
    public function __construct(private SocialMediaServices $service)
    {
        $this->middleware('throttle:60,1');
    }

    // Faonly o'zining profilingizni ko'rsatish
    public function index(): JsonResponse
    {
        $data = $this->service->getAllForUser(auth()->id());

        if ($data->isEmpty()) {
            return response()->json(['message' => 'Sizning profilingiz topilmadi.'], 404);
        }

        return response()->json(SocialMediaResource::collection($data));
    }

    // Yangi profil yaratish
    public function store(SocialMediaStoreRequest $request): JsonResponse
    {
        $dto = $request->validated();

        $result = $this->service->createForUser(auth()->id(), $dto);

        if (!$result) {
            return response()->json([
                'message' => 'Siz allaqachon ijtimoiy tarmoqlar profilingizni kiritgansiz.'
            ], 409);
        }

        return response()->json(new SocialMediaResource($result), 201);
    }

    // Faqat o'z profilingizni yangilash
    public function update(SocialMediaUpdateRequest $request, SocialUserName $socialUser): JsonResponse
    {
        // Faqat o'z profilingizni yangilashga ruxsat beriladi
        if ($socialUser->user_id !== auth()->id()) {
            return response()->json(['message' => 'Siz faqat o\'z profilingizni yangilay olasiz.'], 403);
        }

        $this->service->updateForUser($socialUser, $request->validated());

        return response()->json(['message' => 'Yangilandi muvaffaqiyatli!']);
    }

    // Profilni o'chirish
    public function destroy(SocialUserName $socialUser): JsonResponse
    {
        // Faqat o'z profilingizni o'chirishga ruxsat beriladi
        if ($socialUser->user_id !== auth()->id()) {
            return response()->json(['message' => 'Siz faqat o\'z profilingizni o\'chira olasiz.'], 403);
        }

        $this->service->deleteForUser($socialUser);

        return response()->json(['message' => 'Profil o\'chirildi.']);
    }
}
