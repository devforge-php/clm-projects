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
            'user' => [
                'first_name' => $this->user->firstname,
                'last_name' => $this->user->lastname,
            ],
            'telegram' => $this->telegram_user_name,
            'instagram' => $this->instagram_user_name,
            'facebook' => $this->facebook_user_name,
            'youtube' => $this->youtube_user_name,
            'twitter' => $this->twitter_user_name,
        ];
    }
}
