<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferCapWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Offer $offer;
    public string $capType;
    public int $currentCount;
    public int $capLimit;
    public float $percentageUsed;

    /**
     * Create a new notification instance.
     */
    public function __construct(Offer $offer, string $capType, int $currentCount, int $capLimit)
    {
        $this->offer = $offer;
        $this->capType = $capType; // 'daily' or 'total'
        $this->currentCount = $currentCount;
        $this->capLimit = $capLimit;
        $this->percentageUsed = ($currentCount / $capLimit) * 100;
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
        $capTypeLabel = ucfirst($this->capType);
        $emoji = $this->percentageUsed >= 90 ? '🚨' : '⚠️';
        $remaining = $this->capLimit - $this->currentCount;
        
        return (new MailMessage)
            ->subject($emoji . ' ' . $capTypeLabel . ' Cap Alert: ' . $offer->name)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Your offer ' . strtolower($capTypeLabel) . ' conversion cap is running low!')
            ->line('')
            ->line('**Cap Status:**')
            ->line('- **Offer Name**: ' . $offer->name)
            ->line('- **Cap Type**: ' . $capTypeLabel . ' Conversions')
            ->line('- **Cap Limit**: ' . number_format($this->capLimit) . ' conversions')
            ->line('- **Current**: ' . number_format($this->currentCount) . ' conversions (' . number_format($this->percentageUsed, 1) . '%)')
            ->line('- **Remaining**: ' . number_format($remaining) . ' conversions')
            ->line('')
            ->line('⚠️ **What happens when cap is reached:**')
            ->line('- Your offer will automatically pause')
            ->line('- No new conversions will be tracked')
            ->line('- Affiliates won\'t be able to promote this offer')
            ->line('')
            ->line('**Action Required:**')
            ->line($this->getActionMessage())
            ->action('Manage Offer Caps', route('advertiser.offers.edit', $offer->id))
            ->line('Keep your campaign running smoothly!');
    }

    /**
     * Get action message based on percentage used
     */
    protected function getActionMessage(): string
    {
        if ($this->percentageUsed >= 95) {
            return 'Cap almost reached! Increase your cap immediately or your offer will pause very soon.';
        } elseif ($this->percentageUsed >= 90) {
            return 'You\'ve used over 90% of your ' . $this->capType . ' cap. Consider increasing the cap soon.';
        } else {
            return 'You\'ve used over 75% of your ' . $this->capType . ' cap. Plan to increase the cap to avoid interruption.';
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
        $remaining = $this->capLimit - $this->currentCount;
        
        return [
            'type' => 'cap_warning',
            'title' => ucfirst($this->capType) . ' Cap Alert',
            'message' => $offer->name . ' has used ' . number_format($this->percentageUsed, 1) . '% of its ' . $this->capType . ' cap. Remaining: ' . number_format($remaining) . ' conversions.',
            'action_url' => route('advertiser.offers.edit', $offer->id),
            'action_text' => 'Manage Caps',
            'offer_id' => $offer->id,
            'offer_name' => $offer->name,
            'cap_type' => $this->capType,
            'current_count' => $this->currentCount,
            'cap_limit' => $this->capLimit,
            'percentage_used' => $this->percentageUsed,
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
