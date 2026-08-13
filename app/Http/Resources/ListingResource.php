<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => (float) $this->price,
            'currency' => $this->currency,
            'listing_type' => $this->listing_type,
            'location_city' => $this->location_city,
            'location_state' => $this->location_state,
            'location_country' => $this->location_country,
            'status' => $this->status,
            'approval_status' => $this->approval_status,
            'is_featured' => (bool) $this->is_featured,
            'featured_until' => $this->featured_until?->toIso8601String(),
            'view_count' => $this->view_count,
            'expires_at' => $this->expires_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'user' => new UserResource($this->whenLoaded('user')),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'images' => ListingImageResource::collection($this->whenLoaded('images')),
            'is_favorited' => $this->when(
                isset($this->is_favorited),
                fn () => (bool) $this->is_favorited
            ),
        ];
    }
}
