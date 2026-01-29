@extends('layouts.app')

@section('title', 'Kết quả tìm kiếm - '. $query )
@section('content')

<link rel="stylesheet" href="{{ asset('css/product-search.css') }}">
<div class="search-container">
    <header class="search-header">
        <div class="search-title">Kết quả tìm kiếm 
            <div class="search-query">{{ $query }}</div>
        </div>

        <div class="search-results-count">
            Tìm thấy {{ $products->count() }} sản phẩm
        </div>
    </header>

    @if($products->count() > 0)
    <div class="products-grid" role="list" aria-label="Danh sách sản phẩm tìm kiếm">
        @foreach ($products as $product)
        <article class="product-card">
            <div class="product-image-wrapper">
                <a href="/san-pham/{{ $product->slug }}" aria-labelledby="main-product-{{ $product->id }}">
                    @if($product->primary_image)
                    <img src="{{ asset('storage/' . $product->primary_image->image_url) }}"
                        class="product-image"
                        alt="{{ $product->name }}"
                        loading="lazy">
                    @else
                    <div class="no-image">
                        <i class="fas fa-image" aria-hidden="true"></i>
                    </div>
                    @endif
                </a>

                @php
                $discount = 0;
                if ($product->promotion_price && $product->promotion_price < $product->price) {
                    $discount = round((($product->price - $product->promotion_price) / $product->price) * 100);
                    }
                    @endphp
                    @if($discount > 0)
                    <div class="discount-badge" aria-label="Giảm giá {{ $discount }} phần trăm">
                        Giảm {{ $discount }}%
                    </div>
                    @endif
            </div>
            <div class="product-info">
                <h3 class="product-title">
                    <a href="/products/{{ $product->id }}" id="main-product-{{ $product->id }}">
                        {{ $product->name }}
                    </a>
                </h3>

                <div class="price-section">
                    @if($product->promotion_price && $product->promotion_price < $product->price)
                        <span class="price-original" aria-label="Giá gốc">
                            {{ number_format($product->price) }}₫
                        </span>
                        <div class="price-current" aria-label="Giá khuyến mãi">
                            {{ number_format($product->promotion_price) }}₫
                        </div>
                        @else
                        <div class="price-regular" aria-label="Giá bán">
                            {{ number_format($product->price) }}₫
                        </div>
                        @endif
                </div>
                <div class="product-meta">
                    @php
                    $rating = round($product->reviews()->avg('rating'), 1);
                    $reviewCount = $product->reviews()->count();
                    @endphp
                    <div class="rating-section">
                        <div class="stars" role="img" aria-label="{{ $rating }} trên 5 sao">
                            @for ($i = 1; $i <= 5; $i++)
                                @if($i <=floor($rating))
                                <i class="fas fa-star star" aria-hidden="true"></i>
                                @elseif($i == ceil($rating))
                                @if($rating - floor($rating) >= 0.75)
                                <i class="fas fa-star star" aria-hidden="true"></i>
                                @elseif($rating - floor($rating) >= 0.25)
                                <i class="fas fa-star-half-alt star" aria-hidden="true"></i>
                                @else
                                <i class="far fa-star star empty" aria-hidden="true"></i>
                                @endif
                                @else
                                <i class="far fa-star star empty" aria-hidden="true"></i>
                                @endif
                                @endfor
                        </div>
                        <span class="rating-text">{{ $rating }}</span>
                        <span class="rating-count">({{ $reviewCount }})</span>
                    </div>

                    <div class="stock-info">
                        @if($product->quantity > 0)
                        <p class="stock-available">
                            <i class="fas fa-check-circle" aria-hidden="true"></i>
                            Còn {{ $product->quantity }} sản phẩm
                        </p>
                        @else
                        <p class="stock-out">
                            <i class="fas fa-times-circle" aria-hidden="true"></i>
                            Hết hàng
                        </p>
                        @endif
                    </div>
                    <div class="sold-info">
                        <p class="sold-count">
                            Đã bán: {{ $product->completed_order_items_sum_quantity ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>
        </article>
        @endforeach
    </div>
    @else
    <div class="empty-state" role="status" aria-live="polite">
        <div class="empty-icon" aria-hidden="true"></div>
        <h2 class="empty-title">Không tìm thấy sản phẩm</h2>
        <p class="empty-text">
            Rất tiếc, chúng tôi không tìm thấy sản phẩm nào với từ khóa "<strong>{{ $query }}</strong>".
            <br>Hãy thử tìm kiếm với từ khóa khác hoặc xem tất cả sản phẩm.
        </p>
        <a href="{{ route('products.index') }}" class="empty-btn">
            <i class="fas fa-store" aria-hidden="true"></i>
            Xem tất cả sản phẩm
        </a>
    </div>
    @endif
</div>
@endsection
@section('scripts')

<script>
    const observerOptions = {
        threshold: 0.1,
        rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = "1";
                entry.target.style.transform = "translateY(0)";
            }
        });
    }, observerOptions);

    document.addEventListener("DOMContentLoaded", () => {
        const productCards = document.querySelectorAll(".product-card");
        productCards.forEach((card, index) => {
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            card.style.transition = `opacity 0.6s ease ${
            index * 0.1
        }s, transform 0.6s ease ${index * 0.1}s`;
            observer.observe(card);
        });

        const searchResults = document.querySelector(".products-grid");
        if (searchResults) {
            const resultsCount = document.querySelector(
                ".search-results-count"
            ).textContent;
            searchResults.setAttribute("aria-label", resultsCount);
        }

        const productLinks = document.querySelectorAll(".product-link");
        productLinks.forEach((link) => {
            link.addEventListener("keydown", function(e) {
                if (e.key === "Enter" || e.key === " ") {
                    e.preventDefault();
                    this.click();
                }
            });
        });

        const query = "{{ $query }}";
        if (query.length > 2) {
            highlightSearchTerms(query);
        }
    });

    function highlightSearchTerms(query) {
        const searchTerms = query
            .toLowerCase()
            .split(" ")
            .filter((term) => term.length > 2);
        const productTitles = document.querySelectorAll(".product-title");
        const productDescriptions = document.querySelectorAll(
            ".product-description"
        );

        [...productTitles, ...productDescriptions].forEach((element) => {
            let content = element.textContent;
            searchTerms.forEach((term) => {
                const regex = new RegExp(`(${term})`, "gi");
                content = content.replace(
                    regex,
                    '<mark class="highlight">$1</mark>'
                );
            });
            if (content !== element.textContent) {
                element.innerHTML = content;
            }
        });
    }

    if ("IntersectionObserver" in window) {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute("data-src");
                        imageObserver.unobserve(img);
                    }
                }
            });
        });

        // Apply lazy loading to images (if needed)
        document.querySelectorAll("img[data-src]").forEach((img) => {
            imageObserver.observe(img);
        });
    }

    // Add smooth scroll to top when clicking product links
    document.querySelectorAll(".product-link").forEach((link) => {
        link.addEventListener("click", function() {
            // Add loading state
            this.style.opacity = "0.7";
        });
    });
</script>
@endsection