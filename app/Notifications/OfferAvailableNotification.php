<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferAvailableNotification extends Notification implements ShouldQueue
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
        $commission = $this->offer->pricing_model === 'spread'
            ? '₦' . number_format($this->offer->affiliate_payout, 2)
            : ($this->offer->commission_model === 'revshare'
                ? $this->offer->commission_rate . '%'
                : '₦' . number_format($this->offer->commission_rate, 2));

        return (new MailMessage)
            ->subject('New Offer Available - ' . $this->offer->name)
            ->greeting('Hi ' . $notifiable->name . '!')
            ->line('A new offer is now available for you to promote:')
            ->line('**' . $this->offer->name . '**')
            ->line($this->offer->description ? substr($this->offer->description, 0, 150) . '...' : '')
            ->line('**Commission:** ' . $commission)
            ->line('**Category:** ' . $this->offer->category->name)
            ->line('Start promoting this offer today and earn commissions!')
            ->action('View Offer', url('/affiliate/offers/' . $this->offer->id))
            ->line('Thank you for being part of our network!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'offer_available',
            'offer_id' => $this->offer->id,
            'offer_name' => $this->offer->name,
            'category' => $this->offer->category->name,
            'message' => 'New offer available: "' . $this->offer->name . '"',
        ];
    }
}
