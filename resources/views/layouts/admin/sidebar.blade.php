<nav class="sidebar" id="sidebar">
    <div class="sidebar-content">
        <div class="nav-group {{ request()->is('admin/dashboard') || request()->is('admin/revenue*') ? 'active' : '' }}">
            <div class="menu-header shadow-sm" onclick="toggleMenu(this)">
                <span class="header-text">Tổng quan</span>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <div class="menu-items">
                <a href="{{ url('/admin/dashboard') }}" class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class='bx bxs-dashboard'></i> Dashboard
                </a>
                @can('viewAny', App\Models\Revenue::class)
                <a href="{{ route('admin.revenue.index') }}" class="nav-link {{ request()->is('admin/revenue*') ? 'active' : '' }}">
                    <i class='bx bxs-bar-chart-alt-2'></i> Thống kê
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-bar-chart-alt-2'></i> Thống kê
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
            </div>
        </div>

        <div class="nav-group {{ request()->is('admin/products*') || request()->is('admin/categories*') || request()->is('admin/attributes*') || request()->is('admin/product_variants*') || request()->is('admin/product_images*') || request()->is('admin/warehouse*') ? 'active' : '' }}">
            <div class="menu-header shadow-sm" onclick="toggleMenu(this)">
                <span class="header-text">Sản phẩm & Kho</span>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <div class="menu-items">
                @can('viewAny', App\Models\Product::class)
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->is('admin/products*') ? 'active' : '' }}">
                    <i class='bx bxs-plant-pot'></i> Sản phẩm
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-plant-pot'></i> Sản phẩm
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\Category::class)
                <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->is('admin/categories*') ? 'active' : '' }}">
                    <i class='bx bxs-category'></i> Danh mục
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-category'></i> Danh mục
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\Attribute::class)
                <a href="{{ route('admin.attributes.index') }}" class="nav-link {{ request()->is('admin/attributes*') ? 'active' : '' }}">
                    <i class='bx bxs-slider-alt'></i> Thuộc tính
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-slider'></i> Thuộc tính
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\ProductVariant::class)
                <a href="{{ route('admin.product_variants.index') }}" class="nav-link {{ request()->is('admin/product_variants*') ? 'active' : '' }}">
                    <i class='bx bxs-layer'></i> Phân loại
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-layer'></i> Phân loại
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\ProductImage::class)
                <a href="{{ route('admin.product_images.index') }}" class="nav-link {{ request()->is('admin/product_images*') ? 'active' : '' }}">
                    <i class='bx bxs-images'></i> Hình ảnh
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-images'></i> Hình ảnh
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\Warehouse::class)
                <a href="{{ route('admin.warehouse.index') }}" class="nav-link {{ request()->is('admin/warehouse*') ? 'active' : '' }}">
                    <i class='bx bxs-box'></i> Kho hàng
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-box'></i> Kho hàng
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
            </div>
        </div>

        <div class="nav-group {{ request()->is('admin/orders*') || request()->is('admin/returns*') || request()->is('admin/customers*') || request()->is('admin/coupons*') || request()->is('admin/reviews*') || request()->is('admin/membership-tiers*') ? 'active' : '' }}">
            <div class="menu-header shadow-sm" onclick="toggleMenu(this)">
                <span class="header-text">Bán hàng</span>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <div class="menu-items">
                @can('viewAny', App\Models\Order::class)
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->is('admin/orders*') ? 'active' : '' }}">
                    <i class='bx bxs-cart'></i> Đơn hàng
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-cart'></i> Đơn hàng
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\OrderReturn::class)
                <a href="{{ route('admin.returns.index') }}" class="nav-link {{ request()->is('admin/returns*') ? 'active' : '' }}">
                    <i class='bx bxs-undo'></i> Hoàn hàng
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-undo'></i> Hoàn hàng
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\CustomerProfile::class)
                <a href="{{ route('admin.customers.index') }}" class="nav-link {{ request()->is('admin/customers*') ? 'active' : '' }}">
                    <i class='bx bxs-user'></i> Khách hàng
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-user'></i> Khách hàng
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\MembershipTier::class)
                <a href="{{ route('admin.membership-tiers.index') }}" class="nav-link {{ request()->is('admin/membership-tiers*') ? 'active' : '' }}">
                    <i class='bx bxs-crown'></i> Hạng
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-crown'></i> Hạng
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\Coupon::class)
                <a href="{{ route('admin.coupons.index') }}" class="nav-link {{ request()->is('admin/coupons*') ? 'active' : '' }}">
                    <i class='bx bxs-purchase-tag-alt'></i> Mã giảm giá
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-purchase-tag-alt'></i> Mã giảm giá
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\Review::class)
                <a href="{{ route('admin.reviews.index') }}" class="nav-link {{ request()->is('admin/reviews*') ? 'active' : '' }}">
                    <i class='bx bxs-star'></i> Đánh giá
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-star'></i> Đánh giá
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
            </div>
        </div>

        <div class="nav-group {{ request()->is('admin/staffs*') ? 'active' : '' }}">
            <div class="menu-header shadow-sm" onclick="toggleMenu(this)">
                <span class="header-text">Nhân sự</span>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <div class="menu-items">
                @can('viewAny', App\Models\StaffProfile::class)
                <a href="{{ route('admin.staffs.index') }}" class="nav-link {{ request()->is('admin/staffs*') ? 'active' : '' }}">
                    <i class='bx bxs-user-pin'></i> Nhân viên
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-user-pin'></i> Nhân viên
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
            </div>
        </div>

        <div class="nav-group {{ request()->is('admin/banners*') || request()->is('admin/blogs*') ? 'active' : '' }}">
            <div class="menu-header shadow-sm" onclick="toggleMenu(this)">
                <span class="header-text">Marketing</span>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <div class="menu-items">
                @can('viewAny', App\Models\Banner::class)
                <a href="{{ route('admin.banners.index') }}" class="nav-link {{ request()->is('admin/banners*') ? 'active' : '' }}">
                    <i class='bx bxs-image'></i> Banners
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-image'></i> Banners
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\Blog::class)
                <a href="{{ route('admin.blogs.index') }}" class="nav-link {{ request()->is('admin/blogs*') ? 'active' : '' }}">
                    <i class='bx bxs-news'></i> Bài viết
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-news'></i> Bài viết
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
            </div>
        </div>

        <div class="nav-group {{ request()->is('admin/settings*') ? 'active' : '' }}">
            <div class="menu-header shadow-sm" onclick="toggleMenu(this)">
                <span class="header-text">Tùy chọn</span>
                <i class='bx bx-chevron-down arrow'></i>
            </div>
            <div class="menu-items">
                @can('viewAny', App\Models\PaymentMethod::class)
                <a href="{{ route('admin.settings.payment.index') }}" class="nav-link {{ request()->is('admin/settings/payment*') ? 'active' : '' }}">
                    <i class='bx bxs-credit-card'></i> Thanh toán
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-credit-card'></i> Thanh toán
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
                @can('viewAny', App\Models\ShippingRate::class)
                <a href="{{ route('admin.settings.shipping.index') }}" class="nav-link {{ request()->is('admin/settings/shipping*') ? 'active' : '' }}">
                    <i class='bx bxs-truck'></i> Vận chuyển
                </a>
                @else
                <div class="nav-link menu-item-disabled">
                    <i class='bx bxs-truck'></i> Vận chuyển
                    <i class="bx bxs-lock-alt menu-lock"></i>
                </div>
                @endcan
            </div>
        </div>

    </div>
</nav>


