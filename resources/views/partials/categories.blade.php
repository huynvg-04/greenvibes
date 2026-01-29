<section class="categories-section">
    <div class="categories-card">
        <header class="categories-header">
            <h2 class="categories-title">Danh mục sản phẩm</h2>
        </header>

        <div class="categories-grid-wrapper">
            <div class="categories-grid" id="categoriesGrid">
                <a href="{{ route('home') }}#page-title" class="category-card fade-in">
                    <div class="category-icon">
                        <div class="category-image">
                            <i class="fas fa-th-large" aria-hidden="true"></i>
                        </div>
                    </div>
                    <div class="category-name">Tất cả sản phẩm</div>
                </a>
                @foreach ($categories as $category)
                @if($category->type == 'product')
                <a href="{{ route('home', ['category_id' => $category->id]) }}#page-title" class="category-card fade-in">
                    <div class="category-icon">
                        @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}"
                            class="category-image"
                            alt="{{ $category->name }}"
                            loading="lazy">
                        @else
                        <div class="category-image">
                            <i class="fas fa-image" aria-hidden="true"></i>
                        </div>
                        @endif
                    </div>
                    <div class="category-name">{{ $category->name }}</div>
                </a>
                @endif  
                @endforeach
            </div>

            <div class="categories-pagination" id="categoriesPagination" aria-label="Phân trang danh mục" hidden>
                <button type="button" class="cat-page-btn" id="catPrev" aria-label="Trang trước">&laquo; Prev</button>
                <span id="catPageInfo" class="cat-page-info" aria-live="polite"></span>
                <button type="button" class="cat-page-btn" id="catNext" aria-label="Trang tiếp">Next &raquo;</button>
            </div>
        </div>
    </div>
</section>