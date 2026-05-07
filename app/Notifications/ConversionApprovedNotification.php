<?php

namespace App\Notifications;

use App\Models\Conversion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConversionApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Conversion $conversion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Conversion $conversion)
    {
        $this->conversion = $conversion;
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
        
        return (new MailMessage)
            ->subject('✅ Conversion Approved - $' . number_format($commission, 2) . ' Added to Your Balance')
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('Your conversion has been approved by the advertiser!')
            ->line('')
            ->line('**Approved Conversion:**')
            ->line('- **Offer**: ' . ($conversion->offer->name ?? 'N/A'))
            ->line('- **Commission**: **$' . number_format($commission, 2) . '**')
            ->line('- **Conversion ID**: ' . $conversion->conversion_id)
            ->line('- **Date**: ' . $conversion->created_at->format('M d, Y H:i'))
            ->line('- **Approved**: ' . $conversion->updated_at->format('M d, Y H:i'))
            ->line('')
            ->line('💵 Your commission has been added to your pending balance.')
            ->line('Once it\'s released from the hold period, you can request a payout.')
            ->action('View Balance', route('affiliate.payouts.index'))
            ->line('Keep promoting and earning! 🚀');
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
            'type' => 'conversion_approved',
            'title' => 'Conversion Approved!',
            'message' => 'Your commission of $' . number_format($conversion->commission ?? 0, 2) . ' has been approved and added to your balance.',
            'action_url' => route('affiliate.payouts.index'),
            'action_text' => 'View Balance',
            'conversion_id' => $conversion->id,
            'amount' => $conversion->commission ?? 0,
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
