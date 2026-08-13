<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ListingImageResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url,
            'order' => $this->order,
        ];
    }
}
