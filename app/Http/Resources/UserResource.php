<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'is_verified' => $this->is_verified,
            'is_active' => $this->is_active,
            'role' => $this->role,
            'email_notifications' => $this->email_notifications,
            'created_at' => $this->created_at?->toDateTimeString(),
        ];
    }
}
