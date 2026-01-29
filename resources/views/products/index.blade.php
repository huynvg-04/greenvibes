@extends('layouts.app')

@section('title', 'Sản phẩm - ' . config('app.name'))

@section('content')
<div class="products-container">
    <nav class="breadcrumb-wrapper">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item active">Sản phẩm</li>
        </ul>
    </nav>

    <div class="content-wide py-4">
        <div class="row mt-0">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 pb-2 border-bottom">
                <div class="mb-3-1 mb-md-0 product-filter-nav">
                    <a href="{{ route('products.index') }}" class="nav-link d-inline-block {{ !request('category') ? 'active' : '' }}">Tất cả</a>
                    @foreach($categories->take(4) as $cat)
                    <a href="{{ route('products.index', array_merge(request()->query(), ['category' => $cat->slug])) }}"
                        class="nav-link d-inline-block {{ request('category') == $cat->slug ? 'active' : '' }}">
                        {{ $cat->name }}
                    </a>
                    @endforeach
                </div>

                <div class="d-flex align-items-center gap-4">
                    <div class="dropdown">
                        <button class="btn btn-link text-dark text-decoration-none dropdown-toggle p-0 fw-500 font-heading" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            @switch(request('sort'))
                            @case('popularity') Phổ biến nhất @break
                            @case('rating') Điểm đánh giá @break
                            @case('date') Mới nhất @break
                            @case('price_asc') Giá: Thấp đến Cao @break
                            @case('price_desc') Giá: Cao đến Thấp @break
                            @default Mặc định
                            @endswitch
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2">
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'default']) }}">Sắp xếp mặc định</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'popularity']) }}">Sắp xếp theo độ phổ biến</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}">Sắp xếp theo đánh giá trung bình</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}">Sắp xếp theo mới nhất</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}">Sắp xếp theo giá: thấp đến cao</a></li>
                            <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}">Sắp xếp theo giá: cao đến thấp</a></li>
                        </ul>
                    </div>

                    <div class="filter-toggle d-flex align-items-center text-dark" onclick="toggleFilterPanel()" style="cursor: pointer;">
                        <span class="fw-500 me-2 font-heading">Bộ lọc</span>
                        <i class='bx bx-chevron-down' id="filterIcon" style="font-size: 20px;"></i>
                    </div>
                </div>
            </div>

            <div id="filterPanel" class="bg-light w-100 mb-4 overflow-hidden " style="max-height: 0; transition: all 0.4s; opacity: 0;">
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold mb-3 font-heading">Sắp xếp theo</h6>
                            <ul class="list-unstyled filter-list">
                                <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'default']) }}" class="{{ !request('sort') || request('sort')=='default' ? 'active' : '' }}">Mặc định</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'popularity']) }}" class="{{ request('sort')=='popularity' ? 'active' : '' }}">Được mua nhiều</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'rating']) }}" class="{{ request('sort')=='rating' ? 'active' : '' }}">Đánh giá</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'date']) }}" class="{{ request('sort')=='date' ? 'active' : '' }}">Mới ra mắt</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_asc']) }}" class="{{ request('sort')=='price_asc' ? 'active' : '' }}">Giá: Thấp đến Cao</a></li>
                                <li><a href="{{ request()->fullUrlWithQuery(['sort' => 'price_desc']) }}" class="{{ request('sort')=='price_desc' ? 'active' : '' }}">Giá: Cao đến Thấp</a></li>
                            </ul>
                        </div>

                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold mb-3 font-heading">Lọc theo giá</h6>
                            <ul class="list-unstyled filter-list">
                                <li><a href="{{ route('products.index', request()->except(['min_price', 'max_price', 'page'])) }}" class="{{ !request('min_price') ? 'active' : '' }}">Tất cả</a></li>
                                <li><a href="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price', 'page']), ['max_price' => 200000])) }}" class="{{ request('max_price') == 200000 ? 'active' : '' }}">0₫ - 200.000₫</a></li>
                                <li><a href="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price', 'page']), ['min_price' => 200000, 'max_price' => 400000])) }}" class="{{ request('min_price') == 200000 ? 'active' : '' }}">200.000₫ - 400.000₫</a></li>
                                <li><a href="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price', 'page']), ['min_price' => 400000, 'max_price' => 600000])) }}" class="{{ request('min_price') == 400000 ? 'active' : '' }}">400.000₫ - 600.000₫</a></li>
                                <li><a href="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price', 'page']), ['min_price' => 600000, 'max_price' => 800000])) }}" class="{{ request('min_price') == 600000 ? 'active' : '' }}">600.000₫ - 800.000₫</a></li>
                                <li><a href="{{ route('products.index', array_merge(request()->except(['min_price', 'max_price', 'page']), ['min_price' => 800000])) }}" class="{{ request('min_price') == 800000 ? 'active' : '' }}">800.000₫+</a></li>
                            </ul>
                        </div>

                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold mb-3 font-heading">Danh mục</h6>
                            <ul class="list-unstyled filter-list">
                                <li><a href="{{ route('products.index', request()->except(['category', 'page'])) }}" class="{{ !request('category') ? 'active' : '' }}">Tất cả danh mục</a></li>
                                @foreach($categories as $cat)
                                <li>
                                    <div class="d-flex justify-content-between pe-3">
                                        <a href="{{ route('products.index', array_merge(request()->except(['category', 'page']), ['category' => $cat->slug])) }}" class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                                            {{ $cat->name }}
                                        </a>
                                        <span class="text-muted small">({{ $cat->products_count }})</span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 order-1">


                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4 mt-0">
                    @forelse($products as $product)
                    @php

                    $minVariant = $product->variants->sortBy('sale_price')->first();
                    $displayPrice = $minVariant ? $minVariant->sale_price : ($product->price ?? 0);
                    $originalPrice = $minVariant ? $minVariant->price : ($product->price ?? 0);

                    $img = $product->images->where('is_primary', 1)->first() ?? $product->images->first();
                    $imgUrl = $img ? asset('storage/' . $img->image_url) : asset('images/no-image.png');
                    @endphp

                    <div class="col">
                        <div class="product-card-minimal h-100 position-relative">
                            <div class="img-wrapper position-relative mb-3 overflow-hidden">
                                <a href="{{ route('products.show', $product->slug) }}">
                                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="img-fluid w-100 product-img">
                                </a>

                                @if($product->discount_percent > 0)
                                <span class="badge-circle badge-sale">-{{ $product->discount_percent }}%</span>
                                @elseif($product->is_hot)
                                <span class="badge-circle badge-hot">HOT</span>
                                @endif

                                <div class="hover-actions">
                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-light btn-sm rounded-pill shadow-sm px-3 fw-500">Xem chi tiết</a>
                                </div>
                            </div>

                            <div class="text-center d-flex flex-column align-items-center justify-content-center">
                                <h6 class="product-title mb-1">
                                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                                </h6>
                                <div class="product-price">
                                    @if($originalPrice > $displayPrice)
                                    <span class="text-muted text-decoration-line-through me-2 small">{{ number_format($originalPrice, 0, ',', '.') }}₫</span>
                                    <span class="fw-bold text-dark">{{ number_format($displayPrice, 0, ',', '.') }}₫</span>
                                    @else
                                    <span class="fw-bold text-dark">{{ number_format($displayPrice, 0, ',', '.') }}₫</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-muted">Không tìm thấy sản phẩm nào.</p>
                    </div>
                    @endforelse
                </div>

                <div class="mt-5 d-flex justify-content-center">
                    {{ $products->links() }}
                </div>
            </div>

            <div class="col-lg-3 order-2 ps-lg-5 mt-5 mt-lg-0">

                <div class="sidebar-widget mb-5">
                    <h5 class="widget-title mb-4 font-heading">Danh mục</h5>
                    <ul class="list-unstyled category-list">
                        @foreach($categories as $cat)
                        <li class="mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('products.index', array_merge(request()->except('page'), ['category' => $cat->slug])) }}"
                                    class="{{ request('category') == $cat->slug ? 'active' : '' }}">
                                    {{ $cat->name }}
                                </a>
                                <span class="count text-muted small">({{ $cat->products_count }})</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-widget mb-5">
                    <h5 class="widget-title mb-4 font-heading">Instagram</h5>
                    <div class="row g-2">
                        @for($i=0; $i<6; $i++)
                            <div class="col-4">
                            <div class="bg-light ratio ratio-1x1 rounded position-relative overflow-hidden">
                                <i class="fab fa-instagram position-absolute top-50 start-50 translate-middle text-muted opacity-25"></i>
                            </div>
                    </div>
                    @endfor
                </div>
            </div>

            @if(isset($tags) && $tags->count() > 0)
            <div class="sidebar-widget">
                <h5 class="widget-title mb-4 font-heading">Tags</h5>
                <div class="tag-cloud">
                    @foreach($tags as $tag)
                    <a href="{{ route('products.index', ['tag' => $tag->slug]) }}"
                        class="tag-link d-inline-block text-muted text-decoration-none small mb-1 me-1">
                        #{{ $tag->name }},
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>
</div>
</div>
@endsection

@section('scripts')
<script>
    function toggleFilterPanel() {
        var panel = document.getElementById('filterPanel');
        var icon = document.getElementById('filterIcon');

        if (panel.style.maxHeight && panel.style.maxHeight !== "0px") {
            panel.style.maxHeight = "0px";
            panel.style.opacity = "1";
            panel.style.marginBottom = "0";

            icon.classList.remove('rotate-180');

        } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
            panel.style.opacity = "1";
            panel.style.marginBottom = "1.5rem";

            icon.classList.add('rotate-180');
        }
    }
</script>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('/css/products.css') }}">
@endpush