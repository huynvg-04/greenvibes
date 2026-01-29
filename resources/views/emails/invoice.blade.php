@component('mail::message')
# Hóa đơn thanh toán

Xin chào {{ $order->user->name }},

Bạn đã thanh toán thành công đơn hàng **#{{ $order->code }}**.

**Chi tiết đơn hàng:**
- Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ
- Trạng thái: {{ ucfirst($order->status) }}
- Ngày đặt: {{ $order->updated_at->format('d/m/Y H:i') }}

@component('mail::button', ['url' => route('user.orders.index', $order->code)])
Xem chi tiết đơn hàng
@endcomponent

Cảm ơn bạn đã tin tưởng {{ config('app.name') }}!

@endcomponent
