<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingRemovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Listing $listing) {}

    public function via(): array
    {
        return ['mail'];
    }

    public function toMail(): MailMessage
    {
        return (new MailMessage)
            ->subject('Your ad has been removed — '.$this->listing->title)
            ->greeting('Important notice.')
            ->line("Your listing **\"{$this->listing->title}\"** has been removed by our moderation team following a review.")
            ->line('This action was taken because the listing was found to violate our community guidelines.')
            ->line('If you believe this was a mistake, please contact our support team.')
            ->action('Post a New Ad', url('/sell'))
            ->salutation('Thanks, The Lokabuy Team');
    }
}
