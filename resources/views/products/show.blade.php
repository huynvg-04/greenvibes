@extends('layouts.app')
@section('title', $product->name . ' - Chi tiết sản phẩm')

@section('content')
<link rel="stylesheet" href="{{ asset('css/product.css') }}">

<div class="product-detail-container">
    <nav class="breadcrumb-wrapper">
        <ul class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('home') }}">Trang chủ</a>
            </li>

            @if($product->category)
            <li class="breadcrumb-item">
                <a href="{{ route('products.index') }}">
                    Cây cảnh
                </a>
            </li>
            @endif

            <li class="breadcrumb-item active">
                {{ Str::limit($product->name, 30) }}
            </li>
        </ul>
    </nav>

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

    <div class="content-wide">
        <div class="row gx-5 justify-content-between">
            <div class="col-lg-6">
                <div class="image-wrapper">
                    @php
                    $mainImage = $images->first();
                    @endphp

                    <img src="{{ $mainImage ? asset('storage/' . $mainImage->image_url) : 'https://via.placeholder.com/600x400?text=No+Image' }}"
                        alt="{{ $product->name }}"
                        class="zoom-hover-image"
                        id="mainImage"
                        style="width: 100%; height: auto; object-fit: contain;">

                    <button class="image-zoom-btn" onclick="zoomImage()">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                <div class="thumbnail-container">
                    @foreach($images as $image)
                    @if($loop->iteration <= 5)
                        <div class="thumbnail-wrapper {{ $loop->iteration == 5 && count($images) > 5 ? 'more-images' : '' }}"
                        data-remaining="{{ count($images) - 5 }}">
                        <img src="{{ asset('storage/' . $image->image_url) }}"
                            alt="Thumbnail {{ $loop->iteration }}"
                            class="thumbnail-image"
                            onclick="changeMainImage(this.src)">
                </div>
                @endif
                @endforeach
            </div>

        </div>

        @php
        $minPrice = $product->variants->isNotEmpty() ? $product->variants->min('list_price') : $product->price;
        $maxPrice = $product->variants->isNotEmpty() ? $product->variants->max('list_price') : $product->price;

        $productAttributes = [];
        if($product->variants->isNotEmpty()) {
        foreach ($product->variants as $variant) {
        foreach ($variant->attributeValues as $attrVal) {
        $attrName = $attrVal->attribute->name;
        $productAttributes[$attrName][$attrVal->id] = $attrVal->value;
        }
        }
        }
        $variantsJson = json_encode($product->variants->load('attributeValues')->values()->toArray());
        @endphp

        <div class="col-lg-6">
            <div class="product-card">
                <div class="product-header">
                    @if($product->category)
                    <span class="badge bg-light text-dark border mb-2">{{ $product->category->name }}</span>
                    @endif
                    <div class="product-name" style="color: var(--color-dark); font-size: 24px; font-weight: bold;">{{ $product->name }}</div>
                </div>

                <div class="price-section mt-3">
                    <h2 id="price-display" style="color: var(--color-dark); font-weight: bold;">
                        @if($product->variants->count() > 0)
                        @if($minPrice == $maxPrice)
                        {{ number_format($minPrice) }}₫
                        @else
                        {{ number_format($minPrice, 0, ',', '.') }}₫ - {{ number_format($maxPrice, 0, ',', '.') }}₫
                        @endif
                        @else
                        {{ number_format($product->price, 0, ',', '.') }}₫
                        @endif
                    </h2>
                </div>

                <hr class="my-3">

                @if($product->variants->count() > 0)
                <div class="variants-section mb-1">
                    @foreach($productAttributes as $attrName => $values)
                    <div class="d-flex align-items-center gap-3 attribute-group mb-3">
                        <label class="fw-bold mb-2" style="font-size: 14px;">{{ $attrName }}:</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($values as $id => $value)
                            <label class="variant-option-label">
                                <input type="radio"
                                    name="attr_{{ \Str::slug($attrName) }}"
                                    value="{{ $id }}"
                                    class="variant-input"
                                    data-attr-name="{{ $attrName }}">
                                <span>{{ $value }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                <div class="product-stock mb-4">
                    <div id="stock-display">
                        @if($product->variants->count() > 0)
                        <span class="text-muted">Chọn phân loại sản phẩm</span>
                        @endif
                    </div>
                </div>

                @auth
                @if(auth()->user()->hasRole('customer'))
                <div class="d-flex align-items-center gap-3">
                    <form action="{{ route('user.cart.add') }}" method="POST" class="cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="product_variant_id" id="selected_variant_id">
                        <div class="d-flex align-items-center gap-3 quantity-selector mb-4">
                            <label class="fw-bold mb-0 d-block">Số lượng:</label>
                            <div class="quantity-wrapper d-flex align-items-center">
                                <button type="button" class="qty-btn minus btn btn-outline-secondary" onclick="decreaseQuantity()">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <input type="number" id="quantity" name="quantity"
                                    class="form-control text-center mx-2"
                                    value="1" min="1" style="width:50px;"
                                    oninput="checkMaxQuantity(this)">
                                <button type="button" class="qty-btn plus btn btn-outline-secondary" onclick="increaseQuantity()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <small id="max-qty-msg" class="text-danger mt-1 d-block"></small>
                        </div>

                        <button type="submit" class="btn-add-cart w-70" id="btn-add-to-cart"
                            {{ $product->variants->count() > 0 ? 'disabled' : '' }}>
                            <i class="bx bx-basket me-2"></i> Thêm vào giỏ hàng
                        </button>
                    </form>

                    <form action="{{ route('user.wishlists.toggle', $product->id) }}" method="POST" class="wishlist-form">
                        @csrf
                        <button type="submit"
                            class="btn-wishlist {{ Auth::user()->hasInWishlist($product->id) ? 'active' : '' }}">
                            <i class="{{ Auth::user()->hasInWishlist($product->id) ? 'fas' : 'far' }} fa-heart"></i>
                        </button>
                    </form>
                </div>
                @else
                <div class="alert alert-warning mt-3 border-0 rounded-0">Tài khoản quản trị website không thể đặt hàng.</div>
                @endif
                @else
                <div class="login-prompt alert alert-secondary mt-3 d-flex align-items-center justify-content-center gap-2">
                    <i class="bx bxs-lock"></i>
                    <a href="{{ route('login') }}" class="fw-bold color-accent">Đăng nhập</a> để mua hàng.
                </div>
                @endauth
                <div class="product-meta-footer mt-4 pt-3 border-top">

                    @if($product->category)
                    <div class="meta-row">
                        <span class="meta-label">Danh mục:</span>
                        <a href="{{ route('home', ['category_id' => $product->category->id]) }}" class="meta-link">
                            {{ $product->category->name }}
                        </a>
                    </div>
                    @endif

                    @if($product->tags && $product->tags->count() > 0)
                    <div class="meta-row">
                        <span class="meta-label">Từ khóa:</span>
                        <div class="meta-tags">
                            @foreach($product->tags as $tag)
                            <a href="#" class="tag-link">#{{ $tag->name }}</a>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <div class="meta-row align-items-center">
                        <span class="meta-label">Chia sẻ:</span>
                        <div class="social-share">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                target="_blank" class="share-icon fb" title="Chia sẻ lên Facebook">
                                <i class='bx bxl-facebook'></i>
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($product->name) }}"
                                target="_blank" class="share-icon tw" title="Chia sẻ lên Twitter">
                                <i class="fab fa-twitter"></i>
                            </a>

                            <a href="https://pinterest.com/pin/create/button/?url={{ urlencode(url()->current()) }}&description={{ urlencode($product->name) }}"
                                target="_blank" class="share-icon pin" title="Pin nó">
                                <i class='bx bxl-pinterest'></i>
                            </a>

                            <button onclick="copyLink()" class="share-icon link" title="Sao chép liên kết">
                                <i class="fas fa-link"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="product-tabs-container" style="margin-top: 40px;">

    <div class="product-tabs-header">
        <button class="tab-link active" onclick="openProductTab(event, 'tab-description')">
            Mô tả
        </button>
        @php
        $reviewCount = $product->reviews()->count();
        @endphp
        <button class="tab-link" onclick="openProductTab(event, 'tab-reviews')">
            Đánh giá ({{ $reviewCount }})
        </button>
    </div>

    <div id="tab-description" class="tab-content-panel active">
        <div class="description-content mb-4" style="font-family: var(--font-ui, sans-serif); color: #64748b; line-height: 1.8;">
            @if($product->description && trim($product->description) !== '')
            {!! nl2br(e($product->description)) !!}
            @else
            <p>Chưa có mô tả chi tiết cho sản phẩm này.</p>
            @endif
        </div>
    </div>

    <div id="tab-reviews" class="tab-content-panel">
        <div class="rating-summary-box">
            <div class="rating-score">
                <div class="big-score">
                    {{ number_format($averageRating ?? 0, 1) }}<span style="font-size: 18px; color: #94a3b8;">/5</span>
                </div>
                <div class="stars">
                    @php $ratingAvg = round($product->reviews()->avg('rating'), 1); @endphp
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <=floor($ratingAvg))
                        <i class="fas fa-star" style="color: #f59e0b;"></i>
                        @elseif($i == ceil($ratingAvg) && $ratingAvg - floor($ratingAvg) >= 0.5)
                        <i class="fas fa-star-half-alt" style="color: #f59e0b;"></i>
                        @else
                        <i class="far fa-star" style="color: #e2e8f0;"></i>
                        @endif
                        @endfor
                </div>
            </div>

            <form method="GET" action="{{ route('products.show', $product->slug) }}" class="filter-buttons">
                @foreach(request()->except(['star', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach

                <button type="submit" name="star" value=""
                    class="btn-filter {{ !request('star') ? 'active' : '' }}">
                    Tất cả
                </button>

                @for($i = 5; $i >= 1; $i--)
                @php
                $count = $counts[$i] ?? 0; // Đảm bảo controller truyền biến $counts
                $isActive = request('star') == $i;
                @endphp
                <button type="submit" name="star" value="{{ $i }}"
                    class="btn-filter {{ $isActive ? 'active' : '' }}">
                    {{ $i }} sao ({{ $count }})
                </button>
                @endfor
            </form>
        </div>

        <div class="review-list">
            @forelse($reviews as $review)
            <div style="border-bottom: 1px solid #f1f5f9; padding-bottom: 20px; margin-bottom: 20px;">

                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                    <strong style="color: #1e293b;">{{ $review->orderItem->order->user->name ?? 'Khách hàng' }}</strong>
                    <small style="color: #94a3b8;">{{ \Carbon\Carbon::parse($review->created_at)->format('d/m/Y') }}</small>
                </div>

                <div style="margin-bottom: 8px;">
                    @for($k = 1; $k <= 5; $k++)
                        <i class="{{ $k <= $review->rating ? 'fas' : 'far' }} fa-star"
                        style="color: {{ $k <= $review->rating ? '#f59e0b' : '#e2e8f0' }}; font-size: 12px;"></i>
                        @endfor
                </div>

                @if($review->comment)
                <p style="color: #64748b; margin: 0;">{{ $review->comment }}</p>
                @endif

                @if(!empty($review->images))
                <div class="review-images mt-2 d-flex gap-2">
                    @foreach($review->images as $img)
                    <img src="{{ asset('storage/' . $img) }}"
                        class="rounded border"
                        style="width: 60px; height: 60px; object-fit: cover; cursor: pointer;"
                        onclick="window.open(this.src)">
                    @endforeach
                </div>
                @endif

                <div class="d-flex justify-content-end align-items-center mt-2">
                    <div class="position-relative d-inline-block">

                        <div class="login-tooltip">
                            <div class="tooltip-arrow"></div>
                            Vui lòng <a href="{{ route('login') }}" class="text-white fw-bold text-decoration-underline">đăng nhập</a> để thích!
                        </div>

                        <button class="d-flex align-items-center btn btn-sm border rounded-pill px-3 btn-like-review 
                       {{ $review->isLikedBy(Auth::id()) ? 'active-like' : 'btn-light text-secondary' }}"
                            data-id="{{ $review->id }}"
                            style="transition: all 0.2s;">

                            <i class='bx {{ $review->isLikedBy(Auth::id()) ? 'bxs-heart' : 'bx-heart' }} me-1 icon-heart'></i>
                            <span class="like-count fw-bold">{{ $review->likes_count }}</span>
                        </button>

                    </div>
                </div>

            </div>
            @empty

            <div style="text-align: center; padding: 30px; color: #94a3b8;">
                <i class="far fa-comment-dots" style="font-size: 40px; margin-bottom: 10px;"></i>
                <p>Không có đánh giá nào {{ request('star') ? 'cho mức sao này' : '' }}.</p>
            </div>
            @endforelse

            <div class="mt-3">
                {{ $reviews->appends(request()->query())->links() }}
            </div>
        </div>

    </div>

</div>

</div> @if($relatedProducts->count() > 0)
<div class="related-products-section" style="margin-top: 60px; margin-bottom: 40px;">
    <div class="section-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; border-bottom: 2px solid #f1f5f9; padding-bottom: 15px;">
        <h3 style="font-size: 22px; font-weight: 700; color: #1e293b; margin: 0; position: relative;">
            Sản phẩm liên quan
            <span style="position: absolute; bottom: -17px; left: 0; width: 60px; height: 2px; background: #0f172a;"></span>
        </h3>
        <a href="#"
            style="color: #64748b; text-decoration: none; font-size: 14px; font-weight: 500; transition: color 0.2s;">
            Xem tất cả <i class="fas fa-arrow-right" style="font-size: 12px; margin-left: 4px;"></i>
        </a>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($relatedProducts as $related)
        <div class="col">
            <div class="card h-100 border-0 shadow-sm product-card" style="transition: transform 0.2s, box-shadow 0.2s;">
                <div class="position-relative overflow-hidden" style="border-radius: 8px 8px 0 0;">
                    <a href="{{ route('products.show', $related->slug) }}">
                        @php
                        // Lấy ảnh chính (is_primary=1) hoặc ảnh đầu tiên
                        $image = $related->images->where('is_primary', 1)->first() ?? $related->images->first();
                        $imageUrl = $image ? asset('storage/' . $image->image_url) : asset('images/no-image.png');
                        @endphp
                        <img src="{{ $imageUrl }}" class="card-img-top" alt="{{ $related->name }}"
                            style="height: 250px; object-fit: cover; width: 100%;">
                    </a>

                    @if($related->sale_price && $related->sale_price < $related->price)
                        <span class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 m-2 rounded small fw-bold">
                            -{{ round((($related->price - $related->sale_price) / $related->price) * 100) }}%
                        </span>
                        @endif
                </div>

                <div class="card-body d-flex flex-column p-3">
                    <h5 class="card-title mb-2" style="font-size: 16px; font-weight: 600; min-height: 40px;">
                        <a href="{{ route('products.show', $related->slug) }}" class="text-decoration-none text-dark text-truncate-2">
                            {{ Str::limit($related->name, 45) }}
                        </a>
                    </h5>

                    <div class="mb-2">
                        @php
                        $avgRel = round($related->reviews()->avg('rating') ?? 0, 1);
                        @endphp
                        <div class="small text-warning">
                            @for($r = 1; $r <= 5; $r++)
                                <i class="{{ $r <= $avgRel ? 'fas' : 'far' }} fa-star" style="font-size: 12px;"></i>
                                @endfor
                                <span class="text-muted ms-1" style="font-size: 12px;">({{ $related->reviews()->count() }})</span>
                        </div>
                    </div>

                    <div class="mt-auto d-flex align-items-center justify-content-between">
                        <div class="price-box">
                            @if($related->sale_price && $related->sale_price < $related->price)
                                <span class="d-block text-danger fw-bold">{{ number_format($related->sale_price, 0, ',', '.') }}₫</span>
                                <small class="text-muted text-decoration-line-through">{{ number_format($related->price, 0, ',', '.') }}₫</small>
                                @else
                                <span class="fw-bold text-dark">{{ number_format($related->price, 0, ',', '.') }}₫</span>
                                @endif
                        </div>

                        <a href="{{ route('products.show', $related->slug) }}"
                            class="btn btn-sm btn-outline-dark rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 32px; height: 32px;" title="Xem chi tiết">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>


@endif
<div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="border: none; padding: 24px 24px 0 24px;">
                <h5 class="modal-title" style="color: #1e293b; font-size: 20px; font-weight: 600;">
                    {{ $product->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="outline: none; box-shadow: none;"></button>
            </div>

            <div class="modal-body" style="text-align: center; padding: 24px;">
                <img src=""
                    id="modalImageContent"
                    alt="{{ $product->name }}"
                    class="img-fluid"
                    style="max-width: 100%; height: auto; max-height: 80vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
</div>
@endsection
@section('scripts')
<style>
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .login-tooltip {
        display: none;
        position: absolute;
        bottom: 120%;
        left: 50%;
        transform: translateX(-50%);
        background-color: var(--color-accent);
        color: #fff;
        padding: 8px 12px;
        font-size: 12px;
        white-space: nowrap;
        z-index: 100;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .tooltip-arrow {
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: var(--color-accent) transparent transparent transparent;
    }

    .login-tooltip.show {
        display: block;
        opacity: 1;
        animation: fadeIn 0.3s forwards;
    }

    .active-like {
        background-color: transparent !important;
        border-color: var(--color-accent) !important;
    }

    .active-like i {
        color: var(--color-accent) !important;
    }
</style>
<script src="{{ asset('js/product-detail.js') }}"></script>
<script>
    window.isLoggedIn = {{ auth()->check() ? 'true' : 'false' }};
    @php
        $totalStock = $product->variants->isNotEmpty() ? $product->variants->sum('stock') : $product->quantity;
        
        if($product->variants->isNotEmpty()) {
            $min = $product->variants->min('list_price');
            $max = $product->variants->max('list_price');
            $defaultPriceDisplay = ($min == $max) 
                ? number_format($min, 0, ',', '.') . '₫'
                : number_format($min, 0, ',', '.') . '₫ - ' . number_format($max, 0, ',', '.') . '₫';
        } else {
            $defaultPriceDisplay = number_format($product->price, 0, ',', '.') . '₫';
        }
    @endphp

    window.productConfig = {
        variants: {!! json_encode($product->variants->load('attributeValues')->values()->toArray()) !!},

        totalAttrTypes: {{ count($productAttributes ?? []) }},

        defaultStock: {{ $totalStock }},
        defaultPriceHTML: `{!! $defaultPriceDisplay !!}`,

        storageUrl: "{{ asset('storage') }}"
    };

    window.currentStock = {{ $totalStock }};

</script>
@endsection