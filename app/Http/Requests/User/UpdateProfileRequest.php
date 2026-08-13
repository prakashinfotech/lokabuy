<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($this->user()->id)],
            'phone' => ['nullable', 'regex:/^\+?[\d\s\-]{7,15}$/'],
            'email_notifications' => ['sometimes', 'boolean'],
        ];
    }
}
