@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->code)

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="mb-3 mb-md-0 border-left-4">
                <h3 class="fw-bold text-body mb-1 ps-4">
                    Đơn hàng #{{ $order->code }}
                    <span class="badge bg-light-secondary text-body rounded-pill fs-6 fw-normal px-3 py-1">
                        <i class='bx bx-time-five me-1'></i>{{ $order->order_date->format('H:i - d/m/Y') }}
                    </span>
                </h3>
            </div>
        </div>
        <div class="d-flex gap-2">

            <a href="{{ route('admin.orders.print', $order->id) }}" target="_blank" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">
                <i class='bx bx-printer'></i> In hóa đơn
            </a>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
                Quay lại
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="bg-card border-0 shadow-sm rounded-4 mb-4" style="min-height: 250px;">
                <div class="card-header bg-card p-4 rounded-top-4">
                    <h5 class="fw-bold text-body mb-0">Chi tiết sản phẩm <span class="text-body small">({{ $order->items->count() }})</span></h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table modern-table align-middle mb-0 table-hover custom-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 py-3 text-body small fw-bold text-uppercase">Sản phẩm</th>
                                    <th class="text-end py-3 text-body small fw-bold text-uppercase">Đơn giá</th>
                                    <th class="text-center py-3 text-body small fw-bold text-uppercase">SL</th>
                                    <th class="text-end pe-4 py-3 text-body small fw-bold text-uppercase">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                @php
                                $product = $item->product;
                                $variant = $item->variant;

                                $img = asset('images/no-image.png');
                                if ($variant && $variant->image) $img = asset('storage/' . $variant->image);
                                elseif ($product && $product->primaryImage) $img = asset('storage/' . $product->primaryImage->image_url);
                                elseif ($product && $product->images->first()) $img = asset('storage/' . $product->images->first()->image_url);

                                $variantInfo = $variant ? $variant->attributeValues->pluck('value')->join(' / ') : '';
                                @endphp
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 border overflow-hidden flex-shrink-0" style="width: 50px; height: 50px;">
                                                <img src="{{ $img }}" alt="{{ $product->name ?? 'Deleted' }}" class="w-100 h-100 object-fit-cover">
                                            </div>
                                            <div>
                                                <div class="fw-bold text-body text-truncate" style="max-width: 250px;">{{ $product->name ?? 'Sản phẩm đã xóa' }}</div>
                                                @if($variantInfo)
                                                <div class="small text-body"><i class='bx bxs-layer me-1'></i>{{ $variantInfo }}</div>
                                                @endif
                                                <div class="small text-body font-monospace">SKU: {{ $variant->sku ?? $product->sku ?? '---' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end text-body">{{ number_format($item->price, 0, ',', '.') }}₫</td>
                                    <td class="text-center text-body"><span class="badge bg-light-secondary rounded-pill text-body">x{{ $item->quantity }}</span></td>
                                    <td class="text-end pe-4 fw-bold text-body">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}₫</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4 mb-md-0">
                    <div class="bg-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">Ghi chú & Thanh toán</h6>

                            <div class="p-3 rounded-3 bg-card border border-dashed mb-3">
                                <label class="small text-body fw-bold mb-1">Chi chú từ khách:</label>
                                <div class="fst-italic text-break text-body">
                                    "{{ $order->note ?: 'Không có ghi chú.' }}"
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 rounded-3 border">
                                <span class="small text-body fw-bold">Phương thức thanh toán:</span>
                                @if($order->payment_method == 'cod')
                                <span class="badge bg-light-secondary rounded-pill px-3 py-2 text-body border border-secondary border-opacity-10 ">COD</span>
                                @elseif($order->payment_method == 'vnpay')
                                <span class="badge bg-light-info rounded-pill px-3 py-2 text-body border">VNPAY</span>
                                @else
                                <span class="badge bg-light-primary rounded-pill px-3 py-2 text-body border">{{ $order->payment_method }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center">
                                Tổng thanh toán
                            </h6>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-body">Tổng tiền hàng:</span>
                                @php
                                $subTotal = $order->total_amount + $order->discount_amount - $order->shipping_fee;
                                @endphp
                                <span class="fw-medium">
                                    {{ number_format($subTotal > 0 ? $subTotal : 0, 0, ',', '.') }}₫
                                </span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-body">Phí vận chuyển:</span>
                                @if($order->shipping_fee > 0)
                                <span class="fw-medium">{{ number_format($order->shipping_fee, 0, ',', '.') }}₫</span>
                                @else
                                <span class="text-success fw-bold">Miễn phí</span>
                                @endif
                            </div>

                            @if($order->coupon_discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-body">
                                <span>
                                    Mã giảm giá
                                    @if($order->coupon_code)
                                    (<a href="{{ route('admin.coupons.index')}}" class="font-monospace text-decoration-none text-body">{{ $order->coupon_code }}</a>):
                                    @endif
                                </span>
                                <span class="fw-bold">-{{ number_format($order->coupon_discount, 0, ',', '.') }}₫</span>
                            </div>
                            @endif

                            @if($order->tier_discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-body">
                                <span>
                                    Ưu đãi thành viên:
                                </span>
                                <span class="fw-bold">-{{ number_format($order->tier_discount, 0, ',', '.') }}₫</span>
                            </div>
                            @endif

                            @if($order->discount_amount > 0 && $order->coupon_discount == 0 && $order->tier_discount == 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>Giảm giá</span>
                                <span class="fw-bold">-{{ number_format($order->discount_amount, 0, ',', '.') }}₫</span>
                            </div>
                            @endif

                            <hr class="my-3 border-dashed">

                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-body">Tổng thực thu:</span>
                                <span class="fw-bold text-danger fs-5">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="col-lg-4">

            <div class="bg-card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-card border-bottom p-4 rounded-top-4">
                    <h6 class="fw-bold mb-0">Trạng thái đơn hàng</h6>
                </div>
                <div class="card-body p-4 text-center">
                    @php
                    $statuses = [
                    'pending' => [
                    'label' => 'Chờ duyệt',
                    'class' => 'bg-light-warning text-warning',
                    'border' => 'warning',
                    'icon' => 'bx-loader-circle'
                    ],
                    'confirmed' => [
                    'label' => 'Đã duyệt',
                    'class' => 'bg-light-info text-info',
                    'border' => 'info',
                    'icon' => 'bx-check-circle'
                    ],
                    'shipping' => [
                    'label' => 'Đang giao',
                    'class' => 'bg-light-primary text-primary',
                    'border' => 'primary',
                    'icon' => 'bx-truck'
                    ],
                    'waiting_payment' => [
                    'label' => 'Chờ thanh toán',
                    'class' => 'bg-light-secondary text-body',
                    'border' => 'secondary',
                    'icon' => 'bx-package'
                    ],
                    'completed' => [
                    'label' => 'Hoàn tất',
                    'class' => 'bg-light-success text-success',
                    'border' => 'success',
                    'icon' => 'bx-check-double'
                    ],
                    'cancelled' => [
                    'label' => 'Đã hủy',
                    'class' => 'bg-light-danger text-danger',
                    'border' => 'danger',
                    'icon' => 'bx-x-circle'
                    ],
                    ];

                    $curr = $statuses[$order->status] ?? [
                    'label' => $order->status,
                    'class' => 'bg-light-secondary text-body',
                    'border' => 'secondary',
                    'icon' => 'bx-question-mark'
                    ];

                    $isFinished = in_array($order->status, ['completed', 'cancelled']);
                    @endphp

                    <div class="mb-3">
                        <span class="badge {{ $curr['class'] }} px-3 py-2 rounded-pill border border-{{ $curr['border'] }} border-opacity-10 d-inline-flex align-items-center gap-1 fw-normal font-monospace">

                            {{ $curr['label'] }}
                        </span>

                    </div>

                    @can('update', $order)
                    <label class="small text-body fw-bold mb-2 d-block text-start">Cập nhật trạng thái:</label>

                    @if(!$isFinished)
                    <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                        @csrf
                        <div class="input-group">
                            <select name="status" class="form-select form-control select-status shadow-none me-1" required
                                onchange="document.getElementById('btn-submit-{{ $order->id }}').disabled = false;">

                                @foreach($statuses as $key => $val)
                                @php
                                $keys = array_keys($statuses);
                                $idx = array_search($order->status, $keys);
                                $next = $keys[$idx + 1] ?? null;

                                $canSelect = ($key == $next || $key == 'cancelled');
                                @endphp

                                <option value="{{ $key }}"
                                    {{ $key == $order->status ? 'selected' : '' }}
                                    {{ !$canSelect ? 'disabled' : '' }}>

                                    {{ $val['label'] }}
                                    {{-- Thêm chú thích nhỏ để admin dễ hiểu --}}
                                    {{ $key == $order->status ? '(Hiện tại)' : '' }}
                                </option>
                                @endforeach
                            </select>

                            <button type="submit" id="btn-submit-{{ $order->id }}" class="btn bg-submit border px-3 d-flex align-items-center" disabled>
                                <i class='bx bx-check'></i>
                            </button>
                        </div>
                        <div class="form-text small mt-2 text-start text-muted d-flex align-items-center gap-1">
                            <i class='bx bx-info-circle me-1'></i>Chỉ chuyển sang trạng thái kế tiếp hoặc hủy.
                        </div>
                    </form>
                    @else
                    <div class="input-group opacity-75">
                        <select class="form-select bg-card" disabled>
                            <option selected>{{ $statuses[$order->status]['label'] ?? $order->status }}</option>
                        </select>
                        <button class="btn btn-light border" type="button" disabled>
                            <i class='bx bxs-lock-alt text-muted'></i>
                        </button>
                    </div>
                    <div class="form-text small mt-2 text-start text-muted d-flex align-items-center">
                        <i class='bx bx-block me-1'></i>Đơn hàng đã đóng, không thể chỉnh sửa.
                    </div>
                    @endif
                    @endcan
                </div>
            </div>

            <div class="bg-card border-0 shadow-sm rounded-4 mb-4 shadow-sm">
                <div class="card-header bg-white border-bottom p-4 rounded-top-4">
                    <h6 class="fw-bold mb-0">Khách hàng</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div>
                            <div class="fw-bold text-body">{{ $order->user->name ?? 'Khách vãng lai' }}</div>
                            <div class="small text-body badge bg-light-secondary text-body rounded-pill px-3 py-2 border ">{{ $order->user ? 'Thành viên' : 'Guest' }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-3">
                        <div class="text-body"><i class='bx bx-envelope fs-5'></i></div>
                        <div>
                            <div class="small text-body fw-bold">Email</div>
                            <div class="text-body fw-medium text-break">{{ $order->user->email ?? $order->email }}</div>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="text-body"><i class='bx bx-phone fs-5'></i></div>
                        <div>
                            <div class="small text-body fw-bold">Số điện thoại</div>
                            <div class="text-body fw-medium">{{ $order->phone }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-bottom p-4">
                    <h6 class="fw-bold mb-0 text-body">Giao hàng</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="text-body"><i class='bx bx-home fs-5'></i></div>
                        <div>
                            <div class="small text-body fw-bold">Địa chỉ nhận hàng</div>
                            <div class="text-body fw-medium lh-sm">{{ $order->shipping_address }}</div>
                        </div>
                    </div>

                    <hr class="border-dashed my-3">

                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-body small">Hình thức:</span>
                        <span class="text-body fw-bold">{{ $order->shipping_method ?? 'Tiêu chuẩn' }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-body small">Vận đơn:</span>
                        @if($order->tracking_number)
                        <span class="badge bg-light text-dark border font-monospace">{{ $order->tracking_number }}</span>
                        @else
                        <span class="text-body small italic">---</span>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection