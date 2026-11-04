<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user('web')?->is_admin;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('email'))) {
            $this->merge(['email' => mb_strtolower(trim($this->input('email')))]);
        }
    }

    public function rules(): array
    {
        return ['name' => ['required', 'string', 'max:255'], 'email' => ['required', 'email', 'max:254', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'password' => [$this->isMethod('POST') ? 'required' : 'nullable', 'string', 'max:1024', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
            'is_admin' => ['required', 'boolean'], 'is_active' => ['required', 'boolean']];
    }
}
