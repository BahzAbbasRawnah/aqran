<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCommunityRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'major_id' => 'sometimes|nullable|exists:majors,id',
            'description' => 'sometimes|nullable|string',
            'category' => 'sometimes|nullable|string|max:100',
            'join_link' => 'sometimes|nullable|url|max:500',
            'cover_image' => 'sometimes|nullable|string|max:255',
            'member_count' => 'sometimes|integer|min:0',
        ];
    }
}
