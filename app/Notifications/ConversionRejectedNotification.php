<?php

namespace App\Notifications;

use App\Models\Conversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConversionRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Conversion $conversion;
    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conversion $conversion, ?string $reason = null)
    {
        $this->conversion = $conversion;
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
        $conversion = $this->conversion;
        $commission = $conversion->commission ?? 0;
        
        $message = (new MailMessage)
            ->subject('Conversion Declined - ' . ($conversion->offer->name ?? 'Offer'))
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Unfortunately, your conversion has been declined by the advertiser.')
            ->line('')
            ->line('**Declined Conversion:**')
            ->line('- **Offer**: ' . ($conversion->offer->name ?? 'N/A'))
            ->line('- **Commission Amount**: $' . number_format($commission, 2))
            ->line('- **Conversion ID**: ' . $conversion->conversion_id)
            ->line('- **Date**: ' . $conversion->created_at->format('M d, Y H:i'));
        
        if ($this->reason) {
            $message->line('')
                ->line('**Reason for Decline:**')
                ->line($this->reason);
        }
        
        $message->line('')
            ->line('💡 **Tips to avoid rejections:**')
            ->line('- Ensure traffic quality and relevance')
            ->line('- Follow offer terms and conditions')
            ->line('- Avoid incentivized or fraudulent traffic')
            ->line('- Contact the advertiser for clarification')
            ->action('View Dashboard', route('affiliate.dashboard'))
            ->line('If you believe this was declined in error, please contact support.');
        
        return $message;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $conversion = $this->conversion;
        
        return [
            'type' => 'conversion_rejected',
            'title' => 'Conversion Declined',
            'message' => 'Your conversion for ' . ($conversion->offer->name ?? 'an offer') . ' was declined.' . ($this->reason ? ' Reason: ' . $this->reason : ''),
            'action_url' => route('affiliate.dashboard'),
            'action_text' => 'View Details',
            'conversion_id' => $conversion->id,
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
