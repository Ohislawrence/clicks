<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdvertiserAccountRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(?string $reason = null)
    {
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
        $message = (new MailMessage)
            ->subject('Account Application - DealsIntel')
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Thank you for your interest in joining DealsIntel as an advertiser.')
            ->line('')
            ->line('After careful review, we regret to inform you that we are unable to approve your advertiser account at this time.');
        
        if ($this->reason) {
            $message->line('')
                ->line('**Reason:**')
                ->line($this->reason);
        }
        
        $message->line('')
            ->line('**What you can do:**')
            ->line('- Review our advertiser requirements')
            ->line('- Ensure your business information is complete')
            ->line('- Contact support if you have questions')
            ->line('- Reapply after addressing the concerns')
            ->line('')
            ->line('If you believe this decision was made in error or would like more information, please contact our support team.')
            ->action('Contact Support', url('/support'))
            ->line('Thank you for your understanding.');
        
        return $message;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'account_rejected',
            'title' => 'Account Application Declined',
            'message' => 'Your advertiser account application was not approved.' . ($this->reason ? ' Reason: ' . $this->reason : ''),
            'action_url' => url('/support'),
            'action_text' => 'Contact Support',
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
