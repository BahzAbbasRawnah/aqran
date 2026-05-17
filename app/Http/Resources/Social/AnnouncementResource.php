<?php

namespace App\Http\Resources\Social;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
            'title' => $this->title,
            'content' => $this->content,
            'image_url' => $this->image_url ? asset('storage/' . $this->image_url) : null,
            'publish_date' => $this->publish_date,
            'expires_at' => $this->expires_at,
            'status' => $this->status,
            'reject_reason' => $this->reject_reason,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_general' => $this->majors->isEmpty(),
            
            // Relationships
            'majors' => \App\Http\Resources\Academics\MajorResource::collection($this->whenLoaded('majors')),
        ];
    }
}
