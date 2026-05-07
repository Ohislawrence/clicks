<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferRemovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Offer $offer,
        public string $reason = 'deactivated'
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Offer No Longer Available - ' . $this->offer->name)
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('The offer "' . $this->offer->name . '" is no longer available for promotion.');

        if ($this->reason === 'rejected') {
            $message->line('This offer has been rejected by our admin team and removed from the platform.');
        } elseif ($this->reason === 'deleted') {
            $message->line('This offer has been permanently removed by the advertiser.');
        } else {
            $message->line('This offer has been deactivated and is temporarily unavailable.');
        }

        $message->line('If you have any active links or campaigns promoting this offer, please pause them.')
            ->line('Any pending conversions will still be processed and credited to your account.')
            ->action('Browse Other Offers', url('/affiliate/offers'))
            ->line('Thank you for your understanding.');

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_removed',
            'offer_id' => $this->offer->id,
            'offer_name' => $this->offer->name,
            'reason' => $this->reason,
            'message' => 'Offer "' . $this->offer->name . '" is no longer available.',
        ];
    }
}
