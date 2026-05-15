<?php

namespace App\Http\Requests\Social;

use Illuminate\Foundation\Http\FormRequest;

class StoreWorkshopRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'video' => 'required|file|mimes:mp4,mov,avi,wmv|max:51200', // 50MB max for now
            'thumbnail' => 'nullable|image|max:5120', // 5MB max
            'target_major_ids' => 'nullable|array',
            'target_major_ids.*' => 'exists:majors,id',
        ];
    }
}
