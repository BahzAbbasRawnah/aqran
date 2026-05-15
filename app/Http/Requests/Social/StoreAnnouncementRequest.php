<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
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
            'major_ids' => 'nullable|array',
            'major_ids.*' => 'exists:majors,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'image' => 'required|image|max:10240', // 10MB max
            'expires_at' => 'nullable|date|after:now',
        ];
    }
}
