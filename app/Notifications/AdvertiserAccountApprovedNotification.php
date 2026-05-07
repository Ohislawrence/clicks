<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdvertiserAccountApprovedNotification extends Notification implements ShouldQueue
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
            ->subject('✅ Account Approved - Welcome to DealsIntel!')
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('Your advertiser account has been approved! 🎉')
            ->line('')
            ->line('You now have full access to our affiliate marketing platform.')
            ->line('')
            ->line('🚀 **Get Started Now:**')
            ->line('1. **Create Your First Offer** - Set up campaigns and define payouts')
            ->line('2. **Add Creatives** - Upload banners and marketing materials')
            ->line('3. **Set Tracking** - Configure conversion tracking (pixel or postback)')
            ->line('4. **Manage Affiliates** - Review affiliate access requests')
            ->line('')
            ->line('📊 **Platform Features:**')
            ->line('- Real-time conversion tracking')
            ->line('- Comprehensive analytics dashboard')
            ->line('- Fraud detection and prevention')
            ->line('- Dedicated support team')
            ->line('')
            ->action('Go to Dashboard', route('advertiser.dashboard'))
            ->line('Need help getting started? Contact our support team anytime.');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'account_approved',
            'title' => 'Account Approved!',
            'message' => 'Your advertiser account has been approved. You can now create offers and start working with affiliates.',
            'action_url' => route('advertiser.dashboard'),
            'action_text' => 'Go to Dashboard',
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
