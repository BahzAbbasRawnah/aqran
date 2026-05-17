<?php

namespace App\Http\Resources\Tasks;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'student_id' => $this->student_id,
            'title' => $this->title,
            'description' => $this->description,
            'due_datetime' => $this->due_datetime ? $this->due_datetime->toIso8601String() : null,
            'is_completed' => (bool) $this->is_completed,
            'status_color' => $this->getStatusColor(),
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            
            // Relationships
            'reminders' => TaskReminderResource::collection($this->whenLoaded('reminders')),
        ];
    }

    /**
     * Determine the status color based on completion and due date.
     */
    private function getStatusColor(): string
    {
        if ($this->is_completed) {
            return 'green';
        }

        if (Carbon::parse($this->due_datetime)->isPast()) {
            return 'red';
        }

        return 'orange';
    }
}
