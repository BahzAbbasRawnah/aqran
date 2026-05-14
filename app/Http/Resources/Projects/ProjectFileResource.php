<?php

namespace App\Http\Resources\Projects;

use App\Http\Resources\Auth\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectFileResource extends JsonResource
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
            'project_id' => $this->project_id,
            'uploader_id' => $this->user_id,
            'is_pinned' => $this->is_pinned,
            'file_path' => \Storage::url($this->file_path),
            'file_name' => $this->file_name,
            'file_size_mb' => $this->file_size_mb,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'project' => new ProjectResource($this->whenLoaded('project')),
            'uploader' => new UserResource($this->whenLoaded('uploader')),
        ];
    }
}
