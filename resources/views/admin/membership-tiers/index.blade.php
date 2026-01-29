@extends('layouts.admin')

@section('title', 'Quản lý Hạng Thành Viên')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Quản lý Hạng thành viên</h3>
        </div>

        @can('create', App\Models\MembershipTier::class)
        <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createTierModal">
            <i class='bx bx-plus fs-5'></i> <span class="fw-semibold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="card-header bg-card border-bottom p-4 mb-4 rounded-4 shadow-sm">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h5 class="fw-bold text-body mb-0">Danh sách hạng thành viên</h5>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive bg-card rounded-4 shadow-sm">
            <table class="table modern-table align-middle mb-0 table-hover custom-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase text-center" style="width: 80px;">Priority</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Tên Hạng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Ưu đãi</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Điều kiện đạt hạng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase text-center">Hiệu lực</th>
                        <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tiers as $tier)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4 text-center">
                            <span class="badge bg-light text-body border rounded-pill px-2">{{ $tier->rank_priority }}</span>
                        </td>

                        <td>
                            @php
                            $badge = [
                            'label' => $tier->name,
                            'bg' => $tier->color_hex,
                            ];
                            @endphp
                            <span
                                class="badge rounded-pill px-3 py-2 d-inline-flex align-items-center gap-2 fw-normal font-monospace text-white"
                                style=" background-color: {{ $badge['bg'] }};border: 1px solid rgba(0,0,0,0.05);">
                                {{ $badge['label'] }}
                            </span>
                        </td>

                        <td class="text-center">
                            <div class="d-flex flex-column align-items-center gap-1">
                                @if($tier->discount > 0)
                                <span class="badge bg-light-success text-success border border-0 rounded-pill px-3 py-2 fw-normal font-monospace">
                                    Giảm {{ $tier->discount }}%
                                </span>
                                @else
                                <span class="badge bg-light-secondary border rounded-pill fw-normal text-secondary px-3 py-2 font-monospace">Không giảm giá</span>
                                @endif

                                @if($tier->usage_limit > 0)
                                <span class="text-body small font-monospace">
                                    (Max {{ $tier->usage_limit }} lần/{{ $tier->usage_period }})
                                </span>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            <div class="small">
                                <div class="fw-bold text-body mb-1">
                                    Chi tiêu: {{ number_format($tier->min_spent, 0, ',', '.') }}₫
                                </div>
                                @if($tier->min_orders > 0)
                                <div class="text-body">
                                    và {{ $tier->min_orders }} đơn hàng
                                </div>
                                @endif
                            </div>
                        </td>

                        <td class="text-center">
                            @if($tier->validity_days)
                            <span class="badge bg-light-warning text-warning border border-0 rounded-pill px-3 py-2 fw-normal font-monospace">
                                {{ $tier->validity_days }} ngày
                            </span>
                            @else
                            <span class="badge bg-light-info text-info border border-0 rounded-pill px-3 py-2 fw-normal font-monospace">
                                Vĩnh viễn
                            </span>
                            @endif
                        </td>

                        <td class="pe-4 text-center">
                            <div class="dropdown">
                                <button class="btn btn-icon btn-light border-0 text-body" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card"
                                    style="border-radius: 12px; min-width: 200px; z-index: 1050;">
                                    @can('update', $tier)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-tier"
                                            href="javascript:void(0)"
                                            data-id="{{ $tier->id }}"
                                            data-name="{{ $tier->name }}"
                                            data-discount="{{ $tier->discount }}"
                                            data-usage-limit="{{ $tier->usage_limit }}"
                                            data-usage-period="{{ $tier->usage_period }}"
                                            data-validity-days="{{ $tier->validity_days }}"
                                            data-rank-priority="{{ $tier->rank_priority }}"
                                            data-color-hex="{{ $tier->color_hex }}"
                                            data-min-spent="{{ $tier->min_spent }}"
                                            data-min-orders="{{ $tier->min_orders }}">
                                            <i class='bx bx-edit-alt fs-5 me-3 text-body'></i> Chỉnh sửa
                                        </a>
                                    </li>
                                    @endcan

                                    <li>
                                        <hr class="dropdown-divider my-1">
                                    </li>

                                    @can('delete', $tier)
                                    <li>
                                        <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                            href="javascript:void(0)"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{ $tier->id }}"
                                            data-name="{{ $tier->name }}">
                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa hạng
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
                                    <i class='bx bx-crown fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có hạng thành viên nào.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light-danger text-danger" style="width: 64px; height: 64px; font-size: 32px;">
                    <i class='bx bx-trash'></i>
                </div>
                <h5 class="mb-2 fw-bold text-body">Xóa hạng này?</h5>
                <p class="text-body small mb-4">
                    Hành động này sẽ ảnh hưởng đến việc xếp hạng của khách hàng.<br>
                    Hạng <strong id="modalTierName" class="text-body">...</strong> sẽ bị xóa vĩnh viễn.
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

@include('admin.membership-tiers.modal-create')
@include('admin.membership-tiers.modal-edit')

@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/membership.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/memberships.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if($errors -> any())
        @if(old('_method') == 'PUT')
        new bootstrap.Modal(document.getElementById('editTierModal')).show();
        @else
        new bootstrap.Modal(document.getElementById('createTierModal')).show();
        @endif
        @endif
    });
</script>
@endpush