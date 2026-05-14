<?php

namespace App\Http\Requests\Academics;

use Illuminate\Foundation\Http\FormRequest;

class StoreStudyPlanCourseRequest extends FormRequest
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
            'study_plan_id' => 'required|exists:study_plans,id',
            'course_id' => 'required|exists:courses,id',
            'semester_level' => 'required|integer',
            'course_type' => 'required|in:mandatory,elective',
        ];
    }
}
