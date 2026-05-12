<?php

namespace App\Notifications;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreSubscriptionExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $store;
    protected $daysRemaining;

    /**
     * Create a new notification instance.
     */
    public function __construct(Store $store, int $daysRemaining = 7)
    {
        $this->store = $store;
        $this->daysRemaining = $daysRemaining;
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
        $expiryDate = $this->store->subscription_end_date;
        $plan = $this->store->plan;

        return (new MailMessage)
            ->subject('⚠️ Store Subscription Expiring Soon - ' . $this->store->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your store subscription is expiring soon.')
            ->line('**Store:** ' . $this->store->name)
            ->line('**Current Plan:** ' . ($plan ? $plan->name : 'N/A'))
            ->line('**Expiry Date:** ' . $expiryDate->format('M d, Y') . ' (' . $this->daysRemaining . ' days remaining)')
            ->line('To avoid interruption of service, please renew your subscription before it expires.')
            ->line('**What happens if your subscription expires:**')
            ->line('• Your storefront will be deactivated')
            ->line('• Customers will not be able to access your store')
            ->line('• Orders and products will be preserved')
            ->line('• You can reactivate anytime by renewing')
            ->action('Renew Subscription', route('advertiser.store.subscription'))
            ->line('Thank you for using our Store Builder!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Subscription Expiring Soon',
            'message' => 'Your ' . $this->store->name . ' subscription expires on ' . $this->store->subscription_end_date->format('M d, Y') . ' (' . $this->daysRemaining . ' days remaining)',
            'store_id' => $this->store->id,
            'expiry_date' => $this->store->subscription_end_date,
            'days_remaining' => $this->daysRemaining,
            'action_url' => route('advertiser.store.subscription'),
            'type' => 'warning',
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'store_id' => $this->store->id,
            'expiry_date' => $this->store->subscription_end_date,
            'days_remaining' => $this->daysRemaining,
        ];
    }
}
