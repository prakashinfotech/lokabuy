<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'avatar' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.mimes' => 'Invalid image: must be JPEG, PNG, or WebP and under 2 MB',
            'avatar.max' => 'Invalid image: must be JPEG, PNG, or WebP and under 2 MB',
        ];
    }
}
