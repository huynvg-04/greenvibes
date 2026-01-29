@extends('layouts.admin')

@section('title', 'Danh sách sản phẩm')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Quản lý sản phẩm</h3>
        </div>
        @can('create', App\Models\Product::class)
        <button type="button"
            class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#createProductModal">
            <i class='bx bx-plus fs-5'></i>
            <span class="fw-semibold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <form method="GET" action="{{ route('admin.products.index') }}" class="d-flex flex-wrap align-items-center justify-content-between gap-3 px-3 py-3">

            <div class="col-12 col-md-5">
                <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                    <span class="input-group-text bg-transparent border-0 pe-2 text-muted"><i class='bx bx-search'></i></span>
                    <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                        placeholder="Tìm kiếm theo tên, SKU.."
                        value="{{ request('keyword') }}">
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">
                @if(request()->anyFilled(['keyword', 'sort', 'status']))
                <a href="{{ route('admin.products.index') }}"
                    class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0 order-last order-xl-first ms-auto ms-xl-0"
                    data-bs-toggle="tooltip" title="Xóa bộ lọc"
                    style="width: 36px; height: 36px;">
                    <i class='bx bx-refresh fs-5'></i>
                </a>
                @endif

                <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto" style="max-width: 100%; white-space: nowrap;">

                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Tất cả
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => '1', 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == '1' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Hiển thị
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => '0', 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == '0' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Đang ẩn
                    </a>
                </div>

                <div class="vr d-none d-lg-block mx-1 text-muted opacity-25" style="height: 40px;"></div>

                <div class="d-flex align-items-center gap-2">
                    <select name="sort" class="form-select form-select-sm rounded-pill bg-card form-control border-0 shadow-none cursor-pointer text-body fw-medium py-2 px-3"
                        style="min-width: 140px;" onchange="this.form.submit()">
                        <option value="created_desc" {{ request('sort') == 'created_desc' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Tên Z-A</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                        <option value="discount_desc" {{ request('sort') == 'discount_desc' ? 'selected' : '' }}>Giảm giá nhiều nhất</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card-body p-0">
    <div class="table-responsive rounded-4 bg-card shadow-sm mb-4" style="min-height: 500px;">
        <table class="table modern-table align-middle table-hover mb-0 rounded-4 custom-table">
            <thead class="bg-light">
                <tr>
                    <th class="align-middle ps-4 py-3 text-body text-uppercase small fw-bold" style="width: 60px;">ID</th>
                    <th class="align-middle py-3 text-center text-body text-uppercase small fw-bold" style="width: 40px;">Ảnh</th>
                    <th class="align-middle py-3 text-body text-uppercase small fw-bold" style="min-width: 150px;">Thông tin sản phẩm</th>
                    <th class="align-middle py-3 text-body text-uppercase small fw-bold">Danh mục</th>
                    <th class="align-middle py-3 text-body text-uppercase small fw-bold" style="min-width: 120px;">Giá bán</th>
                    <th class="align-middle py-3 text-body text-uppercase small fw-bold" style="width: 65px;">Tồn kho</th>
                    <th class="align-middle py-3 text-body text-uppercase small fw-bold">Giảm giá</th>
                    <th class="align-middle py-3 text-body text-uppercase small fw-bold">Trạng thái</th>
                    <th class="text-center pe-4 align-middle text-body text-uppercase small fw-bold" style="width: 110px;">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)
                <tr class="transition-all hover-bg-light">
                    <td class="ps-4 text-muted font-monospace fw-normal">#{{ $product->id }}</td>

                    <td class="text-center">
                        @php
                        $levels = [
                        ['min' => 5, 'class' => 'bg-light-success text-success', 'color' => 'success'],
                        ['min' => 2, 'class' => 'bg-light-warning text-warning', 'color' => 'warning'],
                        ['min' => 0, 'class' => 'bg-light-danger text-danger', 'color' => 'danger'],
                        ];

                        foreach ($levels as $level) {
                        if ($product->images_count >= $level['min']) {
                        $badge = $level['class'];
                        $cl = $level['color'];
                        break;
                        }
                        }
                        @endphp

                        <span class="badge {{ $badge }} fw-normal rounded-pill px-3 py-2 font-monospace border border-{{ $cl}} border-opacity-10">
                            {{ $product->images_count }}
                        </span>
                    </td>


                    <td>
                        <div class="d-flex flex-column">
                            <span class="fw-semibold text-body small hover-text-primary transition-all">{{ Str::limit($product->name, 50) }}</span>
                            <small class="text-muted font-monospace mt-1">SKU: {{ $product->sku ?? '---' }}</small>
                        </div>
                    </td>

                    <td>
                        <span class="badge rounded-pill fw-normal px-3 py-2 bg-light-info text-info border border-info border-opacity-10 font-monospace">
                            {{ $product->category->name ?? 'Chưa phân loại' }}
                        </span>
                    </td>

                    <td>
                        @php
                        $minPrice = $product->variants->min('list_price') ?? $product->price;
                        $maxPrice = $product->variants->max('list_price') ?? $product->price;
                        @endphp
                        <div class="fw-bold text-success small">
                            @if($minPrice == $maxPrice)
                            {{ number_format($minPrice, 0, ',', '.') }}₫
                            @else
                            {{ number_format($minPrice, 0, ',', '.') }} - {{ number_format($maxPrice, 0, ',', '.') }}₫
                            @endif
                        </div>
                    </td>

                    <td>
                        @php
                        $stock = $product->variants->sum('stock') ?: $product->quantity;

                        if ($stock > 10) {
                        $badge = [ 'class' => 'bg-light-success text-success', 'label' => $stock, 'color' => 'success' ];
                        } elseif ($stock > 0) {
                        $badge = [
                        'class' => 'bg-light-warning text-warning',
                        'label' => $stock . ' (Sắp hết)', 'color' => 'warning'];
                        } else {
                        $badge = [
                        'class' => 'bg-light-danger text-danger',
                        'label' => 'Hết hàng', 'color' => 'danger'];
                        }
                        @endphp

                        <span class="badge {{ $badge['class'] }} fw-normal rounded-pill px-3 py-2 font-monospace border border-{{ $badge['color'] }} border-opacity-10">
                            {{ $badge['label'] }}
                        </span>
                    </td>

                    <td>
                        @php
                        $discountPercent = $product->discount_percent ?? 0;
                        @endphp
                        @if($discountPercent > 0)
                        <span class="badge bg-light-danger rounded-pill fw-normal text-danger px-3 py-2 border border-danger border-opacity-10 font-monospace">-{{ $discountPercent }}%</span>
                        @else
                        <span class="badge bg-light-secondary rounded-pill fw-normal text-secondary border border-secondary border-opacity-10 px-3 py-2 font-monospace">Không</span>
                        @endif
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            @if($product->status)
                            <span class="badge bg-light-success text-success fw-normal rounded-pill px-3 py-2 font-monospace border border-success border-opacity-10">Hiển thị</span>
                            @else
                            <span class="badge bg-light-secondary rounded-pill fw-normal text-secondary px-3 py-2 border border-secondary border-opacity-10 font-monospace">Đang ẩn</span>
                            @endif
                        </div>
                    </td>

                    <td class="text-center pe-4">
                        <div class="dropdown">
                            <button class="btn btn-icon btn-light border-0 text-muted" type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                data-bs-boundary="viewport">
                                <i class='bx bx-dots-vertical-rounded fs-4'></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 p-2 bg-card" style="border-radius: 12px; min-width: 200px; z-index: 1050;">
                                @can('view', $product)
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body" href="{{ route('admin.products.show', $product) }}">
                                        <i class='bx bx-show fs-5 me-3 text-muted'></i> Xem chi tiết
                                    </a>
                                </li>
                                @endcan
                                @can('update', $product)
                                <li>
                                    <a href="#"
                                        class="dropdown-item w-90 rounded-3 py-2 d-flex align-items-center text-body btn-edit-product"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editProductModal"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}"
                                        data-sku="{{ $product->sku }}"
                                        data-slug="{{ $product->slug }}"
                                        data-discount="{{ $product->discount_percent ?? 0 }}"
                                        data-category="{{ $product->category_id }}"
                                        data-status="{{ $product->status }}"
                                        data-description="{{ $product->description }}"
                                        data-tags='@json($product->tags->pluck("name"))'>
                                        <i class='bx bx-edit fs-5 me-3 text-muted'></i> Chỉnh sửa
                                    </a>
                                </li>
                                @endcan
                                @can('view', App\Models\ProductImage::class)
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body" href="{{ route('admin.product_images.index', ['keyword' => $product->sku]) }}">
                                        <i class='bx bx-images fs-5 me-3 text-muted'></i> Quản lý ảnh ({{ $product->images_count }})
                                    </a>
                                </li>
                                @endcan
                                @can('view', App\Models\ProductVariant::class)
                                @if($product->variants_count >= 0 || $product->category->has_variants)
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body" href="{{ route('admin.product_variants.index', ['keyword' => $product->sku]) }}">
                                        <i class='bx bx-layer fs-5 me-3 text-muted'></i> Phân loại ({{ $product->variants_count }})
                                    </a>
                                </li>
                                @endif
                                @endcan
                                <li>
                                    <hr class="dropdown-divider my-1 border-light">
                                </li>
                                @can('delete', $product)
                                <li>
                                    <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger delete-btn"
                                        href="javascript:void(0)"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal"
                                        data-id="{{ $product->id }}"
                                        data-name="{{ $product->name }}">
                                        <i class='bx bx-trash fs-5 me-3'></i> Xóa sản phẩm
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <div class="py-4">
                            <i class='bx bx-box fs-1 text-muted opacity-50 mb-3'></i>
                            <h6 class="text-muted fw-normal mb-0">Không tìm thấy sản phẩm nào.</h6>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer py-3 px-4 bg-card shadow-sm rounded-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

            <div class="d-flex flex-wrap align-items-center gap-3">
                <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                    @foreach(request()->except(['page', 'per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <label class="text-muted small mb-0 text-nowrap">Hiển thị:</label>
                    <select name="per_page" class="form-select form-select-sm form-control border-0 shadow-sm bg-light"
                        style="width: 70px;" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>

                <div class="vr mx-1"></div>

                <div class="text-muted small">
                    @if($products->total() > 0)
                    Từ <span class="fw-bold text-body">{{ $products->firstItem() }}</span>
                    đến <span class="fw-bold text-body">{{ $products->lastItem() }}</span>
                    trong tổng số <span class="fw-bold text-body">{{ $products->total() }} </span>sản phẩm
                    @else
                    Không có dữ liệu
                    @endif
                </div>
            </div>

            <div>
                @if($products->hasPages())
                {{ $products->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
        </div>
    </div>
</div>
</div>

@include('admin.products.modal-create')
@include('admin.products.modal-edit')
@include('admin.products.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/products/index.css') }}">
@endpush
@push('scripts')
<script>
    window.laravelConfig = {
        tagsWhitelist: @json($tags -> pluck('name')),

        hasErrors: @json($errors -> any()),
        oldMode: "{{ old('form_mode') }}",
        oldId: "{{ old('id') }}",
        oldTags: @json(old('tags')),
        oldStatus: "{{ old('status') }}"
    };
</script>
<script src="{{ asset('assets/admin/js/products/index.js') }}?v={{ time() }}"></script>
@endpush