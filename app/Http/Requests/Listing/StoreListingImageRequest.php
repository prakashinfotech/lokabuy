<?php

declare(strict_types=1);

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;

class StoreListingImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'file', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'image.mimes' => 'Invalid image: must be JPEG, PNG, or WebP and under 5 MB',
            'image.max' => 'Invalid image: must be JPEG, PNG, or WebP and under 5 MB',
        ];
    }
}
