<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Models\OfferAccessRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfferAccessRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public OfferAccessRequest $accessRequest;
    public Offer $offer;
    public ?string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(OfferAccessRequest $accessRequest, ?string $reason = null)
    {
        $this->accessRequest = $accessRequest;
        $this->offer = $accessRequest->offer;
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
        $offer = $this->offer;
        
        $message = (new MailMessage)
            ->subject('Offer Access Request Declined - ' . $offer->name)
            ->greeting('Hello, ' . $notifiable->name)
            ->line('Unfortunately, your access request has been declined by the advertiser.')
            ->line('')
            ->line('**Declined Offer:**')
            ->line('- **Offer Name**: ' . $offer->name)
            ->line('- **Category**: ' . ($offer->category->name ?? 'N/A'))
            ->line('- **Request Date**: ' . $this->accessRequest->created_at->format('M d, Y'));
        
        if ($this->reason) {
            $message->line('')
                ->line('**Reason for Decline:**')
                ->line($this->reason);
        }
        
        $message->line('')
            ->line('**Don\'t worry!**')
            ->line('There are plenty of other great offers available:')
            ->line('- Browse offers in your niche')
            ->line('- Build your traffic quality')
            ->line('- Grow your audience reach')
            ->line('- Try again with other offers')
            ->action('Browse Offers', route('affiliate.offers.index'))
            ->line('Keep building your affiliate business! 🚀');
        
        return $message;
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
            'type' => 'offer_access_rejected',
            'title' => 'Offer Access Declined',
            'message' => 'Your request to promote "' . $offer->name . '" was declined.' . ($this->reason ? ' Reason: ' . $this->reason : ''),
            'action_url' => route('affiliate.offers.index'),
            'action_text' => 'Browse Offers',
            'offer_id' => $offer->id,
            'offer_name' => $offer->name,
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
