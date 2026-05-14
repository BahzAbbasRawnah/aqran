<?php

namespace App\Http\Requests\Schedules;

use Illuminate\Foundation\Http\FormRequest;

class StoreScheduleRequest extends FormRequest
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
            'student_id' => 'required|exists:students,id',
            'semester' => 'required|integer',
            'year' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.course_id' => 'required|exists:courses,id',
            'items.*.type' => 'required|in:theory,practical',
            'items.*.day_of_week' => 'required|in:sunday,monday,tuesday,wednesday,thursday,friday,saturday',
            'items.*.start_time' => 'required|date_format:H:i',
            'items.*.end_time' => 'required|date_format:H:i|after:items.*.start_time',
            'items.*.notes' => 'nullable|string',
        ];
    }
}
