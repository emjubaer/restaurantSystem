<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' =>'user',
            'id' => $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'status' => $this->status,

                'profile' => $this->whenLoaded('profile', function () {
                    return [
                        'id' => $this->profile->id,
                        'bio' => $this->profile->bio,
                        'avatar' => $this->profile->avatar,
                        'date_of_birth' => $this->profile->date_of_birth,
                        'address' => $this->profile->address,
                    ];
                }),

                'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
                'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            ],
        ];
    }
}
