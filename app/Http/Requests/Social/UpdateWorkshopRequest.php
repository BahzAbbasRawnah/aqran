<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkshopRequest extends FormRequest
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
            'target_major_ids' => 'sometimes|nullable|array',
            'target_major_ids.*' => 'exists:majors,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|nullable|string',
            'date' => 'sometimes|date',
            'location' => 'sometimes|nullable|string|max:255',
            'instructor_name' => 'sometimes|string|max:255',
        ];
    }
}
