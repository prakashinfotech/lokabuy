<?php

declare(strict_types=1);

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = [];

        if ($this->has('title')) {
            $fields['title'] = strip_tags((string) $this->input('title', ''));
        }

        if ($this->has('description')) {
            $fields['description'] = strip_tags((string) $this->input('description', ''));
        }

        if ($fields) {
            $this->merge($fields);
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:3', 'max:100'],
            'description' => ['sometimes', 'string', 'min:20', 'max:4000'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['required', 'integer', 'exists:subcategories,id'],
            'listing_type' => ['nullable', Rule::in(['buy', 'sell', 'rent'])],
            'location_city' => ['sometimes', 'string', 'max:100'],
            'location_state' => ['sometimes', 'string', 'max:100'],
            'location_country' => ['sometimes', 'string', 'max:100'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'images.*.mimes' => 'Each photo must be JPEG, PNG, or WebP.',
            'images.*.max' => 'Each photo must be under 5 MB.',
            'images.max' => 'You can upload up to 10 photos.',
        ];
    }
}
