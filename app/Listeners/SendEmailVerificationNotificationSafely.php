<?php

declare(strict_types=1);

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Log;

class SendEmailVerificationNotificationSafely
{
    public function handle(Registered $event): void
    {
        try {
            $event->user->sendEmailVerificationNotification();
        } catch (\Throwable $e) {
            Log::error('Failed to send email verification notification', [
                'user_id' => $event->user->id,
                'email' => $event->user->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
