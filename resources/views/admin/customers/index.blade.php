@extends('layouts.admin')
@section('title', 'Quản lý khách hàng')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Quản lý khách hàng</h3>
        </div>

        @can('create', App\Models\User::class)
        <button type="button"
            class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#createCustomerModal">
            <i class='bx bx-plus fs-5'></i> <span class="fw-bold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-group'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng khách hàng</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($customers->total()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-user-check'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Hoạt động</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($activeCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-user-x'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Bị chặn</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($blockedCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-header bg-card border-bottom p-3 mb-4 rounded-4 shadow-sm">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <div class="col-12 col-md-3">
                <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring flex-grow-1 flex-md-grow-0" style="min-width: 200px; max-width: 300px;">
                    <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                    <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                        placeholder="Tên, email, số điện thoại..." value="{{ request('keyword') }}">
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">

                @if(request()->anyFilled(['keyword', 'status', 'gender', 'tier_id', 'sort']))
                <a href="{{ route('admin.customers.index') }}"
                    class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0 order-last order-xl-first ms-auto ms-xl-0"
                    data-bs-toggle="tooltip" title="Xóa bộ lọc"
                    style="width: 36px; height: 36px;">
                    <i class='bx bx-refresh fs-5'></i>
                </a>
                @endif

                <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto shadow-sm" style="max-width: 100%; white-space: nowrap;">

                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Tất cả
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'active', 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'active' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Hoạt động
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'locked', 'page' => 1]) }}"
                        class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'locked' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                        Bị khóa
                    </a>
                </div>

                <div class="vr d-none d-lg-block mx-1 text-muted opacity-25" style="height: 40px;"></div>

                <div class="d-flex align-items-center gap-2">

                    <select name="gender" class="form-select form-select-sm form-control rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 px-3"
                        style="min-width: 100px;" onchange="this.form.submit()">
                        <option value="">Giới tính</option>
                        <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                        <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                        <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                    </select>

                    <select name="tier_id" class="form-select form-select-sm form-control rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 px-3"
                        style="min-width: 110px;" onchange="this.form.submit()">
                        <option value="">Hạng TV</option>
                        @foreach($tiers as $tier)
                        <option value="{{ $tier->id }}" {{ request('tier_id') == $tier->id ? 'selected' : '' }}>
                            {{ $tier->name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="sort" class="form-select form-select-sm form-control rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 px-3"
                        style="min-width: 110px;" onchange="this.form.submit()">
                        <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Tên A-Z</option>
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
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase" style="width: 60px;">ID</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Khách hàng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Liên hệ</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Giới tính</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Hạng TV</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    @php $profile = $customer->customerProfile; @endphp
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4 text-body font-monospace">#{{ $customer->id }}</td>

                        <td>
                            <div class="d-flex align-items-center">
                                <div class="d-flex flex-column">
                                    <span class="fw-bold text-body">{{ $profile->full_name ?? $customer->name }}</span>
                                    <small class="text-body" style="font-size: 11px;">Tham gia: {{ $customer->created_at->format('d/m/Y') }}</small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column small">
                                <div class="d-flex align-items-center mb-1">
                                    <i class='bx bx-envelope text-body me-2'></i>
                                    <span class="text-body">{{ $customer->email }}</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class='bx bx-phone text-body me-2'></i>
                                    <span class="text-body">{{ $profile->phone ?? '---' }}</span>
                                </div>
                            </div>
                        </td>

                        @php
                        $gender = [
                        'male' => [
                        'bg' => 'bg-light-info',
                        'text' => 'text-info',
                        'label' => 'Nam',
                        'color' => 'info',
                        ],
                        'female' => [
                        'bg' => 'bg-light-danger',
                        'text' => 'text-danger',
                        'label' => 'Nữ',
                        'color' => 'danger',
                        ],
                        'other' => [
                        'bg' => 'bg-light-secondary',
                        'text' => 'text-secondary',
                        'label' => 'Khác',
                        'color' => 'secondary',
                        ],
                        ][$profile->gender ?? 'other'];
                        @endphp

                        <td class="text-center">
                            <span class="badge {{ $gender['bg'] }} {{ $gender['text'] }}
        rounded-pill px-3 py-2 border border-{{ $gender['color'] }} border-opacity-10
        fw-normal font-monospace">
                                {{ $gender['label'] }}
                            </span>
                        </td>


                        <td class="text-center">
                            @php
                            $tier = $profile->tier ?? null;
                            $tierName = $tier->display_name ?? ($tier->name ?? 'Member');
                            $tierColor = $tier->color_hex ?? '#6c757d';
                            @endphp
                            <span class="badge rounded-pill border px-3 py-2 fw-normal font-monospace"
                                style="background-color: {{ $tierColor }}20; color: {{ $tierColor }}; border-color: {{ $tierColor }}40 !important;">
                                {{ $tierName }}
                            </span>
                        </td>

                        <td class="text-center">
                            @php
                            $st = ['active' => ['bg' => 'bg-light-success', 'text' => 'text-success',
                            'label' => 'Hoạt động',
                            'color' => 'success',
                            ],
                            'blocked' => [
                            'bg' => 'bg-light-danger',
                            'text' => 'text-danger',
                            'label' => 'Bị khóa',
                            'color' => 'danger',
                            ],
                            ][$profile->status ?? 'active'] ?? [
                            'bg' => 'bg-light-secondary',
                            'text' => 'text-secondary',
                            'label' => 'Không xác định',
                            'color' => 'secondary',
                            ];
                            @endphp

                            <span class="badge {{ $st['bg'] }} {{ $st['text'] }}
        rounded-pill px-3 py-2 border border-{{ $st['color'] }} border-opacity-10
        fw-normal font-monospace d-inline-flex align-items-center gap-1">
                                {{ $st['label'] }}
                            </span>
                        </td>


                        <td class="pe-4 text-center">
                            @if($customer->id !== Auth::id())
                            <div class="dropdown">
                                <button class="btn btn-icon btn-light border-0 text-muted" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card"
                                    style="border-radius: 12px; min-width: 200px; z-index: 1050;">

                                    @can('update', $customer)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-customer"
                                            href="javascript:void(0)"
                                            data-id="{{ $customer->id }}"
                                            data-email="{{ $customer->email }}"
                                            data-fullname="{{ $profile->full_name ?? $customer->name }}"
                                            data-phone="{{ $profile->phone ?? '' }}"
                                            data-gender="{{ $profile->gender ?? 'other' }}"
                                            data-address="{{ $profile->address ?? '' }}"
                                            data-tier-id="{{ $profile->membership_tier_id ?? '' }}"
                                            data-status="{{ $profile->status ?? 'active' }}"

                                            title="Chỉnh sửa thông tin">
                                            <i class='bx bx-edit-alt fs-5 me-3 text-muted'></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endcan

                                    <li>
                                        <hr class="dropdown-divider my-1 border-light">
                                    </li>

                                    @can('delete', $customer)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-customer-id="{{ $customer->id }}"
                                            data-customer-name="{{ $profile->full_name ?? $customer->name }}">
                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa khách hàng
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                            @else
                            <span class="badge bg-light text-body border fst-italic px-3 py-2 rounded-pill">Bạn</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bx-user-x fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Không tìm thấy khách hàng nào.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($customers->hasPages())
    <div class="card-footer bg-card border-top py-3 px-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-body small">
                Hiển thị <span class="fw-bold text-body">{{ $customers->firstItem() }}</span> - <span class="fw-bold text-body">{{ $customers->lastItem() }}</span>
                trong tổng số <span class="fw-bold text-body">{{ $customers->total() }}</span> khách hàng
            </div>
            <div>
                {{ $customers->appends(request()->all())->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
    @endif
</div>


@include('admin.customers.modal-create')
@include('admin.customers.modal-edit')
<div class="modal fade " id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 mt-6 pt-5 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light-danger text-danger" style="width: 64px; height: 64px; font-size: 32px;">
                    <i class='bx bx-trash'></i>
                </div>
                <h5 class="mb-2 fw-bold text-body">Xóa khách hàng này?</h5>
                <p class="text-body small mb-4">
                    Hành động này không thể hoàn tác.<br>
                    Khách hàng <strong id="modalCustomerName" class="text-body">...</strong> sẽ bị xóa vĩnh viễn.
                </p>

                <form id="deleteForm" method="POST" action="" class="d-grid gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold py-2">Xác nhận xóa</button>
                    <button type="button" class="btn btn-light text-body rounded-pill fw-bold py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/customers.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/customers.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors -> any())
        @if(old('_method') == 'PUT')
        const reOpenEditModal = document.getElementById('editCustomerModal');
        if (reOpenEditModal) {
            const switchEdit = document.getElementById('edit_status');
            if (switchEdit && typeof updateStatusLabel === 'function') {
                updateStatusLabel(switchEdit.checked);
            }
            const modalObj = new bootstrap.Modal(reOpenEditModal);
            modalObj.show();
        }
        @else
        const reOpenCreateModal = document.getElementById('createCustomerModal');
        if (reOpenCreateModal) {
            const modalObj = new bootstrap.Modal(reOpenCreateModal);
            modalObj.show();
        }
        @endif
        @endif
    });
</script>
@endpush