<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\OrderReturn;

class ReturnStatusUpdated extends Notification
{
    use Queueable;

    protected $return;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(OrderReturn $return)
    {
        $this->return = $return;
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
    public function toMail(object $notifiable): MailMessage
    {
        $statusText = $this->return->status === 'approved' ? 'ĐÃ ĐƯỢC DUYỆT' : 'ĐÃ BỊ TỪ CHỐI';
        $color = $this->return->status === 'approved' ? 'success' : 'error';

        return (new MailMessage)
            ->subject("[Cập nhật] Yêu cầu hoàn hàng - {$statusText}")
            ->greeting("Xin chào {$notifiable->name},")
            ->line("Yêu cầu hoàn hàng cho đơn #{$this->return->order->code} của bạn {$statusText}.")
            ->line("Ghi chú từ người bán hàng: " . ($this->return->admin_note ?? 'Không có'))
            ->action('Xem chi tiết', route('user.orders.index', $this->return->order_id))
            ->line('Cảm ơn bạn đã mua sắm tại cửa hàng!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */

    public function toArray(object $notifiable): array
    {
        $isApproved = $this->return->status === 'approved';

        return [
            'return_id' => $this->return->id,
            'title'     => $isApproved ? 'Yêu cầu được duyệt' : 'Yêu cầu bị từ chối',
            'message'   => "Yêu cầu hoàn hàng của đơn #{$this->return->order->code} đã được xử lý.",
            'icon'      => $isApproved ? 'bx bx-check' : 'bx bx-x', 
            'color'     => $isApproved ? '#198754' : '#dc3545',     
            'bg_color'  => $isApproved ? '#d1e7dd' : '#f8d7da',     
            'link'      => route('user.orders.index', $this->return->order_id),
        ];
    }
}
