@extends('layouts.admin')

@section('title', 'Quản lý mã giảm giá')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Mã giảm giá</h3>
        </div>

        @can('create', App\Models\Coupon::class)
        <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createCouponModal">
            <i class='bx bx-plus fs-5'></i> <span class="fw-semibold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light-primary primary-color me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bxs-purchase-tag-alt'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng mã giảm giá</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($totalCoupons ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bx-check'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đang hoạt động</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($activeCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bx-x'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Hết hạn / Hết lượt</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($expiredCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-header bg-card border-bottom p-3 mb-4 rounded-4 shadow-sm">
        <form action="{{ route('admin.coupons.index') }}" method="GET">
            <div class="row g-3 align-items-center">
                <div class="col-12 col-md-4">
                    <div class="input-group bg-light rounded-pill px-3 py-1 border border-light">
                        <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>
                        <input type="text" name="keyword" class="form-control border-0 bg-transparent shadow-none text-body small"
                            placeholder="Tìm mã code..." value="{{ request('keyword') }}">
                    </div>
                </div>
                <div class="col-12 col-md-8 d-flex flex-wrap gap-3 justify-content-md-end align-items-center">
                    @if(request()->anyFilled(['keyword', 'type', 'status']))
                    <a href="{{ route('admin.coupons.index') }}"
                        class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger"
                        data-bs-toggle="tooltip" title="Xóa bộ lọc"
                        style="width: 34px; height: 34px;">
                        <i class='bx bx-refresh fs-5'></i>
                    </a>
                    @endif
                    <form action="{{ route('admin.coupons.index') }}" method="GET" class="d-flex align-items-center m-0">
                        @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                        @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    </form>

                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold text-uppercase d-none d-lg-block">Loại:</span>
                        <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center shadow-sm">

                            <a href="{{ request()->fullUrlWithQuery(['type' => null, 'page' => 1]) }}"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('type') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Tất cả
                            </a>

                            <a href="{{ request()->fullUrlWithQuery(['type' => 'percent', 'page' => 1]) }}"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('type') == 'percent' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                %
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'fixed', 'page' => 1]) }}"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('type') == 'fixed' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                $
                            </a>
                        </div>
                    </div>
                    <div class="vr d-none d-lg-block mx-1 text-muted opacity-25" style="height: 40px;"></div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="text-muted small fw-bold text-uppercase d-none d-lg-block">Trạng thái:</span>
                        <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center shadow-sm">

                            <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Tất cả
                            </a>

                            <a href="{{ request()->fullUrlWithQuery(['status' => 'active', 'page' => 1]) }}"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'active' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Hoạt động
                            </a>

                            <a href="{{ request()->fullUrlWithQuery(['status' => 'expired', 'page' => 1]) }}"
                                class="btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'expired' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Hết hạn
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0 rounded-4">
        <div class="table-responsive bg-card rounded-4 shadow-sm" style="min-height: 220px;">
            <table class="table modern-table align-middle mb-0 table-hover custom-table border-bottom-0 rounded-4">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase">Mã Code</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Giá trị giảm</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Điều kiện</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Thời hạn</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Lượt dùng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Trạng thái</th>
                        <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4">
                            <span class="badge bg-light text-body border border-primary-color border-opacity-25 fs-6 font-monospace px-3 py-2">
                                {{ $coupon->code }}
                            </span>
                            @if($coupon->description)
                            <div class="small text-muted mt-1 text-truncate" style="max-width: 150px;">{{ $coupon->description }}</div>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($coupon->type == 'percent')
                            <div class="d-flex flex-column align-items-center">
                                <span class="fw-bold text-danger fs-6">{{ (int)$coupon->value }}%</span>
                                @if($coupon->max_discount_value > 0)
                                <span class="badge bg-light text-body border rounded-pill px-2 py-1 mt-1 fw-normal font-monospace" style="font-size: 10px;">
                                    Max: {{ number_format($coupon->max_discount_value/1000) }}k
                                </span>
                                @endif
                            </div>
                            @else
                            <span class="fw-bold text-success fs-6">{{ number_format($coupon->value, 0, ',', '.') }}₫</span>
                            @endif
                        </td>

                        <td>
                            <div class="small">
                                @if($coupon->min_order_value > 0)
                                <div class="text-body mb-1">Đơn tối thiểu:</div>
                                <div class="fw-bold text-body">{{ number_format($coupon->min_order_value, 0, ',', '.') }}₫</div>
                                @else
                                <span class="text-body fst-italic">Không yêu cầu</span>
                                @endif
                            </div>
                        </td>

                        <td>
                            <div class="d-flex flex-column small">
                                <div class="mb-1">
                                    <span class="text-muted">Từ:</span> <span class="text-body fw-medium">{{ $coupon->start_date ? $coupon->start_date->format('d/m/Y H:i') : '...' }}</span>
                                </div>
                                <div>
                                    <span class="text-muted">Đến:</span>
                                    @if($coupon->end_date)
                                    <span class="text-body fw-medium {{ $coupon->end_date->isPast() ? 'text-danger' : '' }}">
                                        {{ $coupon->end_date->format('d/m/Y H:i') }}
                                    </span>
                                    @else
                                    <span class="badge bg-light-success text-success border border-0 p-1" style="font-size: 10px;">Vĩnh viễn</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="d-flex align-items-center justify-content-center">
                                <span class="fw-bold primary-color">{{ $coupon->used_count }}</span>
                                <span class="text-body mx-1">/</span>
                                <span class="fw-bold text-body">{{ $coupon->usage_limit ?? '∞' }}</span>
                            </div>
                            @if($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit)
                            <div class="badge bg-danger rounded-pill mt-1" style="font-size: 9px;">Hết lượt</div>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($coupon->is_active)
                            @if($coupon->isValid())
                            <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace">
                               Hoạt động
                            </span>
                            @else
                            <span class="badge bg-light-secondary text-secondary border border-secondary border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace">
                                Hết hạn
                            </span>
                            @endif
                            @else
                            <span class="badge bg-light-danger text-danger border border-0 rounded-pill px-3 py-2 fw-normal font-monospace">
                                Đã tắt
                            </span>
                            @endif
                        </td>

                        <td class="pe-4 text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-light border-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 p-2 bg-card"
                                    style="border-radius: 12px; min-width: 200px; z-index: 1050;">
                                    @can('update', $coupon)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-coupon"
                                            href="javascript:void(0)"
                                            {{-- Data Attributes cho Edit Modal --}}
                                            data-id="{{ $coupon->id }}"
                                            data-code="{{ $coupon->code }}"
                                            data-description="{{ $coupon->description }}"
                                            data-type="{{ $coupon->type }}"
                                            data-value="{{ (int)$coupon->value }}"
                                            data-min-order="{{ (int)$coupon->min_order_value }}"
                                            data-max-discount="{{ $coupon->max_discount_value ? (int)$coupon->max_discount_value : '' }}"
                                            data-start-date="{{ $coupon->start_date ? $coupon->start_date->format('Y-m-d\TH:i') : '' }}"
                                            data-end-date="{{ $coupon->end_date ? $coupon->end_date->format('Y-m-d\TH:i') : '' }}"
                                            data-usage-limit="{{ $coupon->usage_limit }}"
                                            data-is-active="{{ $coupon->is_active ? 'active' : 'inactive' }}">
                                            <i class='bx bx-edit-alt fs-5 me-3 text-muted'></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endcan

                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>

                                    @can('delete', $coupon)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-coupon-id="{{ $coupon->id }}"
                                            data-coupon-code="{{ $coupon->code }}">
                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa mã
                                        </a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bxs-purchase-tag-alt fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có mã giảm giá nào.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($coupons->hasPages())
    <div class="card-footer bg-card border-top py-3 px-4 mt-4 rounded-4 shadow-sm">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-body small">
                Hiển thị <span class="fw-bold text-body">{{ $coupons->firstItem() }}</span> - <span class="fw-bold text-body">{{ $coupons->lastItem() }}</span>
                trong tổng số <span class="fw-bold text-body">{{ $coupons->total() }}</span> mã
            </div>
            <div>
                {{ $coupons->appends(request()->all())->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
    @endif
</div>

@include('admin.coupons.modal-create')
@include('admin.coupons.modal-edit')


<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light-danger text-danger" style="width: 64px; height: 64px; font-size: 32px;">
                    <i class='bx bx-trash'></i>
                </div>
                <h5 class="mb-2 fw-bold text-body">Xóa mã giảm giá?</h5>
                <p class="text-body small mb-4">
                    Hành động này không thể hoàn tác.<br>
                    Mã <strong id="modalCouponCode" class="text-body font-monospace">...</strong> sẽ bị xóa vĩnh viễn.
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
<link rel="stylesheet" href="{{ asset('assets/admin/css/coupons.css') }}">
@endpush
@push('scripts')
    <script>
        window.couponConfig = {
            hasErrors: @json($errors->any()),
            oldMethod: @json(old('_method'))
        };
    </script>
    <script src="{{ asset('assets/admin/js/coupons.js') }}"></script>
@endpush