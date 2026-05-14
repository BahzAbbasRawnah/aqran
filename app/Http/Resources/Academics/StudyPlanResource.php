<?php

namespace App\Http\Resources\Academics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanResource extends JsonResource
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
            'effective_year' => $this->effective_year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'major' => new MajorResource($this->whenLoaded('major')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
        ];
    }
}
