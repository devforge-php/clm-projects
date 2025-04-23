<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user' => $this->user->username,
            'telegram_user_name' => $this->telegram_user_name,
            'instagram_user_name' => $this->instagram_user_name,
            'facebook_user_name' => $this->facebook_user_name,
            'youtube_user_name' => $this->youtube_user_name,
            'twitter_user_name' => $this->twitter_user_name,
        ];
    }
}
