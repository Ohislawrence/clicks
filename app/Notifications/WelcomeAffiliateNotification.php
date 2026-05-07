<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeAffiliateNotification extends Notification implements ShouldQueue
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
            ->subject('🎉 Welcome to DealsIntel - Start Earning Today!')
            ->greeting('Welcome, ' . $notifiable->name . '!')
            ->line('Thank you for joining DealsIntel as an affiliate partner. We\'re excited to have you on board!')
            ->line('**Here\'s how to get started:**')
            ->line('1️⃣ Browse available offers in your dashboard')
            ->line('2️⃣ Request access to offers that match your audience')
            ->line('3️⃣ Get your unique tracking links')
            ->line('4️⃣ Promote and start earning commissions!')
            ->line('')
            ->line('**Your Affiliate Details:**')
            ->line('- **Current Tier**: Bronze (0% bonus)')
            ->line('- **Commission Rate**: Base rate on all approved conversions')
            ->line('- **Payment Terms**: ' . ucfirst($notifiable->payout_frequency ?? 'monthly'))
            ->line('')
            ->line('💡 **Pro Tip**: Upload quality traffic sources to get faster approval on offers!')
            ->action('Go to Dashboard', route('affiliate.dashboard'))
            ->line('If you have any questions, our support team is here to help.')
            ->line('Happy promoting! 🚀');
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
            'message' => 'Your affiliate account has been created. Start browsing offers and earning commissions today!',
            'action_url' => route('affiliate.dashboard'),
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
