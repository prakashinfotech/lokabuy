<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send(User $user, Notification $notification): void
    {
        if (! $user->email_notifications) {
            return;
        }

        try {
            $user->notify($notification);
        } catch (\Throwable $e) {
            Log::error('Failed to dispatch notification', [
                'user_id' => $user->id,
                'notification' => get_class($notification),
                'error' => $e->getMessage(),
            ]);
        }
    }
}
