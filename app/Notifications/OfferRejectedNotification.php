<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Offer $offer,
        public ?string $reason = null
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
            ->subject('Offer Rejected - ' . $this->offer->name)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your offer "' . $this->offer->name . '" has been reviewed and unfortunately was not approved at this time.')
            ->line('**Offer Details:**')
            ->line('- Name: ' . $this->offer->name)
            ->line('- Pricing Model: ' . ucfirst(str_replace('_', ' ', $this->offer->pricing_model)));

        if ($this->offer->pricing_model === 'spread') {
            $message
                ->line('- Advertiser Payout: ₦' . number_format($this->offer->advertiser_payout, 2))
                ->line('- Affiliate Payout: ₦' . number_format($this->offer->affiliate_payout, 2))
                ->line('- Platform Margin: ' . number_format($this->offer->margin_percentage, 1) . '%');
        }

        if ($this->reason) {
            $message
                ->line('')
                ->line('**Reason for Rejection:**')
                ->line($this->reason);
        }

        return $message
            ->line('')
            ->line('You can edit your offer and resubmit it for review with the suggested changes.')
            ->action('Edit Offer', url('/advertiser/offers/' . $this->offer->id . '/edit'))
            ->line('If you have questions, please contact our support team.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_rejected',
            'offer_id' => $this->offer->id,
            'offer_name' => $this->offer->name,
            'pricing_model' => $this->offer->pricing_model,
            'rejection_reason' => $this->reason,
            'message' => 'Your offer "' . $this->offer->name . '" was rejected.',
        ];
    }
}
