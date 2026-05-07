<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $referredUser;
    public float $commissionAmount;
    public string $eventType; // 'signup', 'first_conversion', 'commission'

    /**
     * Create a new notification instance.
     */
    public function __construct(User $referredUser, float $commissionAmount, string $eventType = 'commission')
    {
        $this->referredUser = $referredUser;
        $this->commissionAmount = $commissionAmount;
        $this->eventType = $eventType;
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
        $message = (new MailMessage)
            ->subject('🎉 Referral Commission Earned!')
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line($this->getEventMessage())
            ->line('')
            ->line('**Referral Details:**')
            ->line('- **Referred User**: ' . $this->referredUser->name)
            ->line('- **Commission Earned**: $' . number_format($this->commissionAmount, 2))
            ->line('- **Event Type**: ' . ucfirst(str_replace('_', ' ', $this->eventType)))
            ->line('');
        
        if ($this->eventType === 'signup') {
            $message->line('🚀 **What\'s Next:**')
                ->line('You\'ll continue earning when your referral makes conversions!')
                ->line('Keep sharing your referral link to earn more passive income.');
        } else {
            $message->line('💰 **Your Referral Stats:**')
                ->line('Keep referring affiliates to earn ongoing commissions from their performance!');
        }
        
        $message->line('')
            ->action('View Referrals', route('affiliate.referrals.index'))
            ->line('Keep building your referral network! 🚀');
        
        return $message;
    }

    /**
     * Get event-specific message
     */
    protected function getEventMessage(): string
    {
        return match($this->eventType) {
            'signup' => 'Someone joined using your referral link! You\'ve earned a signup bonus of $' . number_format($this->commissionAmount, 2) . '.',
            'first_conversion' => 'Your referral made their first conversion! You\'ve earned $' . number_format($this->commissionAmount, 2) . ' as a first conversion bonus.',
            'commission' => 'You\'ve earned a referral commission of $' . number_format($this->commissionAmount, 2) . ' from your referral\'s performance.',
            default => 'You\'ve earned $' . number_format($this->commissionAmount, 2) . ' from your referral!',
        };
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'referral_earned',
            'title' => 'Referral Commission Earned!',
            'message' => 'You earned $' . number_format($this->commissionAmount, 2) . ' from ' . $this->referredUser->name . '\'s ' . str_replace('_', ' ', $this->eventType) . '.',
            'action_url' => route('affiliate.referrals.index'),
            'action_text' => 'View Referrals',
            'referred_user_id' => $this->referredUser->id,
            'referred_user_name' => $this->referredUser->name,
            'commission_amount' => $this->commissionAmount,
            'event_type' => $this->eventType,
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
