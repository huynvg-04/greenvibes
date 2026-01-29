@extends('layouts.app')
@section('title', 'Đơn hàng của tôi')

@section('content')
<link rel="stylesheet" href="{{ asset('css/order.css') }}">
<div class="orders-container">

    <div class="orders-header">
        <h1 class="orders-title">Đơn hàng</h1>
    </div>

    @if(session('success'))
    <div class="alert-modern alert-success alert-auto">
        <i class="fas fa-check-circle alert-icon"></i>
        <p class="alert-text">{{ session('success') }}</p>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-modern alert-error alert-auto">
        <i class="fas fa-exclamation-circle alert-icon"></i>
        <p class="alert-text">{{ session('error') }}</p>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="filter-bar fade-in">
        <form method="GET" action="{{ route('user.orders.index') }}" class="filter-form">
            <div class="filter-group">
                <input type="text" name="keyword" class="filter-input" placeholder="Tìm đơn hàng hoặc sản phẩm..."
                    value="{{ request('keyword') }}">
            </div>

            <div class="filter-group">
                <select name="status" class="filter-select">
                    <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>Tất cả trạng thái</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                    <option value="shipping" {{ request('status') == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                    <option value="waiting_payment" {{ request('status') == 'waiting_payment' ? 'selected' : '' }}>Chờ thanh toán</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>

            <div class="filter-group price-range">
                <input type="number" name="price_min" class="filter-input" placeholder="Giá từ"
                    value="{{ request('price_min') }}">
                <input type="number" name="price_max" class="filter-input" placeholder="Đến"
                    value="{{ request('price_max') }}">
            </div>

            <button type="submit" class="btn-filter">Lọc</button>

            @if(request()->hasAny(['keyword', 'status', 'price_min', 'price_max']))
            <a href="{{ route('user.orders.index') }}" class="btn-secondary">Xóa lọc</a>
            @endif
        </form>
    </div>
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 text-center border-end">
                    <div class="mb-2">Hạng thành viên</div>

                    @php
                    $currentTier = $profile->tier;
                    @endphp

                    <div class="badge rounded-pill fs-5 px-4 py-2"
                        style="background-color: {{ $currentTier->color_hex ?? '#6c757d' }}; color: #fff; text-shadow: 0 0 2px rgba(0,0,0,0.5);">
                        {{ $currentTier->name ?? 'Thành viên Mới' }}
                    </div>

                    @if($profile->level_expires_at)
                    <div class="small text-danger mt-2">
                        Hết hạn: {{ \Carbon\Carbon::parse($profile->level_expires_at)->format('d/m/Y') }}
                    </div>
                    @endif
                    @if($currentTier && $currentTier->discount > 0)
                    <div class="mt-3 pt-3 border-top mx-4 d-flex align-items-center gap-2 justify-content-center">
                        <div class="color-accent fw-bold">
                            <i class="bx bx-gift"></i> Quyền lợi:
                        </div>

                        <div class="d-flex align-items-center gap-2 justify-content-center">
                            <span class="fs-4 fw-bold color-accent">
                                -{{ (float)$currentTier->discount }}%
                            </span>
                            <span class="small text-muted">cho mọi đơn hàng</span>

                            @php
                            $remaining = 0;
                            $hasLimit = $currentTier->usage_limit > 0;

                            if ($hasLimit) {
                            // Query đếm số lần đã dùng
                            $query = \App\Models\TierUsageLog::where('user_id', $profile->user_id)
                            ->where('membership_tier_id', $currentTier->id);

                            // Lọc theo chu kỳ
                            if ($currentTier->usage_period == 'month') {
                            $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                            } elseif ($currentTier->usage_period == 'year') {
                            $query->whereYear('created_at', now()->year);
                            }

                            $usedCount = $query->count();
                            $remaining = max(0, $currentTier->usage_limit - $usedCount);
                            }
                            @endphp

                            <div class="badge bg-light text-secondary border fw-normal px-2 py-1 d-flex flex-column align-items-center rounded-0">
                                <div class="mb-1">
                                    <i class="fas fa-sync-alt me-1 text-muted" style="font-size: 10px;"></i>
                                    @if($hasLimit)
                                    {{ $currentTier->usage_limit }} lần /
                                    @switch($currentTier->usage_period)
                                    @case('month') tháng @break
                                    @case('year') năm @break
                                    @default trọn đời
                                    @endswitch
                                    @else
                                    Không giới hạn
                                    @endif
                                </div>

                                @if($hasLimit)
                                <div class="small border-top pt-1 w-100 text-center" style="font-size: 0.85em;">
                                    Còn lại:
                                    <span class="fw-bold color-accent {{ $remaining == 0 }}">
                                        {{ $remaining }}
                                    </span>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="col-md-8">
                    <div class="d-flex justify-content-between">
                        <span>Tổng chi tiêu: <strong>{{ number_format($profile->total_spent_lifetime) }}đ</strong></span>
                        <span>Tổng đơn hàng: <strong>{{ $profile->total_orders_lifetime }}</strong></span>
                    </div>

                    @if($nextTier)
                    @php
                    // 1. Tính tiến độ Tiền
                    $percentMoney = 100;
                    if($nextTier->min_spent > 0) {
                    $percentMoney = ($profile->total_spent_lifetime / $nextTier->min_spent) * 100;
                    }

                    // 2. Tính tiến độ Đơn hàng
                    $percentOrders = 100;
                    if($nextTier->min_orders > 0) {
                    $percentOrders = ($profile->total_orders_lifetime / $nextTier->min_orders) * 100;
                    }

                    // lấy % nhỏ nhất (điều kiện khó nhất) để hiển thị
                    $finalPercent = min($percentMoney, $percentOrders);
                    $finalPercent = min(100, $finalPercent);

                    // tính số lượng còn thiếu
                    $missingMoney = max(0, $nextTier->min_spent - $profile->total_spent_lifetime);
                    $missingOrders = max(0, $nextTier->min_orders - $profile->total_orders_lifetime);
                    @endphp

                    <div class="progress mt-3" style="height: 15px; background-color: #e9ecef;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            style="width: {{ $finalPercent }}%; background-color: {{ $nextTier->color_hex ?? '#0d6efd' }}"
                            aria-valuenow="{{ $finalPercent }}" aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <div class="mt-2 small text-muted">
                        Để thăng hạng lên <strong style="color:{{ $nextTier->color_hex ?? '#000' }}">{{ $nextTier->display_name ?? $nextTier->name }}</strong>, bạn cần thêm:
                        <ul class="mb-0 mt-1 ps-3">
                            @if($missingMoney > 0)
                            <li>Chi tiêu: <span class="fw-bold text-dark">{{ number_format($missingMoney) }}đ</span></li>
                            @endif

                            @if($missingOrders > 0)
                            <li>Đơn hàng: <span class="fw-bold text-dark">{{ $missingOrders }} đơn</span></li>
                            @endif
                        </ul>
                    </div>

                    @else
                    <div class="alert alert-success mt-3 mb-0 py-2 text-center">
                        <i class="fas fa-crown text-warning me-1"></i> Chúc mừng! Bạn đang ở hạng cao nhất.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @forelse($orders as $order)
    <div class="order-card" id="order-{{ $order->id }}">
        <div class="order-header">
            <div class="order-info">
                <h3 class="order-number">Đơn hàng #{{ $order->code }}</h3>
                <div class="order-meta">
                    <span class="order-date">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                    <span class="order-total">
                        {{ number_format($order->total_amount, '0', ',', '.') }}₫
                        @if($order->discount_amount > 0)
                        <small class="text-dark ms-2" title="Đã giảm giá">(-{{ number_format($order->discount_amount, '0', ',', '.') }}₫)</small>
                        @endif
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                @foreach($order->items as $item)
                @if($order->status == 'completed' && !$item->review)
                <span class="badge bg-warning text-dark ms-2 float-end">Chưa đánh giá</span>
                @endif
                @endforeach
                <span class="status-badge status-{{ $order->status }}">
                    @if($order->status == 'pending')
                    <span class="status-dot"></span> Đang xử lý
                    @elseif ($order->status == 'shipping')
                    <span class="status-dot"></span> Đang giao hàng
                    @elseif($order->status == 'completed')
                    <span class="status-dot"></span> Hoàn thành
                    @elseif($order->status == 'waiting_payment')
                    <span class="status-dot"></span> Chờ thanh toán
                    @elseif($order->status == 'cancelled')
                    <span class="status-dot"></span> Đã hủy
                    @elseif($order->status == 'confirmed')
                    <span class="status-dot"></span> Đã xác nhận
                    @endif
                </span>

                <div class="order-actions" style="border-radius: 0px;">
                    <div class="action-buttons">
                        @if($order->status == 'shipping' || $order->status == 'waiting_payment')
                        <button type="button"
                            class="btn-primary"
                            style="border-radius: 0px; font-family: var(--font-ui);"
                            data-action="{{ route('user.orders.complete', $order->code) }}"
                            data-payment-method="{{ $order->payment_method }}"
                            data-bs-toggle="modal"
                            data-bs-target="#confirmReceiveModal">
                            Đã nhận được hàng
                        </button>
                        @endif
                        @php
                        $isCompleted = $order->status == 'completed';
                        $isWithinTime = $order->created_at->addDays(30)->isFuture();
                        $returnRequest = \App\Models\OrderReturn::where('order_id', $order->id)->first();
                        @endphp

                        @if($isCompleted && $isWithinTime)
                        @if(!$returnRequest)
                        <a href="{{ route('user.orders.return', $order) }}"
                            class="btn-secondary text-danger border-danger"
                            style="border-radius: 0px; text-decoration: none;">
                            <i class="fas fa-undo-alt me-1"></i> Yêu cầu hoàn hàng
                        </a>
                        @else
                        @if($returnRequest->status == 'pending')
                        <button class="btn-secondary" disabled style="border-radius: 0px; cursor: default; background: #fff3cd; color: #856404; border: 1px solid #ffeeba;">
                            Chờ xử lý hoàn hàng
                        </button>
                        @elseif($returnRequest->status == 'approved')
                        <button class="btn-secondary" disabled style="border-radius: 0px; cursor: default; background: #d1e7dd; color: #0f5132; border: 1px solid #badbcc;">
                            Chấp nhận hoàn hàng
                        </button>
                        @elseif($returnRequest->status == 'rejected')
                        <button class="btn-secondary" disabled style="border-radius: 0px; cursor: default; background: #f8d7da; color: #842029; border: 1px solid #f5c2c7;">
                            Từ chối hoàn hàng
                        </button>
                        @endif
                        @endif
                        @endif

                        @if($order->status == 'pending')
                        <button type="button"
                            class="btn-secondary btn-danger btn-cancel"
                            data-action="{{ route('user.orders.cancel', $order->id) }}"
                            data-bs-toggle="modal"
                            data-bs-target="#cancelOrderModal">
                            Hủy đơn
                        </button>
                        @endif

                        <button
                            class="btn-secondary toggle-details"
                            style="border-radius: 0px;"
                            onclick="toggleOrderDetails('{{ $order->id }}', this)">
                            Chi tiết
                            <i class="bx bx-chevron-down toggle-icon"></i>
                        </button>

                    </div>
                </div>
            </div>
        </div>

        <div class="order-details" id="details-{{ $order->id }}" style="display: none;">
            <div class="order-items">
                <h4 class="details-title">Sản phẩm trong đơn hàng</h4>
                <div class="items-list">
                    @foreach($order->items as $item)
                    @php
                    $product = $item->product;
                    $variant = $item->variant;

                    $variantInfo = '';
                    if ($variant) {
                    $variantInfo = $variant->attributeValues->map(fn($v) => $v->value)->join(' / ');
                    }

                    $img = asset('images/no-image.png');
                    if ($variant && $variant->image) {
                    $img = asset('storage/' . $variant->image);
                    } elseif ($product && $product->primaryImage) {
                    $img = asset('storage/' . $product->primaryImage->image_url);
                    } elseif ($product && $product->images->first()) {
                    $img = asset('storage/' . $product->images->first()->image_url);
                    }
                    @endphp

                    <div class="order-item-wrapper">


                        <div class="order-item d-flex align-items-start p-3 border-bottom">
                            <div class="item-image me-3" style="width: 60px; height: 60px; flex-shrink: 0;">
                                <img src="{{ $img }}" alt="{{ $item->product_name }}" class="img-fluid rounded border w-100 h-100 object-fit-cover">
                            </div>

                            <div class="item-info flex-grow-1">
                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="color-accent"
                                    style="cursor: pointer;">
                                    {{ $product->name }}
                                </a>

                                @if($variantInfo)
                                <small class="text-muted d-block mb-1">Phân loại: {{ $variantInfo }}</small>
                                @endif

                                <div class="item-meta small text-muted">
                                    <span class="item-quantity">x{{ $item->quantity }}</span>
                                </div>
                            </div>

                            <div class="item-pricing text-end">
                                <div class="item-subtotal text-success fw-bold">
                                    {{ number_format($item->price * $item->quantity, '0', ',', '.') }}₫
                                </div>
                                <small class="text-muted d-block">{{ number_format($item->price, '0', ',', '.') }}₫/sản phẩm</small>
                            </div>
                        </div>

                        @if($order->status == 'completed')
                        <div class="review-action mt-2 text-end">
                            @if($item->review)
                            <div class="bg-light p-3 text-start d-inline-block border" style="min-width: 100%; max-width: 100%;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <small class="fw-bold text-success"><i class="fas fa-check-circle"></i> Đánh giá của bạn:</small>
                                    <div class="text-warning small">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="{{ $i <= $item->review->rating ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                    </div>
                                </div>

                                <div class="text-muted fst-italic mb-2" style="font-size: 0.95rem;">
                                    "{{ $item->review->comment }}"
                                </div>

                                @if(!empty($item->review->images))
                                <div class="review-images d-flex gap-2 mt-2 pt-2 border-top">
                                    @foreach($item->review->images as $reviewImg)
                                    <a href="{{ asset('storage/' . $reviewImg) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $reviewImg) }}"
                                            class="border"
                                            style="width: 60px; height: 60px; object-fit: cover; transition: transform 0.2s;"
                                            onmouseover="this.style.transform='scale(1.1)'"
                                            onmouseout="this.style.transform='scale(1)'">
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @else
                            <button type="button"
                                class="btn btn-outline-warning btn-sm btn-write-review"
                                data-item-id="{{ $item->id }}"
                                data-product-id="{{ $product->id }}"
                                data-product-name="{{ $item->product_name ?? $product->name }}"
                                data-bs-toggle="modal"
                                data-bs-target="#reviewModal">
                                <i class="far fa-star"></i> Viết đánh giá
                            </button>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>

                <div class="order-summary mt-4 p-3 bg-light border">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Địa chỉ nhận hàng:</strong> {{ $order->shipping_address }}</p>
                            <p class="mb-1"><strong>SĐT:</strong> {{ $order->phone }}</p>
                            @if($order->note)
                            <p class="mb-1"><strong>Ghi chú:</strong> <em class="text-muted">{{ $order->note }}</em></p>
                            @endif
                        </div>
                        <div class="col-md-6 text-end">


                            @php
                            $subTotal = $order->total_amount + $order->discount_amount - $order->shipping_fee;
                            @endphp
                            <p class="mb-1">
                                Tạm tính: {{ number_format($subTotal > 0 ? $subTotal : 0, 0, ',', '.') }}₫
                            </p>
                            <p class="mb-1">
                                Phí vận chuyển:
                                @if($order->shipping_fee > 0)
                                {{ number_format($order->shipping_fee, 0, ',', '.') }}₫
                                @else
                                <span class="text-success fw-bold">Miễn phí</span>
                                @endif
                            </p>
                            @if($order->coupon_discount > 0)
                            <p class="mb-1 text-success">
                                Mã giảm giá (<strong>{{ $order->coupon_code }}</strong>):
                                -{{ number_format($order->coupon_discount, 0, ',', '.') }}₫
                            </p>
                            @endif

                            @if($order->tier_discount > 0)
                            <p class="mb-1 text-primary">
                                Ưu đãi thành viên:
                                -{{ number_format($order->tier_discount, 0, ',', '.') }}₫
                            </p>
                            @endif

                            @if($order->discount_amount > 0 && $order->coupon_discount == 0 && $order->tier_discount == 0)
                            <p class="mb-1 text-success">
                                Giảm giá: -{{ number_format($order->discount_amount, 0, ',', '.') }}₫
                            </p>
                            @endif

                            <h5 class="mt-2 text-danger fw-bold">
                                Tổng tiền: {{ number_format($order->total_amount, 0, ',', '.') }}₫
                            </h5>

                            <small class="text-muted">
                                Phương thức thanh toán:
                                @if($order->payment_method == 'cod')
                                Thanh toán khi nhận hàng (COD)
                                @elseif($order->payment_method == 'vnpay')
                                Thanh toán qua VNPAY
                                @else
                                {{ $order->payment_method }}
                                @endif
                            </small>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    @empty
    <div class="empty-state">
        <h3 class="empty-title">Chưa có đơn hàng nào</h3>
        <a href="{{ route('home') }}" class="btn-primary">Mua sắm ngay</a>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper">
    {{ $orders->links('pagination::bootstrap-4') }}
</div>

<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Xác nhận hủy đơn</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                Bạn có chắc chắn muốn <strong>hủy đơn hàng</strong> này không?<br>
                Hành động này không thể hoàn tác.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <form id="cancelOrderForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmReceiveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-3 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Xác nhận nhận hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="receive-confirm-text" class="mb-2 small"></div>

                <small class="text-muted fst-italic mt-2 d-block">
                    * Trạng thái đơn hàng sẽ được chuyển thành <strong>Hoàn thành</strong>.
                </small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chưa nhận</button>
                <form id="confirmReceiveForm" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-success fw-bold">Xác nhận đã nhận</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Đánh giá sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="fw-bold color-accent" id="review-product-name">Tên sản phẩm</p>

                <form id="reviewForm" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="review-item-id" name="order_item_id">

                    <div class="mb-3 text-center">
                        <label class="form-label d-block">Mức độ hài lòng</label>
                        <div class="rating-stars fs-1" style="cursor: pointer;">
                            <i class='bx bx-star star' data-value="1"></i>
                            <i class='bx bx-star star' data-value="2"></i>
                            <i class='bx bx-star star' data-value="3"></i>
                            <i class='bx bx-star star' data-value="4"></i>
                            <i class='bx bx-star star' data-value="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="review-rating" value="5">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Chia sẻ cảm nhận của bạn</label>
                        <textarea name="comment" class="form-control" rows="3" placeholder="Viết cảm nhận của bạn"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Thêm hình ảnh (Tối đa 5 ảnh)</label>
                        <div class="d-flex flex-wrap gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <div class="upload-box-wrapper">
                                <input type="file" id="file-input-{{ $i }}" name="images[]" class="d-none file-input-custom" accept="image/*">
                                <div class="upload-box d-flex justify-content-center align-items-center border rounded"
                                    data-target="#file-input-{{ $i }}"
                                    style="width: 80px; height: 80px; border-style: dashed !important; cursor: pointer; position: relative; overflow: hidden;">

                                    <span class="plus-icon text-muted" style="font-size: 24px;">+</span>
                                    <img src="" class="img-preview w-100 h-100 object-fit-cover d-none">

                                    <span class="remove-btn position-absolute top-0 end-0 bg-danger text-white rounded-circle d-flex justify-content-center align-items-center d-none"
                                        style="width: 20px; height: 20px; font-size: 12px; cursor: pointer; margin: 2px; z-index: 10;">
                                        &times;
                                    </span>
                                </div>
                        </div>
                        @endfor
                    </div>
                    <div class="form-text small text-muted mt-2">
                        Nhấn vào ô trống để thêm ảnh. Nhấn vào dấu X để xóa ảnh.
                    </div>
            </div>

            <div class="text-end">
                <button type="button" class="btn btn-secondary me-1" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('/js/order.js') }}?v={{ time() }}"></script>
@endpush