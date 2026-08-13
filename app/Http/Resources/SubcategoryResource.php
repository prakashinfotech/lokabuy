<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SubcategoryResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'category_id' => $this->category_id,
            'listings_count' => $this->when(isset($this->listings_count), $this->listings_count),
        ];
    }
}
