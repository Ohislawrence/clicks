<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferBudgetWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Offer $offer;
    public float $percentageUsed;
    public float $remainingBudget;
    public string $warningLevel;

    /**
     * Create a new notification instance.
     */
    public function __construct(Offer $offer, float $percentageUsed, string $warningLevel = '75%')
    {
        $this->offer = $offer;
        $this->percentageUsed = $percentageUsed;
        $this->remainingBudget = $offer->budget - $offer->total_spent;
        $this->warningLevel = $warningLevel;
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
        $offer = $this->offer;
        $emoji = $this->percentageUsed >= 90 ? '🚨' : '⚠️';
        
        return (new MailMessage)
            ->subject($emoji . ' Budget Alert: ' . $offer->name)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Your offer budget is running low!')
            ->line('')
            ->line('**Offer Details:**')
            ->line('- **Offer Name**: ' . $offer->name)
            ->line('- **Total Budget**: $' . number_format($offer->budget, 2))
            ->line('- **Spent**: $' . number_format($offer->total_spent, 2) . ' (' . number_format($this->percentageUsed, 1) . '%)')
            ->line('- **Remaining**: $' . number_format($this->remainingBudget, 2))
            ->line('')
            ->line('⚠️ **Action Required:**')
            ->line($this->getActionMessage())
            ->action('Manage Offer Budget', route('advertiser.offers.edit', $offer->id))
            ->line('Don\'t let your campaigns stop! Top up now.');
    }

    /**
     * Get action message based on warning level
     */
    protected function getActionMessage(): string
    {
        if ($this->percentageUsed >= 95) {
            return 'Your budget is almost depleted! Add more budget immediately to avoid campaign interruption.';
        } elseif ($this->percentageUsed >= 90) {
            return 'You\'ve used over 90% of your budget. Consider adding more funds soon.';
        } else {
            return 'You\'ve used over 75% of your budget. Plan to top up to keep your campaign running.';
        }
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $offer = $this->offer;
        
        return [
            'type' => 'budget_warning',
            'title' => 'Budget Alert: ' . $offer->name,
            'message' => 'Your offer has used ' . number_format($this->percentageUsed, 1) . '% of its budget. Remaining: $' . number_format($this->remainingBudget, 2),
            'action_url' => route('advertiser.offers.edit', $offer->id),
            'action_text' => 'Manage Budget',
            'offer_id' => $offer->id,
            'offer_name' => $offer->name,
            'percentage_used' => $this->percentageUsed,
            'remaining_budget' => $this->remainingBudget,
            'warning_level' => $this->warningLevel,
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
