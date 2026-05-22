<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AffiliateAccountApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Account Approved - Welcome to DealsIntel!')
            ->greeting('Congratulations, ' . $notifiable->name . '!')
            ->line('Your affiliate account has been reviewed and approved! 🎉')
            ->line('')
            ->line('You now have full access to browse offers, generate tracking links, and start earning commissions.')
            ->line('')
            ->line('🚀 **Get Started Now:**')
            ->line('1. **Browse Offers** — Find offers that match your audience')
            ->line('2. **Request Access** — Apply to promote offers you like')
            ->line('3. **Get Your Link** — Generate your unique tracking link')
            ->line('4. **Promote & Earn** — Share your link and earn commissions')
            ->line('')
            ->action('Go to Dashboard', route('affiliate.dashboard'))
            ->line('Need help? Check the documentation or contact our support team.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'affiliate_account_approved',
            'message' => 'Your affiliate account has been approved! You can now browse and promote offers.',
        ];
    }
}
