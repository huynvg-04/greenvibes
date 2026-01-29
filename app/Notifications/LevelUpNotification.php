<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LevelUpNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    protected $tier; 

    /**
     * Create a new notification instance.
     *
     * @param $tier Object MembershipTier
     */
    public function __construct($tier)
    {
        $this->tier = $tier;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Gửi Email chúc mừng
     */
    public function toMail($notifiable)
    {
        $benefitText = '';
        if (isset($this->tier->discount) && $this->tier->discount > 0) {
            $benefitText = ' Ưu đãi đặc quyền: Giảm ' . $this->tier->discount . '% cho mọi đơn hàng.';
        }

        return (new MailMessage)
            ->subject('🎉 Chúc mừng! Bạn đã thăng hạng ' . $this->tier->name)
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Tin vui! Nhờ sự ủng hộ nhiệt tình của bạn, hạng thành viên của bạn đã được nâng cấp lên: **' . $this->tier->name . '**.')
            ->line($benefitText)
            ->line('Cảm ơn bạn đã đồng hành cùng chúng tôi.')
            ->action('Xem hạng thành viên và ưu đãi', route('user.orders.index'));
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
            'tier_id' => $this->tier->id,
            'message' => 'Bạn vừa thăng hạng: ' . $this->tier->name . '. Xem ưu đãi ngay!',
            'link' => route('user.orders.index'), 
            'icon' => 'bx bxs-crown', 
            'color' => 'warning' 
        ];
    }
}