<nav class="top-navbar">
     <a href="{{ url('/admin/dashboard') }}" class="brand-logo">
          <div class="brand-icon">
               <i class='bx bxs-plant-pot'></i>
          </div>
          <div class="brand-text">
               <h4>Trang QL Green Vibes</h4>
               <span>Hôm nay: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</span>
          </div>
     </a>

     <div class="d-flex align-items-center flex-grow-1 ps-3">
          <button class="mobile-toggle me-3" onclick="toggleSidebar()">
               <i class='bx bx-menu'></i>
          </button>
          <div class="search-bar">
               <i class='bx bx-search'></i>
               <input type="text" placeholder="Tìm kiếm...">
          </div>
     </div>

     <div class="nav-right">
          <div class="dropdown">
               <button class="nav-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Vị trí menu">
                    <i class='bx bx-layout'></i>
               </button>
               <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu">
                    <li><span class="dropdown-header">Vị trí menu</span></li>
                    <li><a class="custom-dropdown-item" onclick="setLayout('left')"><i class='bx bx-sidebar'></i>
                              Trái</a></li>
                    <li><a class="custom-dropdown-item" onclick="setLayout('right')"><i class='bx bx-sidebar'
                                   style="transform: scaleX(-1);"></i> Phải</a></li>
                    <li><a class="custom-dropdown-item" onclick="setLayout('top')"><i class='bx bx-dock-top'></i>
                              Trên</a></li>
                    <li><a class="custom-dropdown-item" onclick="setLayout('bottom')"><i class='bx bx-dock-bottom'></i>
                              Dưới</a></li>
               </ul>
          </div>

          <div class="dropdown">
               <button class="nav-icon-btn" data-bs-toggle="dropdown" aria-expanded="false" title="Giao diện">
                    <i class='bx bx-sun' id="theme-icon-active"></i>
               </button>
               <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu">
                    <li><span class="dropdown-header">Giao diện</span></li>
                    <li><a class="custom-dropdown-item" onclick="setTheme('light')"><i class='bx bx-sun'></i> Sáng</a>
                    </li>
                    <li><a class="custom-dropdown-item" onclick="setTheme('dark')"><i class='bx bx-moon'></i> Tối</a>
                    </li>
                    <li><a class="custom-dropdown-item" onclick="setTheme('system')"><i class='bx bx-desktop'></i> Hệ
                              thống</a></li>
               </ul>
               <script>
                    (function () {
                         const storedTheme = localStorage.getItem('theme') || 'system';
                         const icon = document.getElementById('theme-icon-active');

                         const shouldShowMoon = () => {
                              if (storedTheme === 'dark') return true;
                              if (storedTheme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches) return true;
                              return false;
                         };

                         if (shouldShowMoon()) {
                              icon.classList.remove('bx-sun');
                              icon.classList.add('bx-moon');
                         }
                    })();
               </script>
          </div>

          <div class="nav-item dropdown">
               <button class="nav-icon-btn" data-bs-toggle="dropdown">
                    <i class='bx bx-bell fs-4'></i>
                    @if(Auth::user()->unreadNotifications->count() > 0)
                         <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger small">
                              {{ Auth::user()->unreadNotifications->count() }}
                         </span>
                    @endif
               </button>

               <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-0 bg-card pb-0"
                    style="width: 320px; max-height: 400px; overflow-y: auto;">
                    <li class="dropdown-header border-bottom bg-card sticky-top z-1">Thông báo</li>

                    @forelse(Auth::user()->notifications as $notification)
                         <li>
                              <a href="{{ route('notifications.read', $notification->id) }}"
                                   class="dropdown-item d-flex align-items-start p-3 border-bottom m-0 {{ $notification->read_at ? '' : 'bg-card' }}">

                                   <div class="me-3 text-{{ $notification->data['color'] ?? 'primary' }}">
                                        <i class="{{ $notification->data['icon'] ?? 'bx bx-info-circle' }} fs-4"></i>
                                   </div>

                                   <div class="w-100">
                                        <span class="d-block text-body text-wrap mb-1"
                                             style="line-height: 1.2;">{{ $notification->data['message'] }}</span>
                                        <small class="text-muted fw-lighter" style="font-size: 11px;">
                                             {{ $notification->created_at->diffForHumans() }}
                                        </small>
                                   </div>
                              </a>
                         </li>
                    @empty
                         <li class="text-center p-4 text-muted">
                              <i class='bx bx-bell-off fs-1 mb-2 d-block opacity-50'></i>
                              Không có thông báo mới
                         </li>
                    @endforelse

                    @if(Auth::user()->notifications->count() > 0)
                         <li class="position-sticky bottom-0 start-0 w-100 p-2 text-center border-top z-1 shadow-sm bg-card">
                              <a class="text-decoration-none fw-bold small text-body"
                                   href="{{ route('notifications.readAll') }}">
                                   Đánh dấu tất cả đã đọc
                              </a>
                         </li>
                    @endif
               </ul>
          </div>

          <div class="dropdown">
               <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-info d-none d-md-block ms-2 py-1">
                         <span style="font-weight: 600; font-size: 14px;">{{ Auth::user()->name ?? 'Admin' }}</span>
                    </div>
               </div>
               <ul class="dropdown-menu dropdown-menu-end custom-dropdown-menu mt-2">
                    <li><span class="dropdown-header">Tài khoản</span></li>
                    <li>
                         @can('view', Auth::user())
                              <a class="custom-dropdown-item text-body " href="{{ route('admin.staffs.show', Auth::id()) }}">
                                   <i class='bx bx-user'></i> Hồ sơ
                              </a>
                         @endcan
                    </li>
                    <li>
                         <a class="custom-dropdown-item text-danger" href="{{ route('logout') }}"
                              onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                              <i class='bx bx-log-out'></i> Đăng xuất
                         </a>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
                    </li>
               </ul>
          </div>
     </div>
</nav>