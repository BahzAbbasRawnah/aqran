<?php

namespace App\Http\Requests\Academics;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudyPlanCourseRequest extends FormRequest
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
            'study_plan_id' => 'sometimes|exists:study_plans,id',
            'course_id' => 'sometimes|exists:courses,id',
            'semester_level' => 'sometimes|integer',
            'course_type' => 'sometimes|in:mandatory,elective',
        ];
    }
}
