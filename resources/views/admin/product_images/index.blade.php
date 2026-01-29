@extends('layouts.admin')

@section('title', 'Quản lý ảnh sản phẩm')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Quản lý ảnh sản phẩm</h3>
        </div>

        <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">
            <i class='bx bx-arrow-back fs-5'></i> <span class="fw-semibold">Về DS Sản phẩm</span>
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 24px;">
                        <i class='bx bxs-images'></i>
                    </div>
                    <div>
                        <p class="text-uppercase fw-semibold small mb-1 text-muted">Tổng hình ảnh</p>
                        <h4 class="fw-bold mb-0">{{ number_format($totalImages ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 24px;">
                        <i class='bx bxs-check-circle'></i>
                    </div>
                    <div>
                        <p class="text-uppercase fw-semibold small mb-1 text-muted">SP có ảnh</p>
                        <h4 class="fw-bold mb-0">{{ number_format($productsWithImages ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 24px;">
                        <i class='bx bxs-star'></i>
                    </div>
                    <div>
                        <p class="text-uppercase fw-semibold small mb-1 text-muted">Ảnh đại diện</p>
                        <h4 class="fw-bold mb-0">{{ number_format($primaryImagesCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 24px;">
                        <i class='bx bxs-layer'></i>
                    </div>
                    <div>
                        <p class="text-uppercase fw-semibold small mb-1 text-muted">Ảnh chi tiết</p>
                        <h4 class="fw-bold mb-0">{{ number_format($secondaryImagesCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="card-header bg-card p-3 mb-4 rounded-4 shadow-sm">
        <form method="GET" action="{{ route('admin.product_images.index') }}" class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring flex-grow-1 flex-md-grow-0" style="min-width: 200px; max-width: 300px;">
                <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>

                @if(request('category_id')) <input type="hidden" name="category_id" value="{{ request('category_id') }}"> @endif
                @if(request('has_images')) <input type="hidden" name="has_images" value="{{ request('has_images') }}"> @endif

                <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                    value="{{ request('keyword') }}" placeholder="Tìm tên hoặc SKU...">
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">

                @if(request()->anyFilled(['keyword', 'category_id', 'has_images']))
                <a href="{{ route('admin.product_images.index') }}"
                    class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0 order-last order-xl-first ms-auto ms-xl-0"
                    data-bs-toggle="tooltip" title="Xóa bộ lọc"
                    style="width: 36px; height: 36px;">
                    <i class='bx bx-refresh fs-5'></i>
                </a>
                @endif

                <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto" style="max-width: 100%; white-space: nowrap;">

                    {{-- Tất cả --}}
                    <a href="{{ request()->fullUrlWithQuery(['has_images' => null, 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('has_images') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Tất cả
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['has_images' => 'yes', 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('has_images') == 'yes' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Đã có ảnh
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['has_images' => 'no', 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('has_images') == 'no' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Chưa có ảnh
                    </a>
                </div>

                <div class="vr d-none d-lg-block mx-1 text-muted opacity-25" style="height: 40px;"></div>

                <div class="d-flex align-items-center gap-2">
                    <select name="category_id" class="form-select form-select-sm rounded-pill bg-light border-0 shadow-none cursor-pointer text-body fw-medium ps-1 pe-3 py-2"
                        style="min-width: 150px; max-width: 200px;" onchange="this.form.submit()">
                        <option value="">Danh mục</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </form>
    </div>

    <div class="card-body bg-card rounded-4 shadow-sm p-4 mb-4">
        @forelse($products as $product)
        <div class="product-group mb-5 pb-4 border-bottom last-no-border position-relative">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-3">

                <div class="d-flex align-items-center gap-3">
                    <div class="avatar-circle bg-light-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class='bx bxs-plant-pot fs-4'></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-body mb-0">{{ $product->name }}</h6>
                        <div class="d-flex align-items-center gap-2 small text-muted">
                            <span class="font-monospace">SKU: {{ $product->sku }}</span>
                            <span class="badge bg-light-secondary text-secondary rounded-pill px-2 py-1 border border-secondary border-opacity-10 fw-normal font-monospace">{{ $product->images->count() }} ảnh</span>
                        </div>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2 ms-auto">

                    <form action="{{ route('admin.product_images.destroySelected', $product) }}" method="POST" id="form-bulk-{{ $product->id }}" class="d-none">
                        @csrf
                    </form>

                    <div class="bulk-actions d-none align-items-center gap-2" id="bulk-toolbar-{{ $product->id }}">
                        <span class="text-danger fw-semibold small bg-danger-subtle px-2 py-1 rounded">
                            Đã chọn: <b class="selected-count">0</b>
                        </span>
                        <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 shadow-sm bulk-delete-btn d-flex align-items-center gap-1"
                            data-bs-toggle="modal"
                            data-bs-target="#deleteModal"
                            data-form-id="form-bulk-wrapper-{{ $product->id }}"> 
                            <i class='bx bx-trash'></i> Xóa đã chọn
                        </button>
                        <div class="vr mx-1 text-muted opacity-25"></div>
                    </div>

                    @if($product->images->count() > 0)
                    @can('delete', App\Models\ProductImage::class)
                    <button type="button"
                        class="btn btn-sm btn-light text-danger rounded-pill px-3 d-flex align-items-center gap-1 delete-all-trigger hover-shadow-sm"
                        data-bs-toggle="modal"
                        data-bs-target="#deleteModal"
                        data-url="{{ route('admin.product_images.destroyAll', $product) }}"
                        data-title="Xóa tất cả ảnh của {{ $product->name }}?"
                        data-desc="Tất cả {{ $product->images->count() }} ảnh sẽ bị xóa vĩnh viễn.">
                        <i class='bx bx-trash'></i> Xóa tất cả
                    </button>
                    @endcan
                    @endif
                </div>
            </div>

            <form action="{{ route('admin.product_images.destroySelected', $product) }}" method="POST" id="form-bulk-wrapper-{{ $product->id }}">
                @csrf
                <div class="row g-3">
                    @foreach($product->images as $image)
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="image-card position-relative rounded-4 overflow-hidden border bg-white h-100 shadow-sm transition-all cursor-pointer"
                            onclick="toggleImageSelection(this, '{{ $product->id }}', '{{ $image->id }}')">

                            <div class="ratio ratio-1x1 bg-light">
                                <img src="{{ asset('storage/' . $image->image_url) }}" class="object-fit-cover w-100 h-100" alt="Product Image">
                            </div>

                            <div class="image-overlay position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-between p-2">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="form-check m-0">
                                        <input class="form-check-input image-checkbox shadow-none border-2 cursor-pointer" type="checkbox"
                                            name="ids[]" value="{{ $image->id }}"
                                            id="cb-{{ $image->id }}"
                                            onclick="event.stopPropagation(); updateBulkToolbar('{{ $product->id }}');">
                                    </div>
                                    @if($image->is_primary)
                                    <span class="badge text-white shadow-sm rounded-circle p-1 d-flex align-items-center" title="Ảnh chính" style="background: var(--primary-color);">
                                        <i class='bx bxs-star'></i>
                                    </span>
                                    @endif
                                </div>

                                <div class="action-buttons d-flex justify-content-end gap-2">
                                    <button type="button"
                                        class="btn btn-sm btn-white text-body rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center hover-scale"
                                        style="width: 32px; height: 32px;"
                                        title="Phóng to"
                                        onclick="viewImage(event, '{{ asset('storage/' . $image->image_url) }}')">
                                        <i class='bx bx-zoom-in fs-5'></i>
                                    </button>
                                    @can('update', $image)
                                    <button type="button"
                                        class="btn btn-sm btn-white text-body rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center btn-edit-image"
                                        style="width: 32px; height: 32px;"
                                        data-id="{{ $image->id }}"
                                        data-product-id="{{ $product->id }}"
                                        data-url="{{ asset('storage/' . $image->image_url) }}"
                                        data-is-primary="{{ $image->is_primary ? 'true' : 'false' }}"
                                        onclick="prepareEditModal(event, this)">

                                        <i class='bx bx-edit-alt fs-5'></i>
                                    </button>
                                    @endcan

                                    @can('delete', $image)
                                    <button type="button"
                                        class="btn btn-sm btn-white text-danger rounded-circle shadow-sm p-0 d-flex align-items-center justify-content-center"
                                        style="width: 32px; height: 32px;"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal"
                                        data-url="{{ route('admin.product_images.destroy', ['product' => $product->id, 'productImage' => $image->id]) }}"
                                        data-title="Xóa ảnh này?"
                                        onclick="event.stopPropagation()"> {{-- <--- QUAN TRỌNG --}}
                                        <i class='bx bx-trash fs-5'></i>
                                    </button>
                                    @endcan
                                </div>
                            </div>
                            <div class="selected-overlay position-absolute top-0 start-0 w-100 h-100 border border-2 border-primary rounded-4 d-none pointer-events-none"></div>
                        </div>
                    </div>
                    @endforeach

                    @can('create', App\Models\ProductImage::class)
                    <div class="col-6 col-md-3 col-lg-2">
                        <div class="h-100 btn-add-image cursor-pointer"
                            data-product-id="{{ $product->id }}"
                            data-product-name="{{ $product->name }}">
                            <div class="ratio ratio-1x1 border border-dashed border-2 rounded-4 bg-light hover-bg-light-success transition-all d-flex align-items-center justify-content-center h-100">
                                <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100 text-body">
                                    <i class='bx bx-plus fs-4'></i>
                                    <span class="small fw-bold">Thêm ảnh</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan
                </div>
            </form>
        </div>
        @empty
        <div class="text-center py-5">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                <i class='bx bx-search fs-1 text-body opacity-50'></i>
            </div>
            <h5 class="fw-bold text-body">Không tìm thấy sản phẩm nào</h5>
            <p class="text-muted small">Thử tìm kiếm với từ khóa khác.</p>
        </div>
        @endforelse
    </div>

    @if($products->hasPages())
    <div class="card-footer bg-card p-4 d-flex justify-content-between align-items-center rounded-4 shadow-sm">
        <div class="text-muted small">
            Đang hiển thị <span class="fw-bold text-body">{{ $products->firstItem() }}</span> đến <span class="fw-bold text-body">{{ $products->lastItem() }}</span> trong tổng số <span class="fw-bold text-body">{{ $products->total() }}</span> ản phẩm
        </div>
        <div>
            {{ $products->links('vendor.pagination.bootstrap-4') }}
        </div>

    </div>
    @endif
</div>

@include('admin.product_images.modal-create')
@include('admin.product_images.modal-edit')
@include('admin.product_images.modal-delete')

<div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <button type="button"
                class="position-absolute top-0 end-0 m-3 me-5 z-3 border-0 bg-dark rounded-circle d-flex align-items-center justify-content-center opacity-75 hover-opacity-100 shadow-sm"
                style="width: 40px; height: 40px; transition: all 0.2s;"
                data-bs-dismiss="modal"
                aria-label="Close">
                <i class='bx bx-x fs-3 text-white'></i>
            </button>
            <div class="modal-body p-0 text-center d-flex align-items-center justify-content-center" style="min-height: 80vh;">
                <img id="view_image_target" src="" class="img-fluid rounded-3 shadow-lg object-fit-contain" style="max-height: 80vh; max-width: 90%;">

            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/images.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/images.js') }}?v={{ time() }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        @if($errors->has('images') || $errors->has('images.*'))
            const oldProductId = "{{ old('product_id') }}";
            if (oldProductId) {
                const btn = document.querySelector(`.btn-add-image[data-product-id="${oldProductId}"]`);
                if (btn) {
                    btn.click();
                }
            }
        @endif

        @if($errors->has('image') || $errors->has('is_primary'))
            const oldImageId = "{{ old('image_id') }}";
            if (oldImageId) {
                const btn = document.querySelector(`.btn-edit-image[data-id="${oldImageId}"]`);
                if (btn) {
                    btn.click();
                }
            }
        @endif
    });
</script>
@endpush