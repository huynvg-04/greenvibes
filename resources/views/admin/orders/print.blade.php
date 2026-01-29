<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $order->code }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 14px; line-height: 1.5; color: #333; }
        .invoice-container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #eee; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; border-bottom: 2px solid #eee; padding-bottom: 20px; }
        .company-info h2 { margin: 0 0 5px; color: #2c3e50; }
        .invoice-details { text-align: right; }
        .invoice-details h3 { margin: 0 0 5px; color: #e74c3c; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 48%; }
        .info-title { font-weight: bold; text-transform: uppercase; font-size: 12px; color: #7f8c8d; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { background: #f8f9fa; text-align: left; padding: 10px; border-bottom: 2px solid #ddd; font-weight: bold; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-section { float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; }
        .total-row.final { font-weight: bold; font-size: 18px; color: #2c3e50; border-top: 2px solid #eee; margin-top: 10px; padding-top: 10px; }
        .footer { clear: both; margin-top: 50px; text-align: center; font-size: 12px; color: #7f8c8d; border-top: 1px solid #eee; padding-top: 20px; }
        
        .print-btn {
            position: fixed; bottom: 30px; right: 30px;
            background: #2980b9; color: white; border: none; padding: 12px 24px;
            border-radius: 50px; cursor: pointer; font-weight: bold; box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .print-btn:hover { background: #3498db; }

        @media print {
            .print-btn { display: none; }
            .invoice-container { border: none; padding: 0; }
            body { -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body>

    <button onclick="window.print()" class="print-btn">In Hóa Đơn</button>

    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h2>GREEN VIBES</h2> <p>Địa chỉ: An Khánh, Hà Nội</p>
                <p>Hotline: +84 799 735 697</p>
            </div>
            <div class="invoice-details">
                <h3>HÓA ĐƠN BÁN HÀNG</h3>
                <p>Mã đơn: <strong>#{{ $order->code }}</strong></p>
                <p>Ngày đặt: {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : '---' }}</p>
                <p>Trạng thái: {{ $order->payment_method == 'cod' ? 'Thanh toán khi nhận hàng' : 'Đã thanh toán' }}</p>
            </div>
        </div>

        <div class="info-section">
            <div class="info-box">
                <div class="info-title">Người mua hàng</div>
                <p><strong>{{ $order->user->name ?? 'Khách vãng lai' }}</strong></p>
                <p>SĐT: {{ $order->phone }}</p>
                <p>Email: {{ $order->user->email }}</p>
            </div>
            <div class="info-box text-right">
                <div class="info-title">Địa chỉ nhận hàng</div>
                <p>{{ $order->shipping_address }}</p>
                @if($order->note)
                <p style="font-style: italic; margin-top: 5px;">"Ghi chú: {{ $order->note }}"</p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 45%">Sản phẩm</th>
                    <th class="text-center">Đơn giá</th>
                    <th class="text-center">SL</th>
                    <th class="text-right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        {{ $item->product->name }}
                        @if($item->variant)
                            <br><small style="color: #666;">Phân loại: {{ $item->variant->attributeValues->pluck('value')->join(' / ') }}</small>
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-section">
            <div class="total-row">
                <span>Tổng tiền hàng:</span>
                <span>{{ number_format($order->total_amount - $order->shipping_fee + $order->discount_amount, 0, ',', '.') }}₫</span>
            </div>
            
            @if($order->shipping_fee > 0)
            <div class="total-row">
                <span>Phí vận chuyển:</span>
                <span>{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
            </div>
            @endif

            @if($order->discount_amount > 0)
            <div class="total-row" style="color: #27ae60;">
                <span>Giảm giá:</span>
                <span>-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
            </div>
            @endif

            <div class="total-row final">
                <span>TỔNG THANH TOÁN:</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
            </div>
        </div>

        <div class="footer">
            <p>Cảm ơn quý khách đã mua hàng!</p>
            <p>Mọi thắc mắc xin liên hệ hotline hoặc website.</p>
        </div>
    </div>

    <script>
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>