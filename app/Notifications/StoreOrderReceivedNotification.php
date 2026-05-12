<?php

namespace App\Notifications;

use App\Models\StoreOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreOrderReceivedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(StoreOrder $order)
    {
        $this->order = $order;
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
        $store = $this->order->store;

        $message = (new MailMessage)
            ->subject('New Order - ' . $this->order->order_number)
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('You have received a new order on your store: **' . $store->name . '**')
            ->line('Order Number: **' . $this->order->order_number . '**')
            ->line('Order Date: ' . $this->order->created_at->format('F j, Y g:i A'));

        // Customer details
        $message->line('');
        $message->line('**Customer Information:**');
        $message->line('Name: ' . $this->order->customer_name);
        $message->line('Email: ' . $this->order->customer_email);
        if ($this->order->customer_phone) {
            $message->line('Phone: ' . $this->order->customer_phone);
        }

        // Order items
        $message->line('');
        $message->line('**Order Items:**');
        foreach ($this->order->items as $item) {
            $message->line('• ' . $item['name'] . ' (x' . $item['quantity'] . ') - ₦' . number_format($item['price'] * $item['quantity'], 2));
        }

        $message->line('');
        $message->line('Subtotal: ₦' . number_format($this->order->subtotal, 2));
        if ($this->order->shipping_fee > 0) {
            $message->line('Shipping: ₦' . number_format($this->order->shipping_fee, 2));
        }
        $message->line('**Total: ₦' . number_format($this->order->total, 2) . '**');

        if ($this->order->shipping_address) {
            $message->line('');
            $message->line('**Shipping Address:**');
            $message->line($this->order->shipping_address);
        }

        $message->action('View Order Details', route('advertiser.store.orders.show', $this->order->id))
            ->line('Please process this order as soon as possible.');

        return $message;
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'New Order Received',
            'message' => 'Order #' . $this->order->order_number . ' - ₦' . number_format($this->order->total, 2),
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'store_id' => $this->order->store_id,
            'customer_name' => $this->order->customer_name,
            'total' => $this->order->total,
            'action_url' => route('advertiser.store.orders.show', $this->order->id),
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
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'total' => $this->order->total,
        ];
    }
}
