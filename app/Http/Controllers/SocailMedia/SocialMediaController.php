<?php

namespace App\Http\Controllers\SocailMedia;

use App\Http\Controllers\Controller;
use App\Http\Resources\SocialMediaResource;
use App\Models\SocialUserName;
use App\Services\SocialMediaServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SocialMediaController extends Controller
{
    protected $socialMediaService;

    public function __construct(SocialMediaServices $socialMediaService)
    {
        $this->socialMediaService = $socialMediaService;
    }

    public function index()
    {
        return $this->socialMediaService->getAllSocialUsers();
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

        return $socialUser;
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

        $this->socialMediaService->updateSocialUser($id, $validatedData);

        return response()->json(['message' => 'Updated successfully']);
    }
}
