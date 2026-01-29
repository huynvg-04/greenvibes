@extends('layouts.app')

@section('title', $blog->title)

@section('content')
<div class="blog-detail-wrapper py-5 bg-white">
    <div class="container">
        <div class="row">
            
            <div class="col-lg-8 pe-lg-5">
                
                <div class="mb-3">
                    <a href="{{ route('user.blogs.index') }}" class="text-decoration-none text-muted small text-uppercase">Blog</a>
                    <span class="mx-2 text-muted">/</span>
                    @if($blog->category)
                        <a href="{{ route('user.blogs.index', ['category' => $blog->category->slug]) }}" class="text-primary fw-bold text-decoration-none small text-uppercase">
                            {{ $blog->category->name }}
                        </a>
                    @endif
                </div>

                <h1 class="fw-bold display-5 mb-4 font-heading" style="line-height: 1.3;">
                    {{ $blog->title }}
                </h1>

                <div class="d-flex align-items-center mb-4 text-muted small">
                    <div class="d-flex align-items-center me-4">
                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                            <i class='bx bx-user fs-5'></i>
                        </div>
                        <div>
                            <span class="d-block fw-bold text-dark">{{ $blog->user->name ?? 'Admin' }}</span>
                            <span>Tác giả</span>
                        </div>
                    </div>
                    
                    <div class="me-4">
                        <i class='bx bx-calendar me-1'></i> {{ $blog->created_at->format('d/m/Y') }}
                    </div>

                    <div>
                        <i class='bx bx-show me-1'></i> {{ $blog->views }} Lượt xem
                    </div>
                </div>

                @if($blog->thumbnail)
                <div class="mb-5 rounded overflow-hidden shadow-sm">
                    <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="w-100 h-auto">
                </div>
                @endif

                <div class="blog-content mb-5">
                    {{-- Sử dụng {!! !!} để render HTML từ database --}}
                    {!! $blog->content !!}
                </div>

                <div class="border-top border-bottom py-4 mb-5 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    
                    <div class="tags">
                        <span class="fw-bold me-2">Tags:</span>
                        <a href="#" class="badge bg-light text-dark text-decoration-none border">Cây cảnh</a>
                        <a href="#" class="badge bg-light text-dark text-decoration-none border">Decor</a>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold">Thích bài viết:</span>
                        
                        <div class="position-relative d-inline-block">
                            <div class="login-tooltip">
                                <div class="tooltip-arrow"></div>
                                Vui lòng <a href="{{ route('login') }}" class="text-white fw-bold text-decoration-underline">đăng nhập</a> để thích!
                            </div>

                            <button class="btn btn-outline-danger rounded-pill px-4 like-btn {{ $blog->isLikedByAuthUser() ? 'active-like' : '' }}" 
                                    data-id="{{ $blog->id }}"
                                    style="transition: all 0.3s;">
                                <i class='bx {{ $blog->isLikedByAuthUser() ? 'bxs-heart' : 'bx-heart' }} me-1 icon-heart'></i>
                                <span class="like-count fw-bold">{{ $blog->likes_count }}</span>
                            </button>
                        </div>
                        </div>
                </div>

                @if($relatedBlogs->count() > 0)
                <div class="related-posts mb-5">
                    <h3 class="fw-bold mb-4 font-heading border-start border-4  ps-3">Bài viết liên quan</h3>
                    <div class="row g-4">
                        @foreach($relatedBlogs as $related)
                        <div class="col-md-4">
                            <div class="card h-100 border-0 shadow-sm">
                                <div class="overflow-hidden" style="height: 180px;">
                                    <a href="{{ route('user.blogs.show', $related->slug) }}">
                                        @if($related->thumbnail)
                                            <img src="{{ asset('storage/' . $related->thumbnail) }}" class="w-100 h-100 object-fit-cover hover-zoom" alt="{{ $related->title }}">
                                        @else
                                            <div class="w-100 h-100 bg-light d-flex align-items-center justify-content-center"><i class='bx bx-image fs-1 text-muted'></i></div>
                                        @endif
                                    </a>
                                </div>
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-2">
                                        <a href="{{ route('user.blogs.show', $related->slug) }}" class="text-decoration-none text-dark hover-green">
                                            {{ Str::limit($related->title, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted"><i class='bx bx-calendar'></i> {{ $related->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

            </div>

            <div class="col-lg-4 ps-lg-4 mt-5 mt-lg-0">
                
                <div class="sidebar-widget mb-5">
                    <h5 class="font-heading fw-bold mb-4 position-relative pb-2 border-bottom">Tìm kiếm</h5>
                    <form action="{{ route('user.blogs.index') }}" method="GET">
                        <div class="input-group">
                            <input type="text" name="keyword" class="form-control border-end-0 bg-light" placeholder="Nhập từ khóa..." value="{{ request('keyword') }}">
                            <button class="btn btn-light border border-start-0" type="submit"><i class='bx bx-search text-muted'></i></button>
                        </div>
                    </form>
                </div>

                <div class="sidebar-widget mb-5">
                    <h5 class="font-heading fw-bold mb-4 position-relative pb-2 border-bottom">Danh mục</h5>
                    <ul class="list-unstyled">
                        @foreach($categories as $cat)
                        <li class="mb-2">
                            <a href="{{ route('user.blogs.index', ['category' => $cat->slug]) }}" 
                               class="d-flex justify-content-between text-decoration-none text-secondary py-2 border-bottom-dashed cat-link">
                                <span><i class='bx bx-chevron-right me-2 text-muted'></i> {{ $cat->name }}</span>
                                <span class="badge bg-light text-dark border h-100">{{ $cat->blogs_count }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sidebar-widget mb-5">
                    <h5 class="font-heading fw-bold mb-4 position-relative pb-2 border-bottom">Bài mới nhất</h5>
                    @foreach($recentPosts as $post)
                    <div class="d-flex mb-3 align-items-start recent-post-item">
                        <div class="flex-shrink-0" style="width: 70px; height: 70px;">
                            <a href="{{ route('user.blogs.show', $post->slug) }}">
                                @if($post->thumbnail)
                                    <img src="{{ asset('storage/' . $post->thumbnail) }}" class="w-100 h-100 rounded object-fit-cover" alt="thumb">
                                @else
                                    <div class="w-100 h-100 bg-light rounded d-flex align-items-center justify-content-center"><i class='bx bx-image text-muted'></i></div>
                                @endif
                            </a>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1" style="line-height: 1.4; font-size: 15px;">
                                <a href="{{ route('user.blogs.show', $post->slug) }}" class="text-decoration-none text-dark font-heading hover-underline">
                                    {{ Str::limit($post->title, 45) }}
                                </a>
                            </h6>
                            <small class="text-muted" style="font-size: 12px;"><i class='bx bx-time me-1'></i> {{ $post->created_at->format('d M, Y') }}</small>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* 1. Format nội dung bài viết (Quan trọng) */
    .blog-content {
        line-height: 1.8;
        font-size: 1.1rem;
        color: #333;
    }
    .blog-content p { margin-bottom: 1.5rem; }
    .blog-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 20px 0; }
    .blog-content h2, .blog-content h3 { font-weight: bold; margin-top: 2rem; margin-bottom: 1rem; color: #212529; }
    .blog-content ul, .blog-content ol { margin-bottom: 1.5rem; padding-left: 20px; }

    /* 2. Tooltip Login (Như đã hướng dẫn) */
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
        margin-left: -6px;
        border-width: 6px;
        border-style: solid;
        border-color: var(--color-accent) transparent transparent transparent;
    }
    .login-tooltip.show { display: block; opacity: 1; animation: tooltipFadeIn 0.3s forwards; }
    @keyframes tooltipFadeIn { from { opacity: 0; transform: translate(-50%, 10px); } to { opacity: 1; transform: translate(-50%, 0); } }

    /* 3. Button Active State */
    .active-like { background-color: #fee2e2 !important; border-color: #ef4444 !important; color: #ef4444 !important; }
    .hover-zoom { transition: transform 0.3s ease; }
    .hover-zoom:hover { transform: scale(1.05); }
    .hover-green:hover { color: #198754 !important; }
    .cat-link:hover { padding-left: 5px; color: #198754 !important; }
    
    /* Font Heading */
    .font-heading { font-family: 'Georgia', serif; }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý nút Like với Tooltip
        const likeBtn = document.querySelector('.like-btn');
        if(likeBtn) {
            likeBtn.addEventListener('click', function() {
                let btn = this;
                let blogId = btn.getAttribute('data-id');
                let icon = btn.querySelector('.icon-heart');
                let countSpan = btn.querySelector('.like-count');
                let tooltip = btn.parentElement.querySelector('.login-tooltip');

                fetch("{{ url('/blog/like') }}/" + blogId, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    // Nếu chưa đăng nhập (Lỗi 401)
                    if (response.status === 401) {
                        if(tooltip) {
                            tooltip.classList.add('show');
                            setTimeout(() => { tooltip.classList.remove('show'); }, 3000);
                        }
                        return null;
                    }
                    return response.json();
                })
                .then(data => {
                    if (data && data.status === 'success') {
                        countSpan.innerText = data.count;
                        if (data.liked) {
                            btn.classList.add('active-like');
                            btn.classList.remove('btn-outline-danger');
                            btn.classList.add('btn-danger', 'text-white'); // Đổi màu nền đỏ đặc
                            icon.classList.remove('bx-heart');
                            icon.classList.add('bxs-heart');
                        } else {
                            btn.classList.remove('active-like');
                            btn.classList.add('btn-outline-danger');
                            btn.classList.remove('btn-danger', 'text-white');
                            icon.classList.remove('bxs-heart');
                            icon.classList.add('bx-heart');
                        }
                    }
                })
                .catch(error => console.error('Lỗi:', error));
            });
        }
    });
</script>
@endsection