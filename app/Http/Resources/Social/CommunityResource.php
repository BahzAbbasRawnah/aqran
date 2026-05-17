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
            'major_id' => $this->major_id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category,
            'join_link' => $this->join_link,
            'cover_image' => $this->cover_image ? asset('storage/' . $this->cover_image) : null,
            'member_count' => $this->members_count ?? $this->members()->count(),
            'is_joined' => $request->user() ? $this->members()->where('user_id', $request->user()->id)->exists() : false,
            'status' => $this->status instanceof \BackedEnum ? $this->status->value : $this->status,
            'reject_reason' => $this->reject_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'major' => new \App\Http\Resources\Academics\MajorResource($this->whenLoaded('major')),
            'members' => UserResource::collection($this->whenLoaded('members')),
            'members_count' => $this->members()->count(),
        ];
    }
}
