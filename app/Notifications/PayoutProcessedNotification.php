<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public PayoutRequest $payout;

    /**
     * Create a new notification instance.
     */
    public function __construct(PayoutRequest $payout)
    {
        $this->payout = $payout;
    }

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
        $payout = $this->payout;
        
        return (new MailMessage)
            ->subject('💸 Payment Sent! $' . number_format($payout->amount, 2))
            ->greeting('Excellent news, ' . $notifiable->name . '!')
            ->line('Your payment has been processed and sent!')
            ->line('')
            ->line('**Payment Details:**')
            ->line('- **Amount**: **$' . number_format($payout->amount, 2) . '**')
            ->line('- **Payment Method**: ' . ucfirst($payout->payment_method ?? 'N/A'))
            ->line('- **Transaction ID**: ' . ($payout->transaction_id ?? 'N/A'))
            ->line('- **Processed**: ' . ($payout->processed_at?->format('M d, Y H:i') ?? now()->format('M d, Y H:i')))
            ->line('')
            ->line('📌 **When will I receive it?**')
            ->line('Depending on your payment method, funds should arrive within:')
            ->line('- **Bank Transfer**: 3-5 business days')
            ->line('- **PayPal**: 1-2 business days')
            ->line('- **Wire Transfer**: 2-3 business days')
            ->line('')
            ->line('💡 If you don\'t receive your payment within this timeframe, please contact support with your transaction ID.')
            ->action('View Payout History', route('affiliate.payouts.index'))
            ->line('Thank you for being a valued partner! 🚀');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $payout = $this->payout;
        
        return [
            'type' => 'payout_processed',
            'title' => 'Payment Sent!',
            'message' => 'Your payment of $' . number_format($payout->amount, 2) . ' has been processed and sent.',
            'action_url' => route('affiliate.payouts.index'),
            'action_text' => 'View Details',
            'payout_id' => $payout->id,
            'amount' => $payout->amount,
            'transaction_id' => $payout->transaction_id,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
