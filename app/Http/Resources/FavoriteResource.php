<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'listing' => new ListingResource($this->whenLoaded('listing')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
