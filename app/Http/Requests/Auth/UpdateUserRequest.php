<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
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
            'username' => ['sometimes', 'string', 'max:50', Rule::unique('users')->ignore($this->user)],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users')->ignore($this->user)],
            'password' => 'sometimes|string|min:8',
            'auth_provider' => 'sometimes|nullable|in:google,apple',
            'role' => 'sometimes|in:student,admin',
        ];
    }
}
