@extends('layouts.admin')

@section('title', 'Quản lý Banner')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Banner</h3>
        </div>

        @can('create', App\Models\Banner::class)
        <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createBannerModal">
            <i class='bx bx-plus fs-5'></i> <span class="fw-bold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="card-header bg-card p-3 mb-4 rounded-4 shadow-sm">
        <form action="{{ route('admin.banners.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-5">
                    <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                        <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>
                        <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                            placeholder="Tìm tiêu đề banner..." value="{{ request('keyword') }}">
                    </div>
                </div>

                <div class="col-12 col-md-7 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                    <form action="{{ route('admin.banners.index') }}" method="GET" class="d-flex align-items-center gap-2 m-0">
                         @if(request()->anyFilled(['keyword', 'status']))
                    <a href="{{ route('admin.banners.index') }}"
                        class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-dark"
                        data-bs-toggle="tooltip" title="Xóa bộ lọc"
                        style="width: 34px; height: 34px;">
                        <i class='bx bx-refresh fs-5'></i>
                    </a>
                    @endif
                        @if(request('keyword'))
                        <input type="hidden" name="keyword" value="{{ request('keyword') }}">
                        @endif
                        <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center">
                            <button type="submit" name="status" value=""
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Tất cả
                            </button>
                            <button type="submit" name="status" value="1"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == '1' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Hiển thị
                            </button>
                            <button type="submit" name="status" value="0"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == '0' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Đang ẩn
                            </button>
                        </div>
                    </form>
                   
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive bg-card rounded-4 shadow-sm" style="min-height: 400px;">
            <table class="table modern-table align-middle mb-0 table-hover custom-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase" style="width: 60px;">ID</th>
                        <th class="py-3 text-body small fw-bold text-uppercase" style="width: 180px;">Hình ảnh</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Tiêu đề / Link</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-end text-body small fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4 text-body font-monospace">#{{ $banner->id }}</td>
                        <td>
                            <div class="ratio ratio-16x9 rounded-3 overflow-hidden border shadow-sm cursor-pointer position-relative group-hover-zoom btn-view-image"
                                style="width: 120px;"
                                data-image="{{ asset('storage/'.$banner->image) }}">

                                <img src="{{ asset('storage/'.$banner->image) }}" class="object-fit-cover transition-transform" alt="Banner">

                                <div class="position-absolute top-0 start-0 w-100 h-100 bg-dark bg-opacity-25 d-flex align-items-center justify-content-center opacity-0 hover-opacity-100 transition-all">
                                    <i class='bx bx-zoom-in text-white fs-4'></i>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-body mb-1">{!! $banner->title !!}</span>
                                @if($banner->link)
                                <a href="{{ $banner->link }}" target="_blank" class="text-primary small text-decoration-none d-inline-flex align-items-center text-truncate" style="max-width: 250px;">
                                    <i class='bx bx-link-external me-1'></i> {{ $banner->link }}
                                </a>
                                @else
                                <span class="text-body small fst-italic">Không có liên kết</span>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            @if($banner->status)
                            <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center fw-normal font-monospace gap-1">
                                Hiển thị
                            </span>
                            @else
                            <span class="badge bg-light-secondary text-secondary border border-secondary border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace d-inline-flex align-items-center gap-1">
                                Đang ẩn
                            </span>
                            @endif
                        </td>

                        <td class="pe-4 text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-light border-0 text-body" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card"
                                    style="border-radius: 12px; min-width: 200px; z-index: 1050;">

                                    @can('update', $banner)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-banner"
                                            href="javascript:void(0)"
                                            data-id="{{ $banner->id }}"
                                            data-title="{{ $banner->title }}"
                                            data-link="{{ $banner->link }}"
                                            data-status="{{ $banner->status }}"
                                            data-image="{{ asset('storage/' . $banner->image) }}"
                                            title="Chỉnh sửa banner">
                                            <i class='bx bx-edit-alt fs-5 me-3 text-body'></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endcan

                                    <li>
                                        <hr class="dropdown-divider my-1 border-light">
                                    </li>

                                    @can('delete', $banner)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-banner-id="{{ $banner->id }}"
                                            data-banner-name="{{ $banner->title }}">
                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa banner
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bx-images fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có banner nào.</h6>
                                <p class="text-body small">Hãy thêm banner mới để trang trí website.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($banners->hasPages())
    <div class="card-footer bg-card py-3 px-4 mt-4 rounded-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-body small">
                Hiển thị <span class="fw-bold text-body">{{ $banners->firstItem() }}</span> - <span class="fw-bold text-body">{{ $banners->lastItem() }}</span>
                trong tổng số <span class="fw-bold text-body">{{ $banners->total() }}</span> banner
            </div>
            <div>
                {{ $banners->appends(request()->all())->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>


@include('admin.banners.modal-create')
@include('admin.banners.modal-edit')
@include('admin.banners.modal-delete')

<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 position-relative text-center">
                <button type="button" class="btn btn-icon btn-light btn-transparent-hover rounded-circle border-0 shadow position-absolute top-0 end-0 mt-n3 me-n3 z-index-3"
                    data-bs-dismiss="modal" aria-label="Close">
                    <i class='bx bx-x fs-4'></i>
                </button>

                <img id="previewImage" src="" class="img-fluid rounded-4 shadow-lg" alt="Banner Preview" style="max-height: 85vh;">

            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/banners.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/banners.js') }}"></script>
@endpush