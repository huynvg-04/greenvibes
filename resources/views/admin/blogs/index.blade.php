@extends('layouts.admin')

@section('title', 'Quản lý bài viết')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Blog</h3>
        </div>

        <div class="d-flex gap-2">
            @can('create', App\Models\Blog::class)
            <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
                data-bs-toggle="modal" data-bs-target="#createBlogModal">
                <i class='bx bx-plus fs-5'></i> <span class="fw-semibold">Thêm mới</span>
            </button>
            @endcan
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bxs-file'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng bài viết</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bxs-globe'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đã công khai</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['published']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bx-edit'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Bài nháp</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['draft']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bx-show'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng lượt xem</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['views']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-header bg-card p-3 mb-4 rounded-4 shadow-sm" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">

        <form action="{{ route('admin.blogs.index') }}" method="GET" class="row g-3 align-items-center justify-content-between">

            <div class="col-12 col-md-5">
                <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                    <span class="input-group-text bg-transparent border-0 pe-2 text-body">
                        <i class='bx bx-search'></i>
                    </span>
                    @if(request()->has('is_published'))
                    <input type="hidden" name="is_published" value="{{ request('is_published') }}">
                    @endif
                    <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                        placeholder="Tìm tiêu đề, mô tả bài viết..." value="{{ request('keyword') }}">
                </div>
            </div>

            <div class="col-12 col-md-7 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">

                {{-- Nút Reset bộ lọc --}}
                @if(request()->anyFilled(['keyword', 'is_published', 'sort']))
                <a href="{{ route('admin.blogs.index') }}"
                    class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger"
                    data-bs-toggle="tooltip" title="Xóa bộ lọc"
                    style="width: 34px; height: 34px;">
                    <i class='bx bx-refresh fs-5'></i>
                </a>
                @endif


                <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center shadow-sm">
                    <button type="submit" name="is_published" value=""
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('is_published') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Tất cả
                    </button>

                    <button type="submit" name="is_published" value="1"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('is_published') == '1' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Công khai
                    </button>

                    <button type="submit" name="is_published" value="0"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('is_published') == '0' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Bản nháp
                    </button>
                </div>
                <div class="vr mx-1 text-muted opacity-25"></div>

                <div class="d-flex align-items-center">
                    <select name="sort" class="form-select form-select-sm rounded-pill bg-card form-control border-0 text-muted cursor-pointer ps-3 pe-4 shadow-sm py-2"
                        style="box-shadow: none;"
                        onchange="this.form.submit()">
                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="views_desc" {{ request('sort') == 'views_desc' ? 'selected' : '' }}>Xem nhiều nhất</option>
                        <option value="views_asc" {{ request('sort') == 'views_asc' ? 'selected' : '' }}>Xem ít nhất</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive bg-card rounded-4 shadow-sm" style="min-height: 400px;">
            <table class="table modern-table align-middle mb-0 table-hover custom-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase text-center" style="width: 60px;">ID</th>
                        <th class="py-3 text-body small fw-bold text-uppercase" style="width: 100px;">Hình ảnh</th>
                        <th class="py-3 text-body small fw-bold text-uppercase" style="width: 440px;">Tiêu đề / Thông tin</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Trạng thái</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Tác giả</th>
                        <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($blogs as $blog)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4 text-center text-body font-monospace">#{{ $blog->id }}</td>

                        <td>
                            <div class="ratio ratio-4x3 rounded-3 overflow-hidden bg-light border">
                                @if($blog->thumbnail)
                                <img src="{{ asset('storage/' . $blog->thumbnail) }}" class="object-fit-cover" alt="Thumbnail">
                                @else
                                <div class="d-flex align-items-center justify-content-center h-100 text-body">
                                    <i class='bx bx-image fs-3 opacity-50'></i>
                                </div>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <a href="{{ route('admin.blogs.show', $blog->id) }}" class="fw-bold text-body product-link mb-1 text-truncate" style="max-width: 350px;">
                                    {{ $blog->title }}
                                </a>
                                <div class="d-flex align-items-center gap-3 text-body small">
                                    <span class="d-flex align-items-center gap-1"><i class='bx bx-calendar'></i> {{ $blog->created_at->format('d/m/Y') }}</span>
                                    <span class="d-flex align-items-center gap-1"><i class='bx bx-show'></i> {{ $blog->views }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            @if($blog->is_published)
                            <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace d-inline-flex align-items-center gap-1">
                                Công khai
                            </span>
                            @else
                            <span class="badge bg-light-warning text-warning border border-warning border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace d-inline-flex align-items-center gap-1">
                                Nháp
                            </span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="text-body small fw-medium">{{ $blog->user->name ?? 'Admin' }}</span>
                            </div>
                        </td>

                        <td class="pe-4 text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-light border-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card"
                                    style="border-radius: 12px; min-width: 200px; z-index: 1050;">
                                    @can('update', $blog)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-blog"
                                            href="javascript:void(0)"
                                            data-id="{{ $blog->id }}"
                                            data-title="{{ $blog->title }}"
                                            data-slug="{{ $blog->slug }}"
                                            data-excerpt="{{ $blog->excerpt }}"
                                            data-content="{{ $blog->content }}"
                                            data-category-id="{{ $blog->category_id }}"
                                            data-is-published="{{ $blog->is_published ? 'true' : 'false' }}"
                                            data-thumbnail="{{ asset('storage/'.$blog->thumbnail) }}">
                                            <i class='bx bx-edit-alt fs-5 me-3 text-muted'></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endcan

                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>

                                    @can('delete', $blog)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $blog->id }}"
                                            data-title="{{ $blog->title }}">
                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa bài viết
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bx-news fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có bài viết nào.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($blogs->hasPages())
    <div class="card-footer bg-card border-top py-3 px-4 rounded-4 shadow-sm mt-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-body small">
                Hiển thị <span class="fw-bold text-body">{{ $blogs->firstItem() }}</span> - <span class="fw-bold text-body">{{ $blogs->lastItem() }}</span>
                trong tổng số <span class="fw-bold text-body">{{ $blogs->total() }}</span> bài viết
            </div>
            <div>
                {{ $blogs->appends(request()->all())->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>

@include('admin.blogs.modal-create')
@include('admin.blogs.modal-edit')
@include('admin.blogs.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/blogs.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/blogs.js') }}"></script>
@endpush