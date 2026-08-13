<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'reason' => $this->reason,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'listing' => new ListingResource($this->whenLoaded('listing')),
            'reporter' => new UserResource($this->whenLoaded('reporter')),
        ];
    }
}
