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
        public Offer $offer,
        public float $advertiserBalance = 0.0,
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
        $minRequired   = (float) ($this->offer->minimum_wallet_required ?? 0);
        $balanceLow    = $minRequired > 0 && $this->advertiserBalance < $minRequired;

        $mail = (new MailMessage)
            ->subject('Offer Approved - ' . $this->offer->name)
            ->greeting('Good news, ' . $notifiable->name . '!')
            ->line('Your offer "' . $this->offer->name . '" has been approved by our team.')
            ->line('**Offer Details:**')
            ->line('- Name: ' . $this->offer->name)
            ->line('- Pricing Model: ' . ucfirst(str_replace('_', ' ', $this->offer->pricing_model)));

        if ($this->offer->pricing_model === 'spread') {
            $mail->line('- Advertiser Payout: ₦' . number_format($this->offer->advertiser_payout, 2))
                 ->line('- Affiliate Payout: ₦' . number_format($this->offer->affiliate_payout, 2))
                 ->line('- Platform Margin: ' . number_format($this->offer->margin_percentage, 1) . '%');
        }

        if ($balanceLow) {
            $mail->line('')
                 ->line('⚠️ **Your wallet balance is low.**')
                 ->line(
                     'Your current balance is **₦' . number_format($this->advertiserBalance, 2) . '**, ' .
                     'but your offer requires a minimum of **₦' . number_format($minRequired, 2) . '** to run.'
                 )
                 ->line(
                     'Although your offer has been approved, **it will not serve traffic until your wallet balance ' .
                     'is topped up to at least ₦' . number_format($minRequired, 2) . '**.'
                 )
                 ->line(
                     'In the meantime, any affiliate links pointing to this offer will automatically redirect visitors ' .
                     'to another available offer in the same category.'
                 )
                 ->action('Top Up Wallet Now', url('/advertiser/wallet'));
        } else {
            $mail->line('Your offer is now live and visible to affiliates.')
                 ->action('View Offer', url('/advertiser/offers/' . $this->offer->id));
        }

        $mail->line('Thank you for using our platform!');

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $minRequired = (float) ($this->offer->minimum_wallet_required ?? 0);
        $balanceLow  = $minRequired > 0 && $this->advertiserBalance < $minRequired;

        return [
            'type'        => 'offer_approved',
            'offer_id'    => $this->offer->id,
            'offer_name'  => $this->offer->name,
            'pricing_model' => $this->offer->pricing_model,
            'balance_low' => $balanceLow,
            'message'     => $balanceLow
                ? 'Your offer "' . $this->offer->name . '" is approved but your wallet balance is too low to serve traffic. Please top up.'
                : 'Your offer "' . $this->offer->name . '" has been approved.',
        ];
    }
}

