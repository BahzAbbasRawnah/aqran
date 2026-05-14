<?php

namespace App\Http\Resources\Social;

use Illuminate\Http\Request;
use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CommunityResource extends JsonResource
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
            'category' => $this->category,
            'join_link' => $this->join_link,
            'cover_image' => $this->cover_image,
            'member_count' => $this->members_count ?? $this->members()->count(),
            'is_joined' => $request->user() ? $this->members()->where('user_id', $request->user()->id)->exists() : false,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'workshops' => WorkshopResource::collection($this->whenLoaded('workshops')),
            'announcements' => AnnouncementResource::collection($this->whenLoaded('announcements')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'members_count' => $this->members()->count(),
        ];
    }
}
