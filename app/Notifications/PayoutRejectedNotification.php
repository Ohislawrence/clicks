<?php

namespace App\Notifications;

use App\Models\PayoutRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public PayoutRequest $payout;
    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(PayoutRequest $payout, ?string $reason = null)
    {
        $this->payout = $payout;
        $this->reason = $reason;
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
        
        $message = (new MailMessage)
            ->subject('Payout Request Declined - $' . number_format($payout->amount, 2))
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Unfortunately, your payout request has been declined.')
            ->line('')
            ->line('**Declined Payout:**')
            ->line('- **Amount**: $' . number_format($payout->amount, 2))
            ->line('- **Payment Method**: ' . ucfirst($payout->payment_method ?? 'N/A'))
            ->line('- **Request ID**: ' . $payout->id)
            ->line('- **Requested**: ' . $payout->created_at->format('M d, Y H:i'));
        
        if ($this->reason) {
            $message->line('')
                ->line('**Reason for Decline:**')
                ->line($this->reason);
        }
        
        $message->line('')
            ->line('**What should I do?**')
            ->line('- Review the reason provided above')
            ->line('- Update your payment information if needed')
            ->line('- Contact support if you have questions')
            ->line('- Submit a new request once the issue is resolved')
            ->line('')
            ->line('💵 Your balance has been restored and is available for withdrawal.')
            ->action('View Payouts', route('affiliate.payouts.index'))
            ->line('If you need assistance, please contact our support team.');
        
        return $message;
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
            'type' => 'payout_rejected',
            'title' => 'Payout Declined',
            'message' => 'Your payout request for $' . number_format($payout->amount, 2) . ' was declined.' . ($this->reason ? ' Reason: ' . $this->reason : ''),
            'action_url' => route('affiliate.payouts.index'),
            'action_text' => 'View Details',
            'payout_id' => $payout->id,
            'amount' => $payout->amount,
            'reason' => $this->reason,
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
