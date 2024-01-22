<?php

namespace App\Http\Requests\Admin\User;

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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'avatar'  => ['required', 'file', 'mimes:png,jpg,jpeg', 'max:2048'],
            'password' => ['nullable', 'string', 'max:255', 'confirmed'],
            'status' => ['required', 'string', Rule::in(['active', 'inactive'])],

            'role_id' => ['required', 'array'],
            'role_id.*' => ['required', 'exists:roles,id'],
        ];
    }
}
