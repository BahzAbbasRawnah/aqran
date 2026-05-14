<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\Academics\MajorResource;
use App\Http\Resources\Academics\StudyPlanResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'major_id' => $this->major_id,
            'study_plan_id' => $this->study_plan_id,
            'enrollment_year' => $this->enrollment_year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'user' => new UserResource($this->whenLoaded('user')),
            'major' => new MajorResource($this->whenLoaded('major')),
            'study_plan' => new StudyPlanResource($this->whenLoaded('studyPlan')),
        ];
    }
}
