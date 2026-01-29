<nav class="navbar navbar-expand-lg fixed-top modern-navbar navbar-light bg-light p-3" role="navigation" aria-label="Main navigation">
     <div class="container px-0">
          <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse"
               data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
               aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <form class="modern-search" id="search-form" action="{{ route('products.search') }}" method="GET" autocomplete="off" role="search">
                    <div class="d-flex position-relative gap-2  ">

                         <div class="position-relative flex-grow-1">
                              <label for="search-input" class="visually-hidden">Tìm kiếm sản phẩm</label>

                              <input class="search-input"
                                   type="text"
                                   name="query"
                                   id="search-input"
                                   placeholder="Bạn muốn mua gì hôm nay?"
                                   aria-label="Tìm kiếm sản phẩm"
                                   style="padding-right: 40px;"> <span id="btn-clear-search"
                                   class="position-absolute top-50 translate-middle-y text-secondary"
                                   style="right: 10px; cursor: pointer; display: none; z-index: 5;">
                                   <i class="bx bx-x" style="right: 40px;"></i>
                              </span>
                         </div>

                         <button class="search-btn" type="submit" aria-label="Tìm kiếm">
                              <i class="fas fa-search" aria-hidden="true"></i>
                         </button>
                    </div>

                    <div id="suggestions" class="list-group position-absolute w-80" style="z-index:1000; top: 100%; left: 0;" role="listbox" aria-label="Gợi ý tìm kiếm"></div>
               </form>
               <a class="navbar-brand position-absolute start-50 translate-middle-x" href="{{ url('/') }}" aria-label="Green Vibes - Trang chủ" id="GreenVibesLogo">
                    GREEN <strong>VIBES</strong>
               </a>
               <ul class="navbar-nav ms-auto align-items-center">
                    @guest
                    <li class="login-register d-flex align-items-center px-3">
                         <i class="far fa-user me-2" aria-hidden="true"></i>
                         <a class="button-login" href="{{ route('login') }}">Đăng nhập</a>
                         <span class="mx-1">/</span>
                         <a class="button-register" href="{{ route('register') }}">Đăng ký</a>
                    </li>
                    @else
                    @php
                    $user = Auth::user();
                    @endphp

                    @if($user && $user->hasAnyRole(['manager','staff']))
                    <li class="nav-item me-3">
                         <a class="nav-link" href="{{ url('/admin/dashboard') }}" data-tooltip="Trang quản trị">
                              <i class="fas fa-tools me-1" aria-hidden="true"></i>Quản trị
                         </a>
                    </li>
                    @endif
                    <li class="nav-item dropdown me-3">
                         <a id="navbarDropdown"
                              class="nav-link d-flex align-items-center gap-1"
                              href="#"
                              role="button"
                              data-bs-toggle="dropdown"
                              aria-expanded="false">

                              <i class='bx bx-user-circle fs-5'></i>
                              <span>{{ Auth::user()->name }}</span>
                              <i class='bx bx-chevron-down arrow'></i>
                         </a>


                         <div class="dropdown-menu mt-2" aria-labelledby="navbarDropdown">
                              @auth
                              @if (Auth::user()->hasRole('customer'))
                              <a class="dropdown-item" href="{{ route('user.orders.index') }}">
                                   <i class="bx bx-receipt me-2"></i> Đơn hàng
                              </a>

                              <a class="dropdown-item" href="{{ route('user.profile.edit') }}">
                                   <i class="bx bx-id-card me-2"></i> Chỉnh sửa hồ sơ
                              </a>
                              @endif
                              @endauth

                              <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                   <i class="bx bx-log-out me-2"></i> Đăng xuất
                              </a>

                              <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                   @csrf
                              </form>
                         </div>
                    </li>

                    @if ($user && $user->hasRole('customer'))
                    @php
                    $cartCount = App\Models\Cart::where('user_id', Auth::id())->count();
                    @endphp
                    <li class="nav-item me-3">
                         <a class="cart-link" href="{{ route('user.cart.index') }}" data-tooltip="Giỏ hàng" aria-label="Giỏ hàng">
                              <i class='bx bx-basket fs-5' aria-hidden="true"></i>
                              @if($cartCount > 0)
                              <span class="cart-badge" aria-label="{{ $cartCount }} sản phẩm trong giỏ">{{ $cartCount }}</span>
                              @endif
                         </a>
                    </li>
                    @endif

                    @if ($user && $user->hasRole('customer'))
                    @php
                    $productCount = App\Models\Wishlist::where('user_id', Auth::id())->count();
                    @endphp
                    <li class="nav-item me-3">
                         <a class="cart-link" href="{{ route('user.wishlists.index') }}" data-tooltip="Yêu thích">
                              <i class="far fa-heart fs-5" aria-hidden="true"></i>
                              @if($productCount > 0)
                              <span class="cart-badge" aria-label="{{ $productCount }} sản phẩm yêu thích">{{ $productCount }}</span>
                              @endif
                         </a>
                    </li>
                    @endif
                    @endguest
                    @auth
                    <li class="nav-item dropdown me-3">
                         <a class="cart-link position-relative d-flex align-items-center" href="#" data-tooltip="Thông báo" aria-label="Thông báo" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                              <i class='bx bx-bell fs-5'></i>

                              @if(Auth::user()->unreadNotifications->count() > 0)
                              <span class="notificate-padge">
                                   {{ Auth::user()->unreadNotifications->count() > 99 ? '99+' : Auth::user()->unreadNotifications->count() }}
                              </span>
                              @endif
                         </a>

                         <ul class="dropdown-menu dropdown-menu-end shadow border-0 py-0" aria-labelledby="notificationDropdown" style="width: 350px;">
                              <li class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom bg-white rounded-top">
                                   <h6 class="mb-0 fw-bold text-gray-800">Thông báo</h6>
                                   @if(Auth::user()->unreadNotifications->count() > 0)
                                   <a href="{{ route('notifications.readAll') }}" class="color-accent">Đánh dấu đã đọc</a>
                                   @endif
                              </li>

                              <div class="notification-list" style="max-height: 400px; overflow-y: auto;">
                                   @forelse(Auth::user()->notifications->take(10) as $notification)
                                   <li>
                                        <a href="{{ route('notifications.read', $notification->id) }}"
                                             class="dropdown-item p-3 border-bottom d-flex align-items-start {{ $notification->read_at ? 'bg-white' : 'bg-light' }}">

                                             <div class="flex-shrink-0 me-3">
                                                  <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                       style="width: 40px; height: 40px; 
                             background-color: {{ $notification->data['bg_color'] ?? '#e9ecef' }}; 
                             color: {{ $notification->data['color'] ?? '#495057' }}">
                                                       <i class='{{ $notification->data['icon'] ?? 'bx bx-bell' }} fs-5'></i>
                                                  </div>
                                             </div>

                                             <div class="flex-grow-1">
                                                  <div class="d-flex justify-content-between align-items-center mb-1">
                                                       <strong class="small {{ $notification->read_at ? 'text-secondary' : 'text-dark' }}">
                                                            {{ $notification->data['title'] ?? 'Thông báo hệ thống' }}
                                                       </strong>
                                                       <small class="text-muted" style="font-size: 0.75rem;">
                                                            {{ $notification->created_at->diffForHumans() }}
                                                       </small>
                                                  </div>
                                                  <p class="mb-0 small text-muted text-wrap" style="line-height: 1.4;">
                                                       {{ $notification->data['message'] ?? '' }}
                                                  </p>
                                             </div>

                                             @if(!$notification->read_at)
                                             <span class="position-absolute top-50 end-0 translate-middle-y me-2 p-1 bg-accent rounded-circle" style="width: 8px; height: 8px;"></span>
                                             @endif
                                        </a>
                                   </li>
                                   @empty
                                   <li class="text-center p-5">
                                        <i class='bx bx-bell-off fs-1 text-muted opacity-25 mb-3'></i>
                                        <p class="text-muted small mb-0">Bạn không có thông báo nào.</p>
                                   </li>
                                   @endforelse
                              </div>

                              <li class="text-center p-2 bg-light rounded-bottom border-top">
                                   <a href="#" class="text-decoration-none small text-secondary fw-bold">Xem tất cả thông báo</a>
                              </li>
                         </ul>
                    </li>
                    @endauth
               </ul>
          </div>
     </div>
</nav>

<div class="secondary-navbar d-none d-lg-block">
     <div class="container">
          <ul class="nav justify-content-center">
               <li class="nav-item">
                    <a class="nav-link sec-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">Trang chủ</a>
               </li>
               <li class="nav-item">
                    <a class="nav-link sec-link {{ request()->routeIs('products.*') ? 'active' : '' }}"
                         href="{{ route('products.index') }}">
                         Cây cảnh
                    </a>
               </li>
               <li class="nav-item">
                    <a class="nav-link sec-link" href="#">Danh mục</a>
                    <i class='bx bx-chevron-down arrow'></i>
               </li>
               <li class="nav-item">
                    <a class="nav-link sec-link" href="#">Bộ sưu tập</a>
                    <i class='bx bx-chevron-down arrow'></i>
               </li>
               <li class="nav-item">
                    <a class="nav-link sec-link {{ request()->routeIs('user.blogs.*') ? 'active' : '' }}"
                         href="{{ route('user.blogs.index') }}">
                         Blog
                    </a>
               </li>
          </ul>
     </div>
</div>