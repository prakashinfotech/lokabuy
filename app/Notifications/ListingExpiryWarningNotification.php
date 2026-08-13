<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Listing;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ListingExpiryWarningNotification extends Notification implements ShouldQueue
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
            ->subject('Your ad expires in 7 days — '.$this->listing->title)
            ->greeting('Heads up!')
            ->line("Your listing **\"{$this->listing->title}\"** will expire on **{$this->listing->expires_at->format('d M Y')}**.")
            ->line('Renew it now to keep it visible to buyers for another 60 days.')
            ->action('Renew My Ad', url('/my/ads'))
            ->line('If you have already sold this item, you can mark it as sold from your dashboard.')
            ->salutation('Thanks, The Lokabuy Team');
    }
}
