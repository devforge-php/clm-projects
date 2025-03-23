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
                'firstname' => $this->firstname,
                'lastname' => $this->lastname,
                'username' => $this->username,
                'city' => $this->city,
                'phone' => $this->phone,
                'email' => $this->email,
            ],
            'profile' => [
                'image' => $this->profile?->image, 
                'gold' => (int) ($this->profile?->gold ?? 0),
                'level' => (int) ($this->profile?->level ?? 0),
                'created_at' => optional($this->profile?->created_at)->toISOString(),
                'updated_at' => optional($this->profile?->updated_at)->toISOString(),
            ],
        ];
    }
    
}
