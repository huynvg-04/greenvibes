@extends('layouts.admin')

@section('title', 'Quản lý danh mục')

@section('content')
<div class="container-fluid px-0 min-vh-100">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">
                Quản lý danh mục
            </h3>
        </div>

        @can('create', App\Models\Category::class)
        <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createModal">
            <i class='bx bx-plus fs-5'></i><span class="fw-semibold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.categories.index') }}">
                <div class="row g-3 align-items-center">

                    <div class="col-12 col-md-5">
                        <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                            <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>
                            <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                                placeholder="Tìm danh mục..." value="{{ request('keyword') }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-7 d-flex flex-wrap gap-2 justify-content-md-end align-items-center">
                        @if(request()->anyFilled(['keyword', 'type']))
                        <a href="{{ route('admin.categories.index') }}"
                            class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger"
                            data-bs-toggle="tooltip" title="Xóa bộ lọc"
                            style="width: 34px; height: 34px;">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                        @endif
                        <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center">

                            <button type="submit" name="type" value=""
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('type') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Tất cả
                            </button>

                            <button type="submit" name="type" value="product"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('type') == 'product' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Sản phẩm
                            </button>

                            <button type="submit" name="type" value="blog"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('type') == 'blog' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Bài viết
                            </button>
                        </div>


                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive rounded-4" style="min-height: 400px;">
                <table class="table modern-table align-middle mb-0 table-hover custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="align-middle ps-4 text-body text-uppercase small fw-bold" style="width: 60px;">ID</th>
                            <th class="py-3 text-body small fw-bold text-uppercase text-center" style="width: 80px;">Ảnh</th>
                            <th class="py-3 text-body small fw-bold text-uppercase" style="width: 250px;">Tên danh mục</th>
                            <th class="py-3 text-body small fw-bold text-uppercase">Slug</th>
                            <th class="py-3 text-body small fw-bold text-uppercase text-center">Loại</th>
                            <th class="pe-4 py-3 text-end text-body small fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr class="transition-all hover-bg-light">
                            <td class="ps-4 text-body font-monospace">#{{ $category->id }}</td>

                            <td class="text-center">
                                @if($category->image)
                                <div class="ratio ratio-1x1 rounded-3 overflow-hidden border mx-auto" style="width: 40px;">
                                    <img src="{{ asset('storage/'.$category->image) }}" class="object-fit-cover">
                                </div>
                                @else
                                <div class="bg-light rounded-3 d-inline-flex align-items-center justify-content-center text-body" style="width: 40px; height: 40px;">
                                    <i class='bx bx-image-alt fs-5'></i>
                                </div>
                                @endif
                            </td>

                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-body fs-6">{{ $category->name }}</span>
                                    @if($category->description)
                                    <small class="text-body text-truncate" style="max-width: 200px;">{{ $category->description }}</small>
                                    @endif
                                </div>
                            </td>

                            <td>
                                <span class="badge bg-light text-body rounded-pill fw-normal py-2 px-3 font-monospace border border-secondary border-opacity-10">
                                    {{ $category->slug }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($category->type == 'product')
                                <span class="badge rounded-pill fw-normal px-3 py-2 bg-light-info text-info border border-info border-opacity-10 font-monospace">
                                    Sản phẩm
                                </span>
                                @else
                                <span class="badge rounded-pill fw-normal px-3 py-2 bg-light-warning text-warning border border-warning border-opacity-10 font-monospace">
                                    Bài viết
                                </span>
                                @endif
                            </td>

                            <td class="pe-4 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-light border-0 text-muted" type="button"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                        data-bs-boundary="viewport">
                                        <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card" style="border-radius: 12px; min-width: 200px; z-index: 1050;">
                                        @can('update', $category)
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit"
                                                href="javascript:void(0)"
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                data-slug="{{ $category->slug }}"
                                                data-type="{{ $category->type }}"
                                                data-description="{{ $category->description }}">
                                                <i class='bx bx-edit fs-5 me-3 text-muted'></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        @endcan


                                        <li>
                                            <hr class="dropdown-divider my-1 border-light">
                                        </li>
                                        @can('delete', $category)
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger delete-btn"
                                                href="javascript:void(0)"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                title="Xóa danh mục">
                                                <i class='bx bx-trash fs-5 me-3'></i> Xóa danh mục
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
                                        <i class='bx bx-folder-open fs-1 text-body opacity-50'></i>
                                    </div>
                                    <h6 class="text-body fw-normal mb-0">Chưa có danh mục nào.</h6>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($categories->hasPages())
        <div class="card-footer bg-card border-top py-3 px-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-body small">
                    Hiển thị <span class="fw-bold text-body">{{ $categories->firstItem() }}</span> - <span class="fw-bold text-body">{{ $categories->lastItem() }}</span>
                    trong tổng số <span class="fw-bold text-body">{{ $categories->total() }}</span> danh mục
                </div>
                <div>
                    {{ $categories->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@include('admin.categories.modal-create')
@include('admin.categories.modal-edit')
@include('admin.categories.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/categories.css') }}">
@endpush
@push('scripts')
<script>
    window.laravelConfig = {
        hasErrors: @json($errors->any()),           
        formMode: "{{ old('form_mode') }}",    
        editId: "{{ old('id') }}"                  
    };
</script>
<script src="{{ asset('assets/admin/js/categories.js') }}?v={{ time() }}"></script>
@endpush