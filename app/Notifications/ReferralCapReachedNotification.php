<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferralCapReachedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public string $capType,
        public float $capValue
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
        if ($this->capType === 'amount') {
            return (new MailMessage)
                ->subject('Referral Commission Cap Reached')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your referral commission has reached its maximum limit.')
                ->line('**Cap Details:**')
                ->line('- Total Earned: ₦' . number_format($this->capValue, 2))
                ->line('- Cap Limit: ₦' . number_format($this->capValue, 2))
                ->line('You will no longer receive commissions from your sub-affiliates going forward.')
                ->line('Your sub-affiliates can continue earning their own commissions without any impact.')
                ->action('View Referral Dashboard', url('/affiliate/referrals'))
                ->line('Thank you for being a valuable member of our network!');
        } else { // time
            return (new MailMessage)
                ->subject('Referral Commission Period Ended')
                ->greeting('Hello ' . $notifiable->name . '!')
                ->line('Your referral commission period has reached its time limit.')
                ->line('**Period Details:**')
                ->line('- Duration: ' . $this->capValue . ' months')
                ->line('- Status: Completed')
                ->line('You will no longer receive commissions from your sub-affiliates going forward.')
                ->line('Your sub-affiliates can continue earning their own commissions without any impact.')
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
            'type' => 'referral_cap_reached',
            'cap_type' => $this->capType,
            'cap_value' => $this->capValue,
            'reached_at' => now()->toIso8601String(),
        ];
    }
}
