<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffiliateAccountRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ?string $reason;

    public function __construct(?string $reason = null)
    {
        $this->reason = $reason;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Account Application Update - DealsIntel')
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Thank you for your interest in joining DealsIntel as an affiliate.')
            ->line('')
            ->line('After reviewing your application, we are unable to approve your account at this time.');

        if ($this->reason) {
            $message->line('')
                ->line('**Reason:**')
                ->line($this->reason);
        }

        $message->line('')
            ->line('If you believe this decision was made in error or wish to provide additional information, please contact our support team.')
            ->action('Contact Support', 'mailto:support@dealsintel.com');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'affiliate_account_rejected',
            'message' => 'Your affiliate account application was not approved.' . ($this->reason ? ' Reason: ' . $this->reason : ''),
        ];
    }
}
