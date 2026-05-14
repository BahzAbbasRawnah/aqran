<?php

namespace App\Http\Resources\Social;

use App\Http\Resources\Academics\MajorResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkshopResource extends JsonResource
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
            'user_id' => $this->user_id,
            'community_id' => $this->community_id,
            'title' => $this->title,
            'description' => $this->description,
            'video_url' => $this->video_url,
            'thumbnail_url' => $this->thumbnail_url,
            'status' => $this->status,
            'reject_reason' => $this->reject_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'user' => new \App\Http\Resources\Auth\UserResource($this->whenLoaded('user')),
            'community' => new CommunityResource($this->whenLoaded('community')),
            'target_majors' => MajorResource::collection($this->whenLoaded('targetMajors')),
        ];
    }
}
