@extends('layouts.app')
@section('content')
<div class="container py-5 my-5 text-center" style="min-height: 100vh;">
    <div class="text-success mb-3"><i class="fas fa-check-circle fa-5x" style="color: var(--color-accent);"></i></div>
    <h2>Đặt hàng thành công!</h2>
    <p>Mã đơn hàng: <strong>{{ $order->code }}</strong></p>
    <p>Tổng tiền: {{ number_format($order->total_amount) }}₫</p>
    <h4>Cảm ơn bạn đã lựa chọn Green Vibes</h4>
    <h6>Kiểm tra thông tin hóa đơn trong mail bạn nhé.</h6>
    <a href="{{ route('user.orders.index') }}" class="btn btn-primary mt-3">Xem đơn hàng</a>
</div>
@endsection