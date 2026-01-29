@extends('layouts.admin')

@section('title', 'Quản lý phân loại sản phẩm')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Quản lý phân loại</h3>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm">
                <i class='bx bx-arrow-back'></i> <span class="fw-semibold">Về DS Sản phẩm</span>
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.product_variants.index') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                            <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>

                            @if(request('variant_status'))
                            <input type="hidden" name="variant_status" value="{{ request('variant_status') }}">
                            @endif

                            <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                                placeholder="Tìm theo tên SP, SKU cha hoặc SKU biến thể..."
                                value="{{ request('keyword') }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-7 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                        @if(request()->anyFilled(['keyword', 'variant_status']))
                        <a href="{{ route('admin.product_variants.index') }}"
                            class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger"
                            data-bs-toggle="tooltip" title="Xóa bộ lọc"
                            style="width: 34px; height: 34px;">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                        @endif

                        <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center">
                            <button type="submit" name="variant_status" value=""
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('variant_status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Tất cả
                            </button>

                            <button type="submit" name="variant_status" value="no_variants"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('variant_status') == 'no_variants' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Chưa có phân loại
                            </button>

                            <button type="submit" name="variant_status" value="has_variants"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('variant_status') == 'has_variants' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Đã có phân loại
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>



    @forelse($products as $product)
    <div class="modern-table bg-card rounded-4 shadow-sm mb-4 overflow-hidden">
        <div class="card-header bg-card p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center">
                <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                    <i class='bx bxs-plant-pot'></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">
                        <a href="{{ route('admin.products.show', $product->id) }}"
                            class="text-body text-decoration-none product-link"
                            title="Xem chi tiết sản phẩm">
                            {{ $product->name }}
                        </a>
                    </h5>
                    <div class="d-flex align-items-center text-body small ">
                        <span class="me-3"><i class='bx bx-barcode me-1'></i>SKU: {{ $product->sku ?? '---' }}</span>
                        <span><i class='bx bx-hash me-1'></i>ID: {{ $product->id }}</span>
                    </div>
                </div>
            </div>

            @can('create', App\Models\ProductVariant::class)
            <button type="button"
                class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm btn-add-variant"
                data-bs-toggle="modal"
                data-bs-target="#createVariantModal"
                data-product-id="{{ $product->id }}"
                data-product-name="{{ $product->name }}"
                data-product-sku="{{ $product->sku }}">
                <i class='bx bx-plus fs-5'></i> Thêm mới
            </button>
            @endcan
        </div>


        @if($product->variants->count() > 0)
        <div class="table-responsive bg-card" style="min-height: 230px;">
            <table class="table modern-table align-middle custom-table mb-4 border-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 align-middle py-3 text-body small fw-bold text-uppercase">SKU Phân loại</th>
                        <th class="align-middle py-3 text-body small fw-bold text-uppercase">Tên hiển thị</th>
                        <th class="align-middle py-3 text-body small fw-bold text-uppercase">Thuộc tính</th>
                        <th class="align-middle py-3 text-body small fw-bold text-uppercase" style="width: 160px;">Giá bán</th>
                        <th class="text-center align-middle py-3 small text-body fw-bold text-uppercase">Tồn kho</th>
                        <th class="pe-4 text-center align-middle py-3 small text-bo fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($product->variants as $variant)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4">
                            <span class="fw-semibold text-body">{{ $variant->sku }}</span>
                        </td>

                        <td>
                            <a href="{{ route('admin.products.show', $product->id) }}"
                                class="fw-medium text-body text-decoration-none product-link"
                                title="Xem chi tiết sản phẩm gốc">
                                {{ $variant->name }}
                            </a>
                        </td>

                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                @foreach($variant->attributeValues as $val)
                                <span class="badge bg-light-info border border-info border-opacity-10 text-info rounded-pill fw-normal px-3 py-2 font-monospace">
                                    {{ $val->attribute->name }}: {{ $val->value }}
                                </span>
                                @endforeach
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column fw-bold" style="font-size: 13px;">
                                <div class="d-flex justify-content-between gap-3">
                                    <span class="text-body">Nhập:</span>
                                    <span>{{ number_format($variant->standard_cost) }}</span>
                                </div>
                                <div class="d-flex justify-content-between gap-3 text-success">
                                    <span>Niêm yết:</span>
                                    <span>{{ number_format($variant->list_price) }}</span>
                                </div>
                                @if($variant->sale_price < $variant->list_price)
                                    <div class="d-flex justify-content-between gap-3 text-danger">
                                        <span>Giảm giá:</span>
                                        <span class="">{{ number_format($variant->sale_price) }}</span>
                                    </div>
                                    @endif
                            </div>
                        </td>

                        <td class="text-center">
                            @if($variant->stock > 0)
                            <span class="badge bg-light-success text-success border border-success border-opacity-10 px-3 py-2 rounded-pill fw-normal font-monospace">
                                {{ $variant->stock }}
                            </span>
                            @else
                            <span class="badge bg-light-danger text-danger border border-success border-opacity-10 px-3 py-2 rounded-pill fw-normal font-monospace">
                                Hết hàng
                            </span>
                            @endif
                        </td>

                        <td class="pe-4 text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-light border-0 text-muted" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end border-0 p-2 bg-card"
                                    style="border-radius: 12px; min-width: 200px; z-index: 1050;">
                                    @can('update', $variant)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-variant"
                                            href="javascript:void(0)"
                                            data-id="{{ $variant->id }}"
                                            data-sku="{{ $variant->sku }}"
                                            data-standard-cost="{{ $variant->standard_cost }}"
                                            data-list-price="{{ $variant->list_price }}"

                                            data-attributes="{{ json_encode($variant->attributeValues->pluck('id')) }}"

                                            title="Chỉnh sửa phân loại">
                                            <i class='bx bx-edit-alt fs-5 me-3 text-muted'></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endcan

                                    <li>
                                        <hr class="dropdown-divider my-1 border-light">
                                    </li>

                                    @can('delete', $variant)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $variant->id }}"
                                            data-name="{{ $variant->name }}">
                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa phân loại
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach

                    <tr class="bg-opacity-10 text-body bg-card ">
                        <td colspan="4" class="ps-4 text-end fw-bold text-body small text-uppercase ">Tổng tồn kho:</td>
                        <td class="text-center fw-bold text-body small ">
                            {{ $product->variants->sum('stock') }}
                        </td>
                        <td class=""></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-5 text-body">
            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                <i class='bx bxs-layer fs-1 text-secondary opacity-50'></i>
            </div>
            <p class="mb-0">Sản phẩm này chưa có phân loại nào.</p>
            @endif
        </div>
    </div>

    @empty
    <div class="text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class='bx bx-search fs-1 text-muted opacity-50'></i>
        </div>
        <h5 class="fw-bold text-body">Không tìm thấy kết quả</h5>
        <p class="text-muted small">Không có sản phẩm hoặc phân loại nào khớp với từ khóa "<strong>{{ request('keyword') }}</strong>"</p>
    </div>
    @endforelse

    @if($products->hasPages())
    <div class="card-footer py-3 px-4 bg-card rounded-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-muted small">
                Đang hiển thị <span class="fw-bold text-body">{{ $products->firstItem() }}</span> đến <span class="fw-bold text-body">{{ $products->lastItem() }}</span> trong tổng số <span class="fw-bold text-body">{{ $products->total() }}</span> mục
            </div>
            <div>
                {{ $products->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>
@include('admin.product_variants.modal-create')
@include('admin.product_variants.modal-edit')
@include('admin.product_variants.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/variants.css') }}">
@endpush

@section('scripts')
<script src="{{ asset('assets/admin/js/variants.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors -> any())
        @if(old('_method') == 'PUT')
        const editModalEl = document.getElementById('editVariantModal');
        if (editModalEl) {
            const editModal = new bootstrap.Modal(editModalEl);
            const id = @json(old('id'));

            if (id) {
                document.getElementById('editVariantForm').action = `/admin/product_variants/${id}`;

                const savedSkuName = @json(old('modal_edit_sku_display'));
                const currentSkuInput = @json(old('sku'));

                const titleSpan = document.getElementById('editModalSku');
                if (titleSpan) {

                    titleSpan.textContent = savedSkuName || currentSkuInput || '...';
                }
            }
            editModal.show();
        }
        @else
        const createModalEl = document.getElementById('createVariantModal');
        if (createModalEl) {
            const createModal = new bootstrap.Modal(createModalEl);

            const parentId = @json(old('product_ref_id'));
            if (parentId) {
                document.getElementById('createVariantForm').action = `/admin/products/${parentId}/variants`;

                const oldName = @json(old('modal_product_name'));
                const oldPrefix = @json(old('modal_sku_prefix'));

                if (oldName) document.getElementById('modalProductName').textContent = oldName;
                if (oldPrefix) document.getElementById('modalSkuPrefix').textContent = oldPrefix;
            }

            createModal.show();
        }
        @endif
        @endif
    });
</script>
@endsection