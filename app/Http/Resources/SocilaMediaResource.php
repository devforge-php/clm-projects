<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocilaMediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'users' => [
                'id' => $this->user->id ?? null,
                'username' => $this->user->username ?? null, // **User modelidan olish**
            ],
            'profile' => [
                'image' => $this->user->profile->image ?? null, // **User modelining profile bog‘lamasi orqali**
                'gold' => (int) ($this->user->profile->gold ?? 0),
                'silver' => (int) ($this->user->profile->silver ?? 0),
                'diamond' => (int) ($this->user->profile->diamond ?? 0),
                'level' => (int) ($this->user->profile->level ?? 0),
            ],
            'socilamedia' => [
                'telegram_user_name' => $this->telegram_user_name,
                'instagram_user_name' => $this->instagram_user_name,
                'facebook_user_name' => $this->facebook_user_name,
                'youtube_user_name' => $this->youtube_user_name,
                'twitter_user_name' => $this->twitter_user_name,
            ]
        ];;
    }
}
