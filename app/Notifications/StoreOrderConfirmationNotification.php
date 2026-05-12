<?php

namespace App\Notifications;

use App\Models\StoreOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StoreOrderConfirmationNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $store = $this->order->store;

        $message = (new MailMessage)
            ->subject('Order Confirmation - ' . $this->order->order_number)
            ->greeting('Hello ' . $this->order->customer_name . '!')
            ->line('Thank you for your order from ' . $store->name . '.')
            ->line('Your order has been confirmed and will be processed shortly.');

        // Add order items
        $message->line('**Order Details:**');
        $message->line('Order Number: **' . $this->order->order_number . '**');
        $message->line('Order Date: ' . $this->order->created_at->format('F j, Y'));

        $message->line('');
        $message->line('**Items:**');
        foreach ($this->order->items as $item) {
            $message->line('• ' . $item['name'] . ' (x' . $item['quantity'] . ') - ₦' . number_format($item['price'], 2));
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

        $message->line('');
        $message->line('If you have any questions, please contact the store:');
        if ($store->email) {
            $message->line('Email: ' . $store->email);
        }
        if ($store->phone) {
            $message->line('Phone: ' . $store->phone);
        }

        $message->action('View Store', route('storefront.show', $store->slug))
            ->line('Thank you for shopping with us!');

        return $message;
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
