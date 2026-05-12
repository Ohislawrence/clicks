<?php

namespace App\Notifications;

use App\Models\Store;
use App\Models\StoreSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreSubscriptionRenewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $store;
    protected $subscription;

    /**
     * Create a new notification instance.
     */
    public function __construct(Store $store, StoreSubscription $subscription)
    {
        $this->store = $store;
        $this->subscription = $subscription;
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
        $plan = $this->subscription->plan;

        return (new MailMessage)
            ->subject('Store Subscription Renewed - ' . $this->store->name)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your store subscription has been successfully renewed.')
            ->line('**Store:** ' . $this->store->name)
            ->line('**Plan:** ' . $plan->name)
            ->line('**Billing Cycle:** ' . ucfirst($this->subscription->billing_cycle))
            ->line('**Amount Paid:** ₦' . number_format($this->subscription->amount, 2))
            ->line('**Subscription Period:** ' . $this->subscription->period_start->format('M d, Y') . ' - ' . $this->subscription->period_end->format('M d, Y'))
            ->line('Your store is now active and accessible to customers.')
            ->action('Manage Store', route('advertiser.store.index'))
            ->line('Thank you for continuing with us!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Subscription Renewed',
            'message' => 'Your ' . $this->store->name . ' subscription has been renewed until ' . $this->subscription->period_end->format('M d, Y'),
            'store_id' => $this->store->id,
            'subscription_id' => $this->subscription->id,
            'amount' => $this->subscription->amount,
            'period_end' => $this->subscription->period_end,
            'action_url' => route('advertiser.store.subscription'),
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
            'subscription_id' => $this->subscription->id,
            'amount' => $this->subscription->amount,
        ];
    }
}
