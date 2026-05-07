<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAdvertiserAccountNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $advertiser;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $advertiser)
    {
        $this->advertiser = $advertiser;
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
        $advertiser = $this->advertiser;
        
        return (new MailMessage)
            ->subject('🔔 New Advertiser Account Requires Review')
            ->greeting('Hello Admin,')
            ->line('A new advertiser account has been created and requires your review.')
            ->line('')
            ->line('**Advertiser Details:**')
            ->line('- **Name**: ' . $advertiser->name)
            ->line('- **Email**: ' . $advertiser->email)
            ->line('- **Company**: ' . ($advertiser->company_name ?? 'N/A'))
            ->line('- **Website**: ' . ($advertiser->website ?? 'N/A'))
            ->line('- **Country**: ' . ($advertiser->country ?? 'N/A'))
            ->line('- **Registered**: ' . $advertiser->created_at->format('M d, Y h:i A'))
            ->line('')
            ->line('**Review Checklist:**')
            ->line('☑️ Verify company information')
            ->line('☑️ Check website legitimacy')
            ->line('☑️ Review business model')
            ->line('☑️ Assess fraud risk')
            ->line('')
            ->line('⚡ **Action Required:**')
            ->line('Please review this account and approve or reject within 24-48 hours.')
            ->action('Review Advertiser Account', route('admin.users.show', $advertiser->id))
            ->line('Keep our platform safe and trustworthy!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $advertiser = $this->advertiser;
        
        return [
            'type' => 'new_advertiser_account',
            'title' => 'New Advertiser Requires Review',
            'message' => $advertiser->name . ' (' . $advertiser->email . ') has registered as an advertiser and needs account approval.',
            'action_url' => route('admin.users.show', $advertiser->id),
            'action_text' => 'Review Account',
            'user_id' => $advertiser->id,
            'user_name' => $advertiser->name,
            'user_email' => $advertiser->email,
            'company_name' => $advertiser->company_name,
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
