<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Models\OfferAccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferAccessApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public OfferAccessRequest $accessRequest;
    public Offer $offer;

    /**
     * Create a new notification instance.
     */
    public function __construct(OfferAccessRequest $accessRequest)
    {
        $this->accessRequest = $accessRequest;
        $this->offer = $accessRequest->offer;
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
        $offer = $this->offer;
        
        return (new MailMessage)
            ->subject('✅ Offer Access Approved - ' . $offer->name)
            ->greeting('Great news, ' . $notifiable->name . '!')
            ->line('Your access request has been approved!')
            ->line('')
            ->line('**Approved Offer:**')
            ->line('- **Offer Name**: ' . $offer->name)
            ->line('- **Payout**: $' . number_format($offer->payout, 2) . ' per conversion')
            ->line('- **Category**: ' . ($offer->category->name ?? 'N/A'))
            ->line('- **Commission Type**: ' . ucfirst($offer->commission_type))
            ->line('')
            ->line('🚀 **Start promoting now!**')
            ->line('1. Create your unique tracking links')
            ->line('2. Share with your audience')
            ->line('3. Earn commissions on conversions')
            ->line('')
            ->line('📊 **Offer Performance Tips:**')
            ->line('- Use the provided creatives for best results')
            ->line('- Follow the offer terms and conditions')
            ->line('- Track your performance in real-time')
            ->action('Create Tracking Link', route('affiliate.links.create', ['offer' => $offer->id]))
            ->line('Good luck with your promotions! 🎉');
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $offer = $this->offer;
        
        return [
            'type' => 'offer_access_approved',
            'title' => 'Offer Access Approved!',
            'message' => 'You can now promote "' . $offer->name . '" and earn $' . number_format($offer->payout, 2) . ' per conversion.',
            'action_url' => route('affiliate.links.create', ['offer' => $offer->id]),
            'action_text' => 'Create Link',
            'offer_id' => $offer->id,
            'offer_name' => $offer->name,
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
