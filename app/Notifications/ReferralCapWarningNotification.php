<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralCapWarningNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $capType,
        public float $remaining,
        public float $total
    ) {}

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
        $percentage = (($this->total - $this->remaining) / $this->total) * 100;

        if ($this->capType === 'amount') {
            return (new MailMessage)
                ->subject('Referral Commission Cap Warning - ' . number_format($percentage, 0) . '% Reached')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your referral commission earnings are approaching the cap limit.')
                ->line('**Current Status:**')
                ->line('- Cap Progress: ' . number_format($percentage, 1) . '%')
                ->line('- Remaining: ₦' . number_format($this->remaining, 2))
                ->line('- Total Cap: ₦' . number_format($this->total, 2))
                ->line('Once you reach ₦' . number_format($this->total, 2) . ', you will no longer earn commissions from your sub-affiliates.')
                ->action('View Referral Dashboard', url('/affiliate/referrals'))
                ->line('Thank you for being a valuable member of our network!');
        } else { // time
            return (new MailMessage)
                ->subject('Referral Commission Time Cap Warning')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your referral commission period is approaching its time limit.')
                ->line('**Current Status:**')
                ->line('- Time Progress: ' . number_format($percentage, 1) . '%')
                ->line('- Remaining: ' . $this->remaining . ' months')
                ->line('- Total Duration: ' . $this->total . ' months')
                ->line('After ' . $this->total . ' months, you will no longer earn commissions from your sub-affiliates.')
                ->action('View Referral Dashboard', url('/affiliate/referrals'))
                ->line('Thank you for being a valuable member of our network!');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'referral_cap_warning',
            'cap_type' => $this->capType,
            'remaining' => $this->remaining,
            'total' => $this->total,
            'percentage' => (($this->total - $this->remaining) / $this->total) * 100,
        ];
    }
}
