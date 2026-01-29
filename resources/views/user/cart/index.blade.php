@extends('layouts.app')
@section('title', 'Giỏ hàng của tôi')

@section('content')
<div class="cart-container">
    <div class="cart-header">
        <h1 class="cart-title">Giỏ hàng</h1>
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
    <div class="alert-modern alert-danger alert-auto">
        <i class="fas fa-exclamation-circle alert-icon"></i>
        <p class="alert-text">{{ session('error') }}</p>
        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    @endif

    <div class="cart-layout-wrapper">
        @if(isset($cart) && $cart->count() > 0)

        <div class="cart-header-row d-none d-lg-grid">
            <div class="text-left"></div> 
            <div>Sản phẩm</div>
            <div class="text-left">Đơn giá</div>
            <div class="text-left">Số lượng</div>
            <div class="text-left">Thành tiền</div>
            <div class="text-left"></div>
        </div>

        <div class="cart-list-section">
            @foreach($cart as $item)
            @php
            $product = $item->product;
            $variant = $item->variant;
            $itemName = $product->name;

            $variantInfo = '';
            if ($variant) {
            $attrs = $variant->attributeValues->map(fn($av) => $av->value)->join(' / ');
            if ($attrs) $variantInfo = "Phân loại: " . $attrs;
            }
            $maxStock = $variant ? $variant->stock : $product->quantity;

            $imgUrl = asset('images/no-image.png');
            if ($variant && $variant->image) $imgUrl = asset('storage/' . $variant->image);
            elseif ($product->primaryImage) $imgUrl = asset('storage/' . $product->primaryImage->image_url);
            elseif ($product->images->first()) $imgUrl = asset('storage/' . $product->images->first()->image_url);

            $price = $variant ? $variant->list_price : $product->price;
            $maxStock = $variant ? $variant->stock : $product->quantity;
            $promoPrice = ($product->promotion_price && $product->promotion_price < $price) ? $product->promotion_price : null;
                $finalPrice = $promoPrice ?? $price;
                @endphp

                <div class="cart-card mb-3">
                    <div class="cart-item"
                        data-id="{{ $item->id }}"
                        data-product-id="{{ $product->id }}"
                        data-variant-id="{{ $variant ? $variant->id : '' }}">

                        <div class="cart-item-content">

                            <div class="item-checkbox text-center">
                                <input type="checkbox" class="modern-checkbox item-check" {{ $item->is_selected ? 'checked' : '' }}>
                            </div>

                            <div class="d-flex align-items-center gap-3 product-info-col">
                                <div class="product-image-wrapper" style="width: 80px; height: 80px; flex-shrink: 0;">
                                    <a href="{{ route('products.show', $product->slug) }}">
                                        <img src="{{ $imgUrl }}" alt="{{ $itemName }}" class="product-image">
                                    </a>
                                </div>
                                <div class="product-detail">
                                    <div class="item-name">
                                        <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark fw-bold">
                                            {{ $itemName }}
                                        </a>
                                    </div>
                                    @if($variantInfo)
                                    <div class="item-variant">{{ $variantInfo }}</div>
                                    @endif
                                    <div class="product-meta mt-1">
                                        <span class="stock-badge">Còn {{ $maxStock }} sản phẩm</span>
                                    </div>
                                    @if($item->quantity > $maxStock)
                                    <div class="text-danger small mt-1"><i class="fas fa-exclamation-triangle"></i> Quá tồn kho ({{ $maxStock }})</div>
                                    @endif

                                    <div class="d-lg-none mt-1 fw-bold text-danger">
                                        {{ number_format($finalPrice, 0, ',', '.') }}₫
                                    </div>
                                </div>
                            </div>

                            <div class="text-center price-col d-none d-lg-block">
                                <div class="unit-price
                                    data-original="{{ $price }}"
                                    data-promo="{{ $promoPrice ?? 0 }}"
                                    data-active="{{ $promoPrice ? 'promo' : 'original' }}">
                                    @if($promoPrice)
                                    <span class="price-old">{{ number_format($price, 0, ',', '.') }}₫</span>
                                    <span class="price-current">{{ number_format($promoPrice, 0, ',', '.') }}₫</span>
                                    @else
                                    <span class="price-current">{{ number_format($price, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                            </div>

                            <div class="text-center quantity-col">
                                <div class="quantity-controls d-inline-flex">
                                    <button type="button" class="qty-btn qty-btn-minus"><i class="fas fa-minus"></i></button>
                                    <input type="number"
                                        class="qty-input quantity update-cart"
                                        value="{{ $item->quantity }}"
                                        min="1"
                                        max="{{ $maxStock }}"
                                        data-stock="{{ $maxStock }}">
                                    <button type="button" class="qty-btn qty-btn-plus"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>

                            <div class="text-center total-col d-none d-lg-block">
                                <div class="item-total">
                                    {{ number_format($finalPrice * $item->quantity, 0, ',', '.') }}₫
                                </div>
                            </div>

                            <div class="text-center remove-col">
                                <button class="remove-btn remove-cart" title="Xóa">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
        </div>

        <div style="height: 20px;"></div>

        @else
        <div class="empty-cart text-center py-5 bg-white">
            <i class="bx bx-basket fa-3x text-muted mb-3"></i>
            <h2 class="h4">Giỏ hàng trống</h2>
            <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
            <a href="/" class="btn-explore mt-3">Khám phá sản phẩm</a>
        </div>
        @endif
    </div>

    @if(isset($cart) && $cart->count() > 0)
    <div class="cart-bottom-bar">
        <div class="cart-bottom-container">
            <div class="bottom-left">
                <div class="check-all-wrapper">
                    <input type="checkbox" id="checkAll" class="modern-checkbox">
                    <label for="checkAll" class="check-label ms-2 user-select-none" style="cursor: pointer;">
                        Chọn tất cả ({{ $cart->count() }})
                    </label>
                </div>
            </div>

            <div class="bottom-right">
                <div class="total-info text-end me-4">
                    <div class="total-label">Tổng thanh toán (<span id="selected-count-text">0</span> sản phẩm):</div>
                    <div class="total-price-large" id="grandTotal">0₫</div>

                    @if(session()->has('coupon'))
                    <div class="text-success small mt-1">
                        Tiết kiệm: {{ number_format(session('coupon')['discount_amount']) }}₫
                    </div>
                    @endif

                    <span id="subtotal" style="display:none;"></span>
                </div>
                <button class="btn-checkout-large" id="checkoutBtn" disabled>
                    Đặt hàng
                </button>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="modal fade" id="confirmRemoveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center p-4">
                <p class="mb-4 fw-bold">Bạn có chắc chắn muốn xóa sản phẩm này?</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-secondary w-50" data-bs-dismiss="modal">Không</button>
                    <button type="button" class="btn btn-danger w-50" id="confirmRemoveBtn">Xóa</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@include('partials.cart-scripts')
@endsection