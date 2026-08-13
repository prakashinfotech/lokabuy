<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => strip_tags((string) $this->input('name', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'regex:/^\+?[\d\s\-]{7,15}$/'],
            'password' => ['required', 'confirmed', Password::min(10)->mixedCase()->numbers()->symbols()],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Phone number must be a valid format (e.g. +91 98765 43210).',
            'password.min' => 'Password must be at least 10 characters.',
        ];
    }
}
