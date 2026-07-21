<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderNotification extends Notification
{
    use Queueable;
    
    protected $order;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Đơn hàng mới #' . $this->order->code)
            ->line('Khách hàng ' . $this->order->user->name . ' vừa đặt hàng.')
            ->action('Xem đơn hàng', route('admin.orders.show', $this->order->code));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'order_id' => $this->order->code,
            'message' => 'Đơn hàng mới #' . $this->order->code . ' từ ' . $this->order->user->name,
            'link' => route('admin.orders.show', $this->order->code),
            'icon' => 'bx bx-cart', 
            'color' => 'success'
        ];
    }
}
