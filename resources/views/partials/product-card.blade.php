@php
$hasVariants = $product->variants->isNotEmpty();
$minPrice = $hasVariants ? $product->variants->min('list_price') : $product->price;

$discount = 0;
if ($product->promotion_price && $product->promotion_price < $minPrice) {
    $discount=round((($minPrice - $product->promotion_price) / $minPrice) * 100);
    }
    $rating = round($product->reviews()->avg('rating'), 1);
    $reviewCount = $product->reviews()->count();
    @endphp

@if(isset($product->status) && $product->status == 1)
<div class="col">
    {{-- 1. Thêm d-flex flex-column để thẻ card trở thành hộp dẻo dọc --}}
    <div class="product-card-minimal h-100 position-relative bg-white rounded-3 overflow-hidden shadow-sm transition-all d-flex flex-column">

        {{-- Phần Hình Ảnh --}}
        <div class="img-wrapper position-relative mb-3 overflow-hidden bg-light">
            <a href="{{ route('products.show', $product->slug) }}" class="d-block ratio ratio-1x1">
                @if($product->primary_image)
                    <img src="{{ asset('storage/' . $product->primary_image) }}"
                         class="w-100 h-100 img-fluid object-fit-cover"
                         alt="{{ $product->name }}"
                         loading="lazy">
                @else
                    <div class="d-flex align-items-center justify-content-center w-100 h-100 text-muted bg-light">
                        <i class="fas fa-image fa-3x"></i>
                    </div>
                @endif
            </a>

            {{-- Badge --}}
            <div class="position-absolute top-0 start-0 p-2 z-2">
                @if(isset($discount) && $discount > 0)
                    <span class="badge bg-danger rounded-pill shadow-sm">-{{ $discount }}%</span>
                @elseif(isset($product->is_hot) && $product->is_hot)
                    <span class="badge bg-warning text-dark rounded-pill shadow-sm">HOT</span>
                @endif
            </div>

            {{-- Hover Actions --}}
            <div class="hover-actions d-flex justify-content-center gap-2 position-absolute w-100" style="bottom: 10px; z-index: 10;">
                <a href="{{ route('products.show', $product->slug) }}"
                   class="btn btn-light btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center action-btn"
                   data-bs-toggle="tooltip"
                   title="Xem chi tiết">
                    <i class="fas fa-eye text-dark"></i>
                </a>
                {{-- Các nút Auth giữ nguyên như cũ --}}
            </div>
        </div>

        {{-- Phần Nội Dung --}}
        {{-- 2. Thêm flex-grow-1 để phần này chiếm hết khoảng trống còn lại --}}
        <div class="card-body text-center d-flex flex-column align-items-center p-3 pt-0 flex-grow-1">
            
            {{-- Tên sản phẩm: CỐ ĐỊNH 2 DÒNG --}}
            {{-- Style này giúp tên luôn cao bằng nhau dù ngắn hay dài --}}
            <h6 class="product-title mb-1 w-100" 
                style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.5em; line-height: 1.25em;">
                <a href="{{ route('products.show', $product->slug) }}" class="text-decoration-none text-dark fw-bold">
                    {{ $product->name }}
                </a>
            </h6>

            {{-- Giá bán --}}
            <div class="product-price mb-2">
                @if($product->promotion_price && $product->promotion_price < $minPrice)
                    <span class="text-muted text-decoration-line-through me-1 small">
                        {{ number_format($minPrice, 0, ',', '.') }}₫
                    </span>
                    <span class="fw-bold text-danger fs-6">
                        {{ number_format($product->promotion_price, 0, ',', '.') }}₫
                    </span>
                @else
                    <span class="fw-bold text-dark fs-6">
                        @if(isset($hasVariants) && $hasVariants)
                            <span class="small fw-normal text-muted" style="font-size: 0.7em">Từ</span>
                        @endif
                        {{ number_format($minPrice, 0, ',', '.') }}₫
                    </span>
                @endif
            </div>

            <div class="d-flex align-items-center justify-content-center w-100 mt-auto text-muted small lh-1 py-1">
                
                <div class="d-flex align-items-center gap-1">
                    <span class="fw-bold text-dark">{{ $rating ?? 0 }}</span>
                    <i class="fas fa-star text-warning" style="font-size: 0.85em; padding-bottom: 1px;"></i>
                    <span class="text-secondary" style="font-size: 0.9em;">({{ $reviewCount ?? 0 }})</span>
                </div>

                <span class="mx-2 opacity-50">|</span>

                <div class="text-truncate" style="font-size: 0.9em;">
                    Đã bán {{ $product->completed_order_items_sum_quantity ?? 0 }}
                </div>
            </div>

        </div>

    </div>
</div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {

        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const cartBtns = document.querySelectorAll('.btn-add-cart');
        cartBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();

                const originalContent = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                this.disabled = true;

                const productId = this.getAttribute('data-id');

                fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            product_id: productId,
                            quantity: 1
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        // Reset nút bấm
                        this.innerHTML = originalContent;
                        this.disabled = false;

                        if (data.success) {
                            showToast('success', 'Đã thêm vào giỏ hàng!');
                            // Cập nhật số lượng trên Header nếu có
                            const cartCount = document.getElementById('cart-count');
                            if (cartCount) cartCount.innerText = data.total_items;
                        } else {
                            showToast('error', 'Lỗi: ' + (data.message || 'Thử lại sau.'));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        this.innerHTML = originalContent;
                        this.disabled = false;
                        showToast('error', 'Lỗi kết nối server!');
                    });
            });
        });

        const wishlistBtns = document.querySelectorAll('.btn-wishlist');
        wishlistBtns.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const icon = this.querySelector('i');
                const productId = this.getAttribute('data-id');

                const isLiked = icon.classList.contains('fas');

                if (!isLiked) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    showToast('success', 'Đã thêm vào yêu thích');
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far'); 
                    showToast('info', 'Đã xóa khỏi yêu thích');
                }

                // Gửi request ngầm
                fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });
            });
        });
    });

    function showToast(type, message) {
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999;';
            document.body.appendChild(container);
        }

        const bgClass = type === 'success' ? 'bg-success' : (type === 'error' ? 'bg-danger' : 'bg-dark');

        const toastEl = document.createElement('div');
        toastEl.className = `toast show align-items-center text-white ${bgClass} border-0 mb-2 shadow`;
        toastEl.innerHTML = `
        <div class="d-flex">
            <div class="toast-body px-3 py-2 fw-semibold">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" onclick="this.closest('.toast').remove()"></button>
        </div>
    `;

        container.appendChild(toastEl);

        setTimeout(() => {
            toastEl.style.transition = 'opacity 0.5s ease';
            toastEl.style.opacity = '0';
            setTimeout(() => toastEl.remove(), 500);
        }, 3000);
    }
</script>