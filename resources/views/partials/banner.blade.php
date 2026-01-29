@if($banners->count() > 0)
<div id="carouselBanner" class="carousel slide carousel-fade banner-carousel" data-bs-ride="carousel" data-bs-interval="7000">
    <div class="carousel-inner">
        @foreach($banners as $key => $banner)
        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}" style="position: relative;">

            <img src="{{ asset('storage/' . $banner->image) }}"
                class="banner-img w-100"
                alt="{{ $banner->title }}"
                loading="{{ $key == 0 ? 'eager' : 'lazy' }}"
                style="object-fit: cover; height: 800px;">

            @if($banner->title)
            <div class="carousel-caption d-flex flex-column justify-content-center h-100 text-start text-dark"
                style="top: 0; bottom: 0; left: 10%; right: 50%;">

                <div class="text-uppercase fs-5 ls-2 mb-2 animate-slide-down delay-1">
                    Chào mừng đến Green <strong class="color-accent">Vibes</strong>
                </div>

                <h1 class="display-4 fw-bold mb-4 animate-slide-down delay-2">
                    {!! $banner->title !!}
                </h1>

                
                <div class="animate-slide-up delay-3">
                    <a href="{{ route('products.index')}}" class="btn go-to-products rounded-0 px-4 py-2 d-flex align-items-center justify-content-center">Khám phá</a>
                </div>
            </div>
            @endif

        </div>
        @endforeach
    </div>

    <button class="carousel-control-prev" type="button" data-bs-target="#carouselBanner" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true" style="background-size: 50%;"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselBanner" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true" style="background-size: 50%;"></span>
    </button>
</div>
@endif