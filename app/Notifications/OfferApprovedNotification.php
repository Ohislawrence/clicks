<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Offer $offer
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
        return (new MailMessage)
            ->subject('Offer Approved - ' . $this->offer->name)
            ->greeting('Good news, ' . $notifiable->name . '!')
            ->line('Your offer "' . $this->offer->name . '" has been approved by our team.')
            ->line('**Offer Details:**')
            ->line('- Name: ' . $this->offer->name)
            ->line('- Pricing Model: ' . ucfirst(str_replace('_', ' ', $this->offer->pricing_model)))
            ->when($this->offer->pricing_model === 'spread', function($message) {
                return $message
                    ->line('- Advertiser Payout: ₦' . number_format($this->offer->advertiser_payout, 2))
                    ->line('- Affiliate Payout: ₦' . number_format($this->offer->affiliate_payout, 2))
                    ->line('- Platform Margin: ' . number_format($this->offer->margin_percentage, 1) . '%');
            })
            ->line('Your offer is now live and visible to affiliates.')
            ->action('View Offer', url('/advertiser/offers/' . $this->offer->id))
            ->line('Thank you for using our platform!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_approved',
            'offer_id' => $this->offer->id,
            'offer_name' => $this->offer->name,
            'pricing_model' => $this->offer->pricing_model,
            'message' => 'Your offer "' . $this->offer->name . '" has been approved.',
        ];
    }
}
