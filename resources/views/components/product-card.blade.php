<article class="product-card">
    <div class="product-image-wrapper">
        <a href="{{ route('products.show', $product->slug) }}">
            @if($image)
                <img src="{{ $image }}" 
                     class="product-image" 
                     alt="{{ $product->name }}" 
                     loading="lazy">
            @else
                <div class="no-image">
                    <i class="fas fa-image" aria-hidden="true"></i>
                </div>
            @endif
        </a>

        @if($discountPercent > 0)
            <div class="discount-badge">Giảm {{ $discountPercent }}%</div>
        @endif
    </div>

    <div class="product-info">
        <h3 class="product-title">
            <a href="{{ route('products.show', $product->slug) }}">
                {{ $product->name }}
            </a>
        </h3>

        <div class="price-section">
            {!! $priceHtml !!}
        </div>

        <div class="product-meta">
            <div class="rating-section">
                <div class="stars" aria-label="{{ $rating }} trên 5 sao">
                    @for ($i = 1; $i <= 5; $i++)
                        @if($i <= floor($rating))
                            <i class="fas fa-star star"></i>
                        @elseif($i == ceil($rating) && $rating - floor($rating) >= 0.5)
                            <i class="fas fa-star-half-alt star"></i>
                        @else
                            <i class="far fa-star star empty"></i>
                        @endif
                    @endfor
                </div>
                <span class="rating-count">({{ $reviewCount }})</span>
            </div>

            <div class="sold-info">
                <p class="sold-count">Đã bán: {{ $soldCount }}</p>
            </div>
        </div>
    </div>
</article>