<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\OrderReturn;

class NewReturnRequestNotification extends Notification
{
    use Queueable;

    protected $returnRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(OrderReturn $returnRequest)
    {
        $this->returnRequest = $returnRequest;
    }
    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
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
        return (new MailMessage)
            ->subject('[Admin] Yêu cầu hoàn hàng mới #' . $this->returnRequest->order->code)
            ->greeting("Xin chào {$notifiable->name},")
            ->line("Có một yêu cầu hoàn hàng mới từ khách hàng.")
            ->line("Mã đơn hàng: **{$this->returnRequest->order->code}**")
            ->line("Lý do: {$this->returnRequest->reason}")
            ->action('Xử lý ngay', route('admin.returns.show', $this->returnRequest->order->code))
            ->line('Vui lòng kiểm tra và xử lý sớm.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray(object $notifiable): array
    {
        return [
            'return_id' => $this->returnRequest->order->code,
            'title'     => 'Yêu cầu hoàn hàng mới',
            'message'   => "Khách hàng {$this->returnRequest->user->name} yêu cầu hoàn đơn #{$this->returnRequest->order->code}",
            'icon'      => 'bx bx-undo',
            'color'     => 'success',
            'bg_color'  => '#fff3cd',
            'link'      => route('admin.returns.show', $this->returnRequest->order->code),
        ];
    }
}
