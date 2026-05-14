<?php

namespace App\Http\Resources\Schedules;

use App\Http\Resources\Academics\CourseResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScheduleItemResource extends JsonResource
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
            'schedule_id' => $this->schedule_id,
            'course_id' => $this->course_id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'location' => $this->notes,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Relationships
            'schedule' => new ScheduleResource($this->whenLoaded('schedule')),
            'course' => new CourseResource($this->whenLoaded('course')),
        ];
    }
}
