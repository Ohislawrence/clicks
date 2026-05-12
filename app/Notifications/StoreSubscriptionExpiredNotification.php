<?php

namespace App\Notifications;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreSubscriptionExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $store;

    /**
     * Create a new notification instance.
     */
    public function __construct(Store $store)
    {
        $this->store = $store;
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
            ->subject('⛔ Store Subscription Expired - ' . $this->store->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your store subscription has expired.')
            ->line('**Store:** ' . $this->store->name)
            ->line('**Previous Plan:** ' . ($plan ? $plan->name : 'N/A'))
            ->line('**Expired On:** ' . $expiryDate->format('M d, Y'))
            ->line('Your store has been deactivated and is no longer accessible to customers.')
            ->line('**Your data is safe:**')
            ->line('• All your products are preserved')
            ->line('• All order history is intact')
            ->line('• Store settings are retained')
            ->line('• You can reactivate anytime')
            ->line('To reactivate your store, simply renew your subscription now.')
            ->action('Renew Subscription Now', route('advertiser.store.subscription'))
            ->line('We look forward to serving you again!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Subscription Expired',
            'message' => 'Your ' . $this->store->name . ' subscription expired on ' . $this->store->subscription_end_date->format('M d, Y') . '. Renew now to reactivate.',
            'store_id' => $this->store->id,
            'expiry_date' => $this->store->subscription_end_date,
            'action_url' => route('advertiser.store.subscription'),
            'type' => 'error',
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
        ];
    }
}
