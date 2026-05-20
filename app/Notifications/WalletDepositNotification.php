<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WalletDepositNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly float $amount) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Wallet Funded — ₦' . number_format($this->amount, 2))
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('Your wallet has been successfully credited.')
            ->line('')
            ->line('**Amount Deposited:** ₦' . number_format($this->amount, 2))
            ->line('**New Balance:** ₦' . number_format($notifiable->fresh()->advertiser_balance, 2))
            ->line('')
            ->line('You can now create offers or top up existing campaign budgets.')
            ->action('Go to My Wallet', route('advertiser.wallet.index'))
            ->line('Thank you for using ' . config('app.name') . '!');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'    => 'wallet_deposit',
            'amount'  => $this->amount,
            'message' => '₦' . number_format($this->amount, 2) . ' has been credited to your wallet.',
        ];
    }
}
