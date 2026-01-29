@extends('layouts.admin')

@section('title', 'Quản lý thuộc tính')

@section('content')
<div class="container-fluid px-0 min-vh-100">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">
                Quản lý thuộc tính
            </h3>
        </div>

        @can('create', App\Models\Attribute::class)
        <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createModal">
            <i class='bx bx-plus fs-5'></i> <span class="fw-bold">Thêm mới</span>
        </button>
        @endcan
    </div>
<div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('admin.products.index') }}">
                <div class="row g-3 align-items-center">
                    <div class="col-12 col-md-5">
                        <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                            <span class="input-group-text bg-transparent border-0 pe-2 text-muted"><i class='bx bx-search'></i></span>
                            <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                                placeholder="Tìm kiếm theo tên, SKU.."
                                value="{{ request('keyword') }}">
                        </div>
                    </div>

                    <div class="col-12 col-md-1 text-end">
                        @if(request()->anyFilled(['keyword', 'sort', 'status']))
                        <a href="{{ route('admin.products.index') }}"
                            class="btn btn-icon rounded-pill btn-light text-danger bg-light-danger border-1 d-flex align-items-center justify-content-center p-2"
                            data-bs-toggle="tooltip" title="Xóa bộ lọc">
                            <i class='bx bx-x fs-6'></i>
                        </a>
                        @endif
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
                            <th class="ps-4 py-3 text-body small fw-bold text-uppercase" style="width: 60px;">ID</th>
                            <th class="py-3 text-body small fw-bold text-uppercase" style="width: 250px;">Tên thuộc tính</th>
                            <th class="py-3 text-body small fw-bold text-uppercase">Các giá trị (Values)</th>
                            <th class="pe-4 py-3 text-end text-body small fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attributes as $attribute)
                        <tr class="transition-all hover-bg-light">
                            <td class="ps-4 text-body font-monospace">#{{ $attribute->id }}</td>

                            <td>
                                <span class="fw-bold text-body fs-6">{{ $attribute->name }}</span>
                                <div class="small text-body font-monospace mt-1">{{ $attribute->slug }}</div>
                            </td>

                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                    $valuesJson = json_encode($attribute->values);
                                    @endphp

                                    @if($attribute->values->count() > 0)
                                    @foreach($attribute->values->take(5) as $val)
                                    <span class="badge bg-light-info text-info border border-info border-opacity-10 rounded-pill px-3 py-2 fw-normal">
                                        {{ $val->value }}
                                    </span>
                                    @endforeach
                                    @if($attribute->values->count() > 5)
                                    <span class="badge bg-light text-body border rounded-pill px-2">
                                        +{{ $attribute->values->count() - 5 }}
                                    </span>
                                    @endif
                                    @else
                                    <span class="text-body small fst-italic">Chưa có giá trị</span>
                                    @endif
                                </div>
                            </td>

                            <td class="pe-4 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-light border-0 text-muted" type="button"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card"
                                        style="border-radius: 12px; min-width: 200px; z-index: 1050;">

                                        @can('update', $attribute)
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit"
                                                href="javascript:void(0)"
                                                data-id="{{ $attribute->id }}"
                                                data-name="{{ $attribute->name }}"
                                                data-slug="{{ $attribute->slug }}"
                                                data-values="{{ $valuesJson }}"
                                                title="Sửa & Quản lý giá trị">
                                                <i class='bx bx-edit-alt fs-5 me-3 text-muted'></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        @endcan

                                        <li>
                                            <hr class="dropdown-divider my-1 border-light"> 
                                        </li>

                                        @can('delete', $attribute)
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                                href="javascript:void(0)"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal"
                                                data-id="{{ $attribute->id }}"
                                                data-name="{{ $attribute->name }}"
                                                title="Xóa thuộc tính">
                                                <i class='bx bx-trash fs-5 me-3'></i> Xóa thuộc tính
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class='bx bx-layer fs-1 text-body opacity-50'></i>
                                    </div>
                                    <h6 class="text-body fw-normal mb-0">Chưa có thuộc tính nào.</h6>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($attributes->hasPages())
        <div class="card-footer bg-card border-top py-3 px-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <div class="text-body small">
                    Hiển thị <span class="fw-bold text-body">{{ $attributes->firstItem() }}</span> - <span class="fw-bold text-body">{{ $attributes->lastItem() }}</span>
                    trong tổng số <span class="fw-bold text-body">{{ $attributes->total() }}</span> thuộc tính
                </div>
                <div>
                    {{ $attributes->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@include('admin.attributes.modal-create')
@include('admin.attributes.modal-edit')
@include('admin.attributes.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/attributes.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/attributes.js') }}"></script>
@endpush