<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TierUpgradedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public string $oldTier;
    public string $newTier;
    public float $newBonus;
    public array $tierStats;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $oldTier, string $newTier, float $newBonus, array $tierStats = [])
    {
        $this->oldTier = $oldTier;
        $this->newTier = $newTier;
        $this->newBonus = $newBonus;
        $this->tierStats = $tierStats;
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
        $tierEmojis = [
            'bronze' => '🥉',
            'silver' => '🥈',
            'gold' => '🥇',
            'platinum' => '💎',
        ];
        
        $emoji = $tierEmojis[$this->newTier] ?? '⭐';
        
        return (new MailMessage)
            ->subject('🎉 Tier Upgrade! You\'re now ' . ucfirst($this->newTier))
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('You\'ve been upgraded to **' . ucfirst($this->newTier) . ' Tier** ' . $emoji)
            ->line('')
            ->line('**Your New Benefits:**')
            ->line('- **Commission Bonus**: ' . number_format($this->newBonus, 0) . '%')
            ->line('- **Higher Priority**: Your offers get priority support')
            ->line('- **Better Access**: Easier approval for exclusive offers')
            ->line('- **Dedicated Support**: Priority customer service')
            ->line('')
            ->line('**Your Performance:**')
            ->line('- **Total Conversions**: ' . number_format($this->tierStats['conversions'] ?? 0))
            ->line('- **Total Earnings**: $' . number_format($this->tierStats['earnings'] ?? 0, 2))
            ->line('- **Conversion Rate**: ' . number_format($this->tierStats['conversion_rate'] ?? 0, 2) . '%')
            ->line('')
            ->line('🚀 **What\'s Next?**')
            ->line($this->getNextTierMessage())
            ->action('View Your Dashboard', route('affiliate.dashboard'))
            ->line('Keep up the amazing work! You\'re doing fantastic! 🌟');
    }

    /**
     * Get next tier message based on current tier
     */
    protected function getNextTierMessage(): string
    {
        return match($this->newTier) {
            'silver' => 'Reach $5,000 in earnings and 100 conversions to unlock Gold tier with 10% bonus!',
            'gold' => 'Reach $10,000 in earnings and 250 conversions to unlock Platinum tier with 15% bonus!',
            'platinum' => 'You\'ve reached the highest tier! Keep performing at this level to maintain your status.',
            default => 'Keep earning to reach the next tier!',
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
            'type' => 'tier_upgraded',
            'title' => 'Tier Upgraded!',
            'message' => 'Congratulations! You\'ve been upgraded from ' . ucfirst($this->oldTier) . ' to ' . ucfirst($this->newTier) . ' tier. Your new commission bonus is ' . number_format($this->newBonus, 0) . '%!',
            'action_url' => route('affiliate.dashboard'),
            'action_text' => 'View Dashboard',
            'old_tier' => $this->oldTier,
            'new_tier' => $this->newTier,
            'new_bonus' => $this->newBonus,
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
