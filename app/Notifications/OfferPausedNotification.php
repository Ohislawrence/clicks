<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferPausedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Offer  $offer,
        public readonly string $reason,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🚨 Campaign Paused: ' . $this->offer->name)
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your campaign has been automatically paused because its budget is exhausted.')
            ->line('')
            ->line('**Campaign Details:**')
            ->line('- **Offer:** ' . $this->offer->name)
            ->line('- **Reason:** ' . $this->reason)
            ->line('- **Budget Allocated:** ₦' . number_format($this->offer->budget_limit, 2))
            ->line('- **Budget Spent:** ₦' . number_format($this->offer->spent_budget, 2))
            ->line('- **Conversions Delivered:** ' . number_format($this->offer->total_conversions))
            ->line('')
            ->line('**Top up your wallet to resume this campaign immediately.** Your affiliates are ready to drive more conversions.')
            ->action('Top Up Wallet & Resume', route('advertiser.wallet.index'))
            ->line('If you have any questions, please contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'        => 'offer_paused',
            'offer_id'    => $this->offer->id,
            'offer_name'  => $this->offer->name,
            'reason'      => $this->reason,
            'budget_limit' => $this->offer->budget_limit,
            'spent_budget' => $this->offer->spent_budget,
            'message'     => "Your offer \"{$this->offer->name}\" has been paused: {$this->reason}",
        ];
    }
}

