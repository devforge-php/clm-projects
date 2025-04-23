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

    public function index(): JsonResponse
    {
        $data = $this->service->getAllForUser(auth()->id());
        return response()->json(SocialMediaResource::collection($data));
    }

    public function store(SocialMediaStoreRequest $request): JsonResponse
    {
        $dto = $request->validated();
        $result = $this->service->createForUser(auth()->id(), $dto);

        if (! $result) {
            return response()->json([
                'message' => 'Siz allaqachon ijtimoiy tarmoqlar profilingizni kiritgansiz.'
            ], 409);
        }

        return response()->json(new SocialMediaResource($result), 201);
    }

    public function update(SocialMediaUpdateRequest $request, SocialUserName $socialUser): JsonResponse
    {
        // Policy check (agar kerak bo'lsa): $this->authorize('update', $socialUser);
    
        // Yalpi o'zgarishlar faqat `user_id` asosida bo'ladi, shuning uchun
        // faqat `user_id` orqali tekshirish kerak bo'ladi.
        if ($socialUser->user_id !== auth()->id()) {
            return response()->json(['message' => 'Siz faqat o\'z profilingizni yangilay olasiz.'], 403);
        }
    
        // Yangilash metodini chaqiramiz.
        $this->service->updateForUser($socialUser, $request->validated());
    
        // Natija qaytariladi
        return response()->json(['message' => 'Yangilandi muvaffaqiyatli!']);
    }
    
}
