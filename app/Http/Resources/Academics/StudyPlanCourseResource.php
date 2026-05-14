<?php

namespace App\Http\Resources\Academics;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudyPlanCourseResource extends JsonResource
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
            'study_plan_id' => $this->study_plan_id,
            'course_id' => $this->course_id,
            'semester_level' => $this->semester_level,
            'course_type' => $this->course_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'study_plan' => new StudyPlanResource($this->whenLoaded('studyPlan')),
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}
