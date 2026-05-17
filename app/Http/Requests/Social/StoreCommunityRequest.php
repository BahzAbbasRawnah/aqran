<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommunityRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'major_id' => 'nullable|exists:majors,id',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'join_link' => 'nullable|url|max:500',
            'cover_image' => 'nullable|string|max:255',
            'member_count' => 'sometimes|integer|min:0',
        ];
    }
}
