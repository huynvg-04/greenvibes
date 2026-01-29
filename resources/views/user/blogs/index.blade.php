@extends('layouts.app')
@section('title', 'Blog - ' . config('app.name'))

@section('content')
<div class="blog-container">
     <div class="blog-header">
          <h1 class="blog-title">Bài viết</h1>
     </div>

     <div class="content-wide">
          <div class="row">
               <div class="col-lg-8 pe-lg-5">
                    @forelse($blogs as $blog)
                    <div class="blog-card mb-5 pb-5 border-bottom">
                         <div class="blog-thumb mb-4 position-relative overflow-hidden rounded shadow-sm">
                              <a href="{{ route('user.blogs.show', $blog->slug) }}">
                                   @if($blog->thumbnail)
                                   <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="{{ $blog->title }}" class="img-fluid w-100 hover-zoom">
                                   @else
                                   <div class="bg-light d-flex align-items-center justify-content-center" style="height: 400px;">
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                   </div>
                                   @endif
                              </a>
                         </div>

                         <h2 class="blog-title mb-3">
                              <a href="{{ route('user.blogs.show', $blog->slug) }}" class="text-decoration-none fw-bold text-dark font-heading">
                                   {{ $blog->title }}
                              </a>
                         </h2>

                         <div class="blog-meta mb-2 small text-muted fw-bold">
                              <span class="me-3">
                                   <i class='bx bx-user me-1'></i> {{ $blog->user->name ?? 'Admin' }}
                              </span>
                              <span class="me-3">
                                   <i class='bx bx-calendar me-1'></i> {{ $blog->created_at->format('d-M-Y') }}
                              </span>

                              @if($blog->category)
                              <span class="me-3">
                                   <i class='bx bx-folder me-1'></i> {{ $blog->category->name }}
                              </span>
                              @endif
                              <span>
                                   <i class='bx bx-show me-1'></i> {{ $blog->views }} Lượt xem
                              </span>
                         </div>
                         <p class="text-muted mb-4" style="line-height: 1.8;">
                              {{ Str::limit($blog->excerpt ?? strip_tags($blog->content), 220) }}
                         </p>

                         <div class="d-flex justify-content-between align-items-center">
                              <a href="{{ route('user.blogs.show', $blog->slug) }}" class="btn btn-outline-dark rounded-pill px-4 fw-bold btn-sm">
                                   ĐỌC TIẾP
                              </a>

                              <div class="position-relative d-inline-block">

                                   <div class="login-tooltip">
                                        <div class="tooltip-arrow"></div>
                                        Vui lòng <a href="{{ route('login') }}" class="text-white fw-bold text-decoration-underline">đăng nhập</a> để thích!
                                   </div>

                                   <button class="btn btn-sm btn-light border rounded-pill px-3 like-btn d-flex align-items-center {{ $blog->isLikedByAuthUser() ? 'active-like' : '' }}"
                                        data-id="{{ $blog->id }}">
                                        <i class='bx {{ $blog->isLikedByAuthUser() ? 'bxs-heart' : 'bx-heart' }} text-danger me-1 icon-heart'></i>
                                        <span class="like-count fw-bold">{{ $blog->likes_count }}</span>
                                   </button>
                              </div>
                         </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                         <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="Empty" style="width: 200px; opacity: 0.5;">
                         <h4 class="mt-3 text-muted">Chưa có bài viết nào</h4>
                    </div>
                    @endforelse

                    <div class="mt-4">
                         {{ $blogs->links() }}
                    </div>
               </div>

               <div class="col-lg-4 ps-lg-4 mt-5 mt-lg-0">

                    <div class="sidebar-widget mb-5">
                         <h5 class="font-heading fw-bold mb-4 position-relative pb-2 border-bottom">Tìm kiếm</h5>
                         <form action="{{ route('user.blogs.index') }}" method="GET">
                              <div class="input-group">
                                   <input type="text" name="keyword" class="form-control border-end-0 bg-light" placeholder="Nhập từ khóa..." value="{{ request('keyword') }}">
                                   <button class="btn btn-light border border-start-0" type="submit"><i class="fas fa-search text-muted"></i></button>
                              </div>
                         </form>
                    </div>

                    <div class="sidebar-widget mb-5">
                         <h5 class="font-heading fw-bold mb-4 position-relative pb-2 border-bottom">Danh mục</h5>
                         <ul class="list-unstyled">
                              @foreach($categories as $cat)
                              <li class="mb-2">
                                   <a href="{{ route('user.blogs.index', ['category' => $cat->slug]) }}"
                                        class="d-flex justify-content-between text-decoration-none text-secondary py-2 border-bottom-dashed cat-link {{ request('category') == $cat->slug ? 'active' : '' }}">
                                        <span><i class="fas fa-angle-right me-2 text-muted"></i> {{ $cat->name }}</span>
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
                                        <div class="w-100 h-100 bg-light rounded d-flex align-items-center justify-content-center"><i class="fas fa-image text-muted"></i></div>
                                        @endif
                                   </a>
                              </div>
                              <div class="flex-grow-1 ms-3">
                                   <h6 class="mb-1" style="line-height: 1.4; font-size: 15px;">
                                        <a href="{{ route('user.blogs.show', $post->slug) }}" class="text-decoration-none text-dark font-heading hover-underline">
                                             {{ Str::limit($post->title, 45) }}
                                        </a>
                                   </h6>
                                   <small class="text-muted" style="font-size: 12px;"><i class="far fa-clock me-1"></i> {{ $post->created_at->format('d M, Y') }}</small>
                              </div>
                         </div>
                         @endforeach
                    </div>

                    <div class="sidebar-widget">
                         <h5 class="font-heading fw-bold mb-4 position-relative pb-2 border-bottom">Instagram</h5>
                         <div class="row g-2">
                              @for($i=0; $i<6; $i++)
                                   <div class="col-4">
                                   <div class="ratio ratio-1x1 bg-light rounded overflow-hidden position-relative insta-item">
                                        <i class="fab fa-instagram position-absolute top-50 start-50 translate-middle text-muted opacity-25 fa-2x"></i>
                                   </div>
                         </div>
                         @endfor
                    </div>
               </div>
          </div>
     </div>
</div>
</div>
@endsection


@section('scripts')
<style>
     .like-btn:focus {
          cursor: pointer;
          outline: none !important;
          box-shadow: none !important;
     }

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
          margin-left: -5px;
          border-width: 5px;
          border-style: solid;
          border-color: var(--color-accent) transparent transparent transparent;
     }

     .login-tooltip.show {
          display: block;
          opacity: 1;
          animation: fadeIn 0.3s forwards;
     }

     @keyframes fadeIn {
          from {
               opacity: 0;
               transform: translate(-50%, 10px);
          }

          to {
               opacity: 1;
               transform: translate(-50%, 0);
          }
     }

     .content-wide {
          max-width: calc(100% - 300px);
          margin: 0 auto;
     }

     .blog-container {
          margin: 0 auto;
     }

     .blog-header {
          height: 140px;
          background: #f4f4f4;
          margin-top: 50px;
          padding: var(--space-xl) 0 var(--space-xl) 60px;
          font-family: var(--font-body);
          display: flex;
          align-items: center;
     }

     .blog-title {
          font-family: var(--font-ui);
          font-size: var(--type-h2);
          font-weight: 300;
          color: var(--color-primary);
          margin: 0;
          letter-spacing: 0.5px;
     }

     .font-heading {
          font-family: var(--font-ui);
     }

     .hover-zoom {
          transition: transform 0.5s ease;
     }

     .blog-thumb:hover .hover-zoom {
          transform: scale(1.05);
     }

     /* Category Links */
     .cat-link {
          transition: all 0.2s;
     }

     .cat-link:hover,
     .cat-link.active {
          color: #198754 !important;
          padding-left: 5px;
     }

     .border-bottom-dashed {
          border-bottom: 1px dashed #eee;
     }

     /* Buttons */
     .active-like {
          background-color: transparent !important;
          border-color: var(--color-accent) !important;
     }

     .active-like i {
          color: var(--color-accent) !important;
     }

     /* Pagination styling (Bootstrap default is okay, but this makes it cleaner) */
     .pagination .page-item.active .page-link {
          background-color: #212529;
          border-color: #212529;
     }

     .pagination .page-link {
          color: #212529;
     }
</style>
<script>
     document.querySelectorAll('.like-btn').forEach(button => {
          button.addEventListener('click', function() {
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
                         if (response.status === 401) {
                              if (tooltip) {
                                   tooltip.classList.add('show');

                                   setTimeout(() => {
                                        tooltip.classList.remove('show');
                                   }, 3000);
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
                                   icon.classList.remove('bx-heart');
                                   icon.classList.add('bxs-heart');
                              } else {
                                   btn.classList.remove('active-like');
                                   icon.classList.remove('bxs-heart');
                                   icon.classList.add('bx-heart');
                              }
                         }
                    })
                    .catch(error => console.error('Lỗi:', error));
          });
     });
</script>
@endsection