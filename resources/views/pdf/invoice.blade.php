<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #{{ $order->code ?? $order->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background: #f4f4f4;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .text-danger {
            color: #d32f2f;
        }
    </style>
</head>

<body>
    <h2>Hóa đơn thanh toán</h2>
    <p><strong>Mã đơn hàng:</strong> #{{ $order->code ?? $order->id }}</p>
    <p><strong>Khách hàng:</strong> {{ $order->user->name ?? 'Khách vãng lai' }}</p>
    <p><strong>Email:</strong> {{ $order->user->email ?? $order->email }}</p>
    <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
    <p><strong>Địa chỉ giao hàng:</strong> {{ $order->shipping_address }}</p>
    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Sản phẩm</th>
                <th class="text-right">Số lượng</th>
                <th class="text-right">Đơn giá</th>
                <th class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->items as $item)
            <tr>
                <td>
                    {{ $item->product->name ?? 'Sản phẩm đã xóa' }}
                    @if($item->variant)
                    <br><small>({{ $item->variant->sku }})</small>
                    @endif
                </td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                <td class="text-right">{{ number_format($item->quantity * $item->price, 0, ',', '.') }} đ</td>
            </tr>
            @endforeach

            @php
            // Tạm tính = Tổng tiền cuối + Giảm giá - Phí ship
            $subTotal = $order->total_amount + $order->discount_amount - $order->shipping_fee;
            @endphp

            <tr>
                <td colspan="3" class="text-right font-bold">Tạm tính:</td>
                <td class="text-right">{{ number_format($subTotal, 0, ',', '.') }} đ</td>
            </tr>

            <tr>
                <td colspan="3" class="text-right">Phí vận chuyển:</td>
                <td class="text-right">
                    @if($order->shipping_fee > 0)
                    {{ number_format($order->shipping_fee, 0, ',', '.') }} đ
                    @else
                    Miễn phí
                    @endif
                </td>
            </tr>

            @if($order->coupon_discount > 0)
            <tr>
                <td colspan="3" class="text-right">
                    Mã giảm giá (<strong>{{ $order->coupon_code }}</strong>):
                </td>
                <td class="text-right text-danger">
                    -{{ number_format($order->coupon_discount, 0, ',', '.') }} đ
                </td>
            </tr>
            @endif

            @if($order->tier_discount > 0)
            <tr>
                <td colspan="3" class="text-right">
                    Ưu đãi thành viên:
                </td>
                <td class="text-right text-danger">
                    -{{ number_format($order->tier_discount, 0, ',', '.') }} đ
                </td>
            </tr>
            @endif

            <tr style="background-color: #f8f9fa;">
                <td colspan="3" class="text-right font-bold" style="font-size: 16px;">TỔNG THANH TOÁN:</td>
                <td class="text-right font-bold" style="font-size: 16px; color: #d32f2f;">
                    {{ number_format($order->total_amount, 0, ',', '.') }} đ
                </td>
            </tr>
        </tbody>
    </table>

    <p style="margin-top: 20px; font-style: italic; font-size: 12px; text-align: center;">
        Cảm ơn quý khách đã mua hàng tại cửa hàng chúng tôi!
    </p>
</body>

</html>