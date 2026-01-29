<section class="main-content" id="main-content">
    <h1 id="page-title" class="page-title">{{ $categoryName }}</h1>
    <div class="products-grid" id="mainProductsGrid">

        @forelse ($products as $product)
        @include('partials.product-card', ['product' => $product, 'ariaId' => 'main-product-'.$product->id])
        @empty
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-search" aria-hidden="true"></i>
            </div>
            <h2 class="empty-title">Chưa có sản phẩm</h2>
            <p class="empty-text">Hãy tham khảo các danh mục khác bạn nhé</p>
            <a href="{{ route('home') }}" class="empty-btn">
                <i class="fas fa-store" aria-hidden="true"></i>
                Xem tất cả sản phẩm
            </a>
        </div>
        @endforelse
    </div>

    <div class="products-loadmore" id="productsLoadMoreWrapper" data-seed="{{ request('seed') }}">
        <button type="button" id="productsLoadMoreBtn" class="loadmore-btn">Xem thêm</button>
    </div>
</section>
@if(request('scroll'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('page-title');
        if (el) {
            el.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
</script>
@endif