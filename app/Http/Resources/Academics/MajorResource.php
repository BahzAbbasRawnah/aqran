<?php

namespace App\Http\Resources\Academics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MajorResource extends JsonResource
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
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'study_plans' => StudyPlanResource::collection($this->whenLoaded('studyPlans')),
            'courses' => CourseResource::collection($this->whenLoaded('courses')),
            // Other relationships could be added here if needed
        ];
    }
}
