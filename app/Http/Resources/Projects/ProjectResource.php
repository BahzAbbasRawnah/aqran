<?php

namespace App\Http\Resources\Projects;

use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'chat_link' => $this->chat_link,
            'semester_end_date' => $this->semester_end_date,
            'expires_at' => $this->expires_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'members' => $this->whenLoaded('members', function() {
                return $this->members->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'profile_photo_url' => $user->profile_photo_url,
                        'role' => $user->pivot->role,
                    ];
                });
            }),
            'files' => ProjectFileResource::collection($this->whenLoaded('files')),
        ];
    }
}
