<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('Your ad is now live — '.$this->listing->title)
            ->greeting('Great news!')
            ->line("Your listing **\"{$this->listing->title}\"** has been approved and is now visible to buyers.")
            ->action('View Your Ad', url('/listings/'.$this->listing->slug))
            ->line('Your ad will remain active for 60 days. You can renew it from your dashboard before it expires.')
            ->salutation('Thanks, The Lokabuy Team');
    }
}
