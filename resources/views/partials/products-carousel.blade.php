<section class="categories-section">
    <div class="categories-card">
        <header class="categories-header">
            <h2 class="categories-title">{{ $title }}</h2>
        </header>
        <div id="{{ $carouselId }}" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner fade-in">
                @foreach ($products->chunk(5) as $chunkIndex => $chunk)
                <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                    <div class="products-grid">
                        @foreach ($chunk as $product)
                            @include('partials.product-card', ['product' => $product, 'ariaId' => $itemPrefix . '-'. $product->id])
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="prev" aria-label="{{ $title }} trước">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#{{ $carouselId }}" data-bs-slide="next" aria-label="{{ $title }} tiếp theo">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>
    </div>
</section>
