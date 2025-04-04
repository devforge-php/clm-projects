<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
      
class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => [
                'firstname' => $this->user?->firstname,  // $this->user orqali user ma'lumotini olish
                'lastname'  => $this->user?->lastname,
                'username'  => $this->user?->username,
                'city'      => $this->user?->city,
                'phone'     => $this->user?->phone,
                'email'     => $this->user?->email,
            ],
            'profile' => [
                'image'      => $this->image, 
                'gold'       => (int) ($this->gold ?? 0),
                'tasks'      => (int) ($this->tasks ?? 0),
                'refferals'  => (int) ($this->refferals ?? 0),
                'level'      => (int) ($this->level ?? 0),
                'created_at' => optional($this->created_at)->toISOString(),
                'updated_at' => optional($this->updated_at)->toISOString(),
            ],
        ];
    }
}
