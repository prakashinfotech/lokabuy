<?php

declare(strict_types=1);

namespace App\Http\Requests\Listing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => strip_tags((string) $this->input('title', '')),
            'description' => strip_tags((string) $this->input('description', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'description' => ['required', 'string', 'min:20', 'max:4000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'size:3'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'subcategory_id' => ['required', 'integer', 'exists:subcategories,id'],
            'listing_type' => ['nullable', Rule::in(['buy', 'sell', 'rent'])],
            'location_city' => ['required', 'string', 'max:100'],
            'location_state' => ['required', 'string', 'max:100'],
            'location_country' => ['required', 'string', 'max:100'],
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
