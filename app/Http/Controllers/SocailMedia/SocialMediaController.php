<?php

namespace App\Http\Controllers\SocailMedia;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialMediaResource;
use App\Services\SocialMediaServices;
use Illuminate\Http\Request;

class SocialMediaController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaServices $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }

    public function index()
    {
        return response()->json($this->socialMediaService->getAllSocialUsers());
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'telegram_user_name' => 'string|nullable',
            'instagram_user_name' => 'string|nullable',
            'facebook_user_name' => 'string|nullable',
            'youtube_user_name' => 'string|nullable',
            'twitter_user_name' => 'string|nullable',
        ]);

        $socialUser = $this->socialMediaService->createSocialUser($validatedData, auth()->id());

        if (!$socialUser) {
            return response()->json([
                'message' => 'Siz allaqachon ijtimoiy tarmoqlar profilingizni kiritgansiz.'
            ], 409);
        }

        return response()->json($socialUser);
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'telegram_user_name' => 'string|nullable',
            'instagram_user_name' => 'string|nullable',
            'facebook_user_name' => 'string|nullable',
            'youtube_user_name' => 'string|nullable',
            'twitter_user_name' => 'string|nullable',
        ]);

        $updated = $this->socialMediaService->updateSocialUser($id, $validatedData);

        if (!$updated) {
            return response()->json(['message' => 'Ruxsat yo‘q yoki maʼlumot topilmadi.'], 403);
        }

        return response()->json(['message' => 'Updated successfully']);
    }
}
