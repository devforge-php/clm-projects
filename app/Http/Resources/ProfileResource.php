<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
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
                    'id' => $this->user->id,
                    'firstname' => $this->user->firstname,
                    'lastname' => $this->user->lastname,
                    'username' => $this->user->username,
                    'city' => $this->user->city,
                    'phone' => $this->user->phone,
                    'email' => $this->user->email,
                ],
            'image' => $this->image,
            'gold' => (int) $this->gold,
            'tasks' => (int) $this->tasks,
            'refferals' => (int) $this->refferals,
            'level' => (int) $this->level,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
    
}
