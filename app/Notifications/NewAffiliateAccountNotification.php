<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAffiliateAccountNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $affiliate;

    public function __construct(User $affiliate)
    {
        $this->affiliate = $affiliate;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🆕 New Affiliate Application - Action Required')
            ->greeting('Hello, Admin')
            ->line('A new affiliate has registered and is awaiting your approval.')
            ->line('')
            ->line('**Applicant Details:**')
            ->line('• **Name:** ' . $this->affiliate->name)
            ->line('• **Email:** ' . $this->affiliate->email)
            ->line('• **Country:** ' . ($this->affiliate->country ?? 'Not specified'))
            ->line('• **Phone:** ' . ($this->affiliate->phone ?? 'Not provided'))
            ->line('• **Registered:** ' . $this->affiliate->created_at->format('M d, Y H:i'))
            ->line('')
            ->action('Review Application', route('admin.users.show', $this->affiliate->id))
            ->line('Please review and approve or reject this application.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_affiliate_application',
            'affiliate_id' => $this->affiliate->id,
            'affiliate_name' => $this->affiliate->name,
            'affiliate_email' => $this->affiliate->email,
            'message' => 'New affiliate application from ' . $this->affiliate->name . ' is awaiting approval.',
        ];
    }
}

