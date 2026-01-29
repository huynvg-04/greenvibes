@extends('layouts.admin')

@section('title', 'Chi tiết: ' . $product->name)

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">
                {{ $product->name }}
            </h3>
        </div>

        <div class="d-flex flex-wrap gap-2">
            @can('viewAny', \App\Models\ProductImage::class)
            <a href="{{ route('admin.product_images.index', ['keyword' => $product->sku])}}" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
                <span>Quản lý Ảnh</span>
            </a>
            @endcan
            @can('viewAny', \App\Models\ProductVariant::class)
            <a href="{{ route('admin.product_variants.index', ['keyword' => $product->sku]) }}" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
                <span>Phân loại</span>
            </a>
            @endcan

            <button type="button"
                class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 btn-edit-product"
                data-bs-toggle="modal"
                data-bs-target="#editProductModal"
                data-id="{{ $product->id }}"
                data-name="{{ $product->name }}"
                data-sku="{{ $product->sku }}"
                data-slug="{{ $product->slug }}"
                data-discount="{{ $product->discount_percent }}"
                data-category="{{ $product->category_id }}"
                data-status="{{ $product->status }}"
                data-description="{{ $product->description }}"
                data-tags='@json($product->tags->pluck("name"))'>
                <span>Chỉnh sửa</span>
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-12 col-lg-8">

            <div class="card-header bg-card p-4 d-flex justify-content-between align-items-center rounded-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-body mb-0 d-flex align-items-center gap-1"><i class='bx bx-info-circle me-2 text-body'></i>Thông tin chung</h5>
                @if($product->status == 1 || $product->status == 'active')
                <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center fw-normal font-monospace">
                    Đang kinh doanh
                </span>
                @else
                <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center fw-normal font-monospace">
                    Ngừng kinh doanh
                </span>
                @endif
            </div>
            <div class="card-body bg-card rounded-4 shadow-sm mb-4 p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="text-body small text-uppercase fw-bold mb-1">Tên sản phẩm</label>
                        <p class="fw-bold text-body fs-5 mb-0">{{ $product->name }}</p>
                    </div>
                    <div class="col-md-6">
                        <label class="text-body small text-uppercase fw-bold mb-1">Mã SKU</label>
                        <p class="font-monospace text-body   fw-bold fs-5 mb-0">{{ $product->sku }}</p>
                    </div>

                    <div class="col-md-6">
                        <label class="text-body small text-uppercase fw-bold mb-1">Danh mục</label>
                        <div>
                            @if($product->category)
                            <span class="badge rounded-pill border border-info border-opacity-10 fw-normal font-monospace px-3 py-2 bg-light-info text-info">
                                {{ $product->category->name }}
                            </span>
                            @else
                            <span class="text-body fst-italic">Chưa phân loại</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="text-body small text-uppercase fw-bold mb-1">Slug (SEO)</label>
                        <p class="text-body small bg-light p-2 rounded mb-0 text-truncate">{{ $product->slug }}</p>
                    </div>

                    <div class="col-12">
                        <label class="text-body small text-uppercase fw-bold mb-1">Mô tả</label>
                        <div class="bg-light p-3 rounded-3 text-body" style="min-height: 100px;">
                            @if($product->description)
                            {!! nl2br($product->description) !!}
                            @else
                            <span class="text-body fst-italic">Không có mô tả chi tiết.</span>
                            @endif
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="text-body small text-uppercase fw-bold mb-2">Tags</label>
                        <div class="d-flex flex-wrap gap-2">
                            @forelse($product->tags as $tag)
                            <span class="badge bg-white border text-body fw-normal px-3 py-2 shadow-sm">#{{ $tag->name }}</span>
                            @empty
                            <span class="text-body small fst-italic">Không có tag nào.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-header bg-card border-bottom p-4 d-flex justify-content-between align-items-center mb-4 rounded-4 shadow-sm">
                <h5 class="fw-bold text-body mb-0 d-flex align-items-center gap-1"><i class='bx bxs-layer me-2 text-body'></i>Phân loại hàng</h5>
            </div>


            <div class="table-responsive rounded-4 shadow-sm">
                <table class="table modern-table bg-card align-middle mb-0 table-hover custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 text-body py-3 small fw-bold text-uppercase">SKU</th>
                            <th class="text-body small py-3 fw-bold text-uppercase" style="min-width: 70px;">Tên PL</th>
                            <th class="text-body small py-3 fw-bold text-uppercase">Thuộc tính</th>
                            <th class="text-end text-body py-3 small fw-bold text-uppercase">Giá nhập</th>
                            <th class="text-end text-body py-3 small fw-bold text-uppercase">Giá niêm yết</th>
                            <th class="text-center text-body py-3 small fw-bold text-uppercase pe-4">Tồn kho</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($product->variants as $variant)
                        <tr>
                            <td class="ps-4 font-monospace fw-semibold">{{ $variant->sku }}</td>
                            <td>{{ $variant->name }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($variant->attributeValues as $val)
                                    <span class="badge rounded-pill fw-normal px-3 py-2 bg-light-info text-info border border-info border-opacity-10 font-monospace">{{ $val->attribute->name }}: {{ $val->value }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="text-end text-body">{{ number_format($variant->standard_cost) }}</td>
                            <td class="text-end text-success fw-bold">{{ number_format($variant->list_price) }}</td>
                            <td class="text-center pe-4">
                                @if($variant->stock > 0)
                                <span class="badge bg-light-success text-success border border-success border-opacity-10 fw-normal font-monospace rounded-pill py-2 px-3">{{ $variant->stock }}</span>
                                @else
                                <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 fw-normal font-monospace rounded-pill py-2 px-3">Hết hàng</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <div class="bg-light rounded-circle p-3 mb-2">
                                        <i class='bx bxs-layer fs-1 text-body opacity-50'></i>
                                    </div>
                                    <p class="text-body small mb-0">Sản phẩm này chưa có phân loại nào.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-12 col-lg-4">

            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-card border-bottom p-4" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="fw-bold text-body mb-0 d-flex align-items-center gap-1"><i class='bx bx-images me-2 text-body'></i>Thư viện ảnh</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3 position-relative">
                        @php
                        $primaryImage = $product->images->where('is_primary', true)->first();
                        $mainImgUrl = $primaryImage ? asset('storage/' . $primaryImage->image_url) : 'https://via.placeholder.com/400x400?text=No+Image';
                        @endphp
                        <div class="ratio ratio-1x1 rounded-4 overflow-hidden border shadow-sm">
                            <img src="{{ $mainImgUrl }}" class="object-fit-contain p-2" alt="Main Image">
                        </div>
                        <span class="position-absolute top-0 start-0 m-3 badge bg-warning text-dark shadow-sm d-inline-flex align-items-center rounded-pill border border-warning border-opacity-10 ">Ảnh chính</span>
                    </div>

                    @if($product->images->count() > 1)
                    <div class="row g-2">
                        @foreach($product->images as $img)
                        @if(!$img->is_primary)
                        <div class="col-3">
                            <div class="ratio ratio-1x1 rounded-3 overflow-hidden border cursor-pointer hover-shadow transition-all">
                                <img src="{{ asset('storage/' . $img->image_url) }}" class="object-fit-cover w-100 h-100" alt="Gallery">
                            </div>
                        </div>
                        @endif
                        @endforeach
                    </div>
                    @endif

                    @if($product->images->count() == 0)
                    <div class="text-center p-3 bg-light rounded-3 text-body small">
                        Chưa có ảnh nào.
                    </div>
                    @endif
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">

                        <li class="list-group-item p-3 d-flex justify-content-between align-items-center bg-transparent">
                            <span class="text-body small text-uppercase fw-bold">Tổng tồn kho</span>
                            <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill py-2 px-3 fw-normal font-monospace">{{ $product->variants->sum('stock') }}</span>
                        </li>

                        <li class="list-group-item p-3 d-flex justify-content-between align-items-center bg-transparent border-bottom-0">
                            <span class="text-body small text-uppercase fw-bold">Ngày tạo</span>
                            <span class="text-body small">{{ $product->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    </ul>

                    @if($product->promotion)
                    <div class="p-3 bg-light-danger bg-opacity-10 border-top border-danger border-opacity-10">
                        <div class="d-flex align-items-center text-danger fw-bold mb-2">
                            <i class='bx bxs-offer me-2 fs-5'></i> Đang khuyến mãi
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small text-body">{{ $product->promotion->name }}</span>
                            <span class="badge bg-danger">-{{ $product->promotion->discount }}%</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center border-top border-danger border-opacity-10 pt-2 mt-2">
                            <span class="small fw-bold text-danger">Giá sau giảm:</span>
                            <span class="fw-bold text-danger fs-5">{{ number_format($product->promotion_price) }} ₫</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</div>

@include('admin.products.modal-edit')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/product-detail.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/products/index.js') }}"></script>
@endpush