<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Listing $listing,
        private readonly string $reason,
    ) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Your ad could not be approved — '.$this->listing->title)
            ->greeting('We reviewed your listing.')
            ->line("Unfortunately, your listing **\"{$this->listing->title}\"** was not approved.")
            ->line('**Reason:** '.$this->reason)
            ->line('Please review our listing guidelines and resubmit your ad with the required changes.')
            ->action('Post a New Ad', url('/sell'))
            ->salutation('Thanks, The Lokabuy Team');
    }
}
