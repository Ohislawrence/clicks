<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeAdvertiserNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        return (new MailMessage)
            ->subject('Welcome to DealsIntel - Your Account is Under Review')
            ->greeting('Welcome, ' . $notifiable->name . '!')
            ->line('Thank you for registering with DealsIntel as an advertiser. We\'re excited to partner with you!')
            ->line('**Account Status: Under Review** ⏳')
            ->line('Our team is currently reviewing your account details. This typically takes 24-48 hours.')
            ->line('')
            ->line('**What happens next?**')
            ->line('1️⃣ Our team reviews your company information')
            ->line('2️⃣ You\'ll receive an email once approved')
            ->line('3️⃣ You can then create offers and start working with affiliates')
            ->line('4️⃣ Top affiliates will promote your offers to their audiences')
            ->line('')
            ->line('**Your Company Details:**')
            ->line('- **Company**: ' . ($notifiable->company_name ?? 'N/A'))
            ->line('- **Website**: ' . ($notifiable->website ?? 'N/A'))
            ->line('- **Country**: ' . ($notifiable->country ?? 'N/A'))
            ->line('')
            ->line('💡 **While you wait**: Browse our platform and see how our performance marketing system works.')
            ->action('View Dashboard', route('advertiser.dashboard'))
            ->line('If you have any questions, feel free to reach out to our support team.')
            ->line('We\'ll notify you as soon as your account is approved!');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'welcome',
            'title' => 'Welcome to DealsIntel!',
            'message' => 'Your advertiser account is under review. You\'ll be notified once approved (typically 24-48 hours).',
            'action_url' => route('advertiser.dashboard'),
            'action_text' => 'View Dashboard',
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
