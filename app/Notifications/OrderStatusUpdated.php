<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusUpdated extends Notification
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
            ->subject('Cập nhật đơn hàng #' . $this->order->code)
            ->line('Đơn hàng của bạn đã chuyển sang trạng thái: ' . $this->order->status)
            ->action('Chi tiết đơn hàng', route('user.orders.index', $this->order->code));
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
            'order_id' => $this->order->id,
            'message' => 'Đơn hàng #' . $this->order->code . ' đã cập nhật trạng thái: ' . $this->order->status,
            'link' => route('user.orders.index', $this->order->code),
            'icon' => 'bx bx-package',
            'color' => 'primary'
        ];
    }
}
