<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'subcategories' => SubcategoryResource::collection($this->whenLoaded('subcategories')),
            'listings_count' => $this->when(isset($this->listings_count), $this->listings_count),
        ];
    }
}
