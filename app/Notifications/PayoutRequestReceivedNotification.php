<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRequestReceivedNotification extends Notification implements ShouldQueue
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
            ->subject('Payout Request Received - $' . number_format($payout->amount, 2))
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('We\'ve received your payout request.')
            ->line('')
            ->line('**Payout Request Details:**')
            ->line('- **Amount**: **$' . number_format($payout->amount, 2) . '**')
            ->line('- **Request ID**: ' . $payout->id)
            ->line('- **Payment Method**: ' . ucfirst($payout->payment_method ?? 'N/A'))
            ->line('- **Status**: Pending Review')
            ->line('- **Requested**: ' . $payout->created_at->format('M d, Y H:i'))
            ->line('')
            ->line('⏳ **What happens next?**')
            ->line('1. Our team reviews your request (typically within 1-2 business days)')
            ->line('2. Once approved, payment will be processed')
            ->line('3. You\'ll receive confirmation when payment is sent')
            ->line('')
            ->line('📌 Payment typically arrives within 5-7 business days after approval.')
            ->action('View Payout Status', route('affiliate.payouts.index'))
            ->line('Thank you for being a valued partner!');
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
            'type' => 'payout_received',
            'title' => 'Payout Request Received',
            'message' => 'Your payout request for $' . number_format($payout->amount, 2) . ' is being reviewed.',
            'action_url' => route('affiliate.payouts.index'),
            'action_text' => 'View Status',
            'payout_id' => $payout->id,
            'amount' => $payout->amount,
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
