<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('✅ Payout Approved - $' . number_format($payout->amount, 2))
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line('Your payout request has been approved!')
            ->line('')
            ->line('**Payout Details:**')
            ->line('- **Amount**: **$' . number_format($payout->amount, 2) . '**')
            ->line('- **Payment Method**: ' . ucfirst($payout->payment_method ?? 'N/A'))
            ->line('- **Request ID**: ' . $payout->id)
            ->line('- **Approved**: ' . $payout->approved_at?->format('M d, Y H:i') ?? now()->format('M d, Y H:i'))
            ->line('')
            ->line('🚀 **Next Steps:**')
            ->line('Your payment is now being processed and will be sent shortly.')
            ->line('You\'ll receive another notification once the payment has been sent.')
            ->line('')
            ->line('📌 Expected arrival: 5-7 business days')
            ->action('View Payout History', route('affiliate.payouts.index'))
            ->line('Thank you for your patience!');
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
            'type' => 'payout_approved',
            'title' => 'Payout Approved!',
            'message' => 'Your payout of $' . number_format($payout->amount, 2) . ' has been approved and is being processed.',
            'action_url' => route('affiliate.payouts.index'),
            'action_text' => 'View Details',
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
