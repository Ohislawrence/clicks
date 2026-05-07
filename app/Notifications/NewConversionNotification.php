<?php

namespace App\Notifications;

use App\Models\Conversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewConversionNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Conversion $conversion;
    public string $recipientType; // 'affiliate' or 'advertiser'

    /**
     * Create a new notification instance.
     */
    public function __construct(Conversion $conversion, string $recipientType = 'affiliate')
    {
        $this->conversion = $conversion;
        $this->recipientType = $recipientType;
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
        
        if ($this->recipientType === 'affiliate') {
            return $this->affiliateEmail($notifiable);
        }
        
        return $this->advertiserEmail($notifiable);
    }

    protected function affiliateEmail(object $notifiable): MailMessage
    {
        $conversion = $this->conversion;
        $payout = $conversion->payout ?? 0;
        $commission = $conversion->commission ?? 0;
        
        return (new MailMessage)
            ->subject('💰 New Conversion! You Earned $' . number_format($commission, 2))
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line('You just earned a new commission!')
            ->line('')
            ->line('**Conversion Details:**')
            ->line('- **Offer**: ' . ($conversion->offer->name ?? 'N/A'))
            ->line('- **Your Commission**: **$' . number_format($commission, 2) . '**')
            ->line('- **Conversion ID**: ' . $conversion->conversion_id)
            ->line('- **Date**: ' . $conversion->created_at->format('M d, Y H:i'))
            ->line('- **Status**: ' . ucfirst($conversion->status))
            ->line('')
            ->line($conversion->status === 'pending' 
                ? '⏳ This conversion is pending approval from the advertiser.' 
                : '✅ This conversion has been approved!')
            ->action('View Conversion', route('affiliate.dashboard'))
            ->line('Keep up the great work! 🚀');
    }

    protected function advertiserEmail(object $notifiable): MailMessage
    {
        $conversion = $this->conversion;
        $payout = $conversion->payout ?? 0;
        $affiliate = $conversion->affiliate;
        
        return (new MailMessage)
            ->subject('New Conversion on ' . ($conversion->offer->name ?? 'Your Offer'))
            ->greeting('Hello, ' . $notifiable->name . '!')
            ->line('You have a new conversion to review.')
            ->line('')
            ->line('**Conversion Details:**')
            ->line('- **Offer**: ' . ($conversion->offer->name ?? 'N/A'))
            ->line('- **Affiliate**: ' . ($affiliate->name ?? 'N/A') . ' (Tier: ' . ucfirst($affiliate->tier ?? 'bronze') . ')')
            ->line('- **Payout**: **$' . number_format($payout, 2) . '**')
            ->line('- **Conversion ID**: ' . $conversion->conversion_id)
            ->line('- **Date**: ' . $conversion->created_at->format('M d, Y H:i'))
            ->line('')
            ->line('📝 Please review and approve or reject this conversion within 7 days.')
            ->action('Review Conversion', route('advertiser.conversions.index'))
            ->line('Thank you for using DealsIntel!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $conversion = $this->conversion;
        
        if ($this->recipientType === 'affiliate') {
            return [
                'type' => 'conversion_new',
                'title' => 'New Conversion!',
                'message' => 'You earned $' . number_format($conversion->commission ?? 0, 2) . ' from ' . ($conversion->offer->name ?? 'an offer'),
                'action_url' => route('affiliate.dashboard'),
                'action_text' => 'View Details',
                'conversion_id' => $conversion->id,
                'amount' => $conversion->commission ?? 0,
            ];
        }
        
        return [
            'type' => 'conversion_new',
            'title' => 'New Conversion',
            'message' => 'New conversion on ' . ($conversion->offer->name ?? 'your offer') . ' - $' . number_format($conversion->payout ?? 0, 2),
            'action_url' => route('advertiser.conversions.index'),
            'action_text' => 'Review Now',
            'conversion_id' => $conversion->id,
            'amount' => $conversion->payout ?? 0,
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
