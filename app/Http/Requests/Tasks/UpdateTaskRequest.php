<?php

namespace App\Http\Requests\Tasks;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'student_id' => 'sometimes|exists:students,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'due_datetime' => 'sometimes|date',
            'is_completed' => 'sometimes|boolean',
            'reminders' => 'sometimes|array',
            'reminders.*.reminder_type' => 'required_with:reminders|string|in:10_min,1_hour,1_day,3_days',
            'reminders.*.is_sent' => 'sometimes|boolean',
        ];
    }
}
