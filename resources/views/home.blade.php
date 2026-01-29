@extends('layouts.app')

@section('title', 'Green Vibes')
@section('content')
<div class="products-container">
    @include('partials.alert')

    @include('partials.banner')

    @include('partials.categories')

    @include('partials.products-carousel', ['products' => $newProducts, 'title' => 'Sản phẩm mới nhất', 'carouselId' => 'newProductsCarousel', 'itemPrefix' => 'new-product'])

    @include('partials.products-carousel', ['products' => $bestSellerProducts, 'title' => 'Sản phẩm bán chạy', 'carouselId' => 'bestSellerCarousel', 'itemPrefix' => 'bestseller-product'])

    @include('home.main-products')

</div>

@push('scripts')
    <script src="{{ asset('js/home.js') }}?v={{ time() }}"></script>
@endpush
@endsection
