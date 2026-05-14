<?php

namespace App\Http\Requests\Projects;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectFileRequest extends FormRequest
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
            'project_id' => 'sometimes|exists:projects,id',
            'user_id' => 'sometimes|exists:users,id',
            'section' => 'sometimes|in:pinned,chat',
            'file_path' => 'sometimes|string|max:255',
            'file_name' => 'sometimes|string|max:255',
            'file_size_mb' => 'sometimes|numeric|min:0',
            'description' => 'sometimes|nullable|string|max:280',
        ];
    }
}
