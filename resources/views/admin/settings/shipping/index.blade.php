@extends('layouts.admin')

@section('title', 'Cấu hình phí vận chuyển')

@section('content')
<div class="container-fluid px-0">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Phí Vận Chuyển</h3>
        </div>
        
        @can('create', App\Models\ShippingRate::class)
        <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal" data-bs-target="#createShippingModal">
            <i class='bx bx-plus fs-5'></i> <span class="fw-semibold">Thêm mới</span>
        </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive rounded-4" style="min-height: 400px;">
                <table class="table modern-table align-middle mb-0 table-hover custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-body small fw-bold text-uppercase">Tên hiển thị</th>
                            <th class="py-3 text-body small fw-bold text-uppercase text-center">Phí Ship</th>
                            <th class="py-3 text-body small fw-bold text-uppercase">Điều kiện</th>
                            <th class="py-3 text-body small fw-bold text-uppercase text-center">Thời gian (Ước tính)</th>
                            <th class="py-3 text-body small fw-bold text-uppercase text-center">Trạng thái</th>
                            <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase" style="width: 120px;">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rates as $rate)
                        <tr class="transition-all hover-bg-light">
                            <td class="ps-4">
                                <span class="fw-bold text-body">{{ $rate->name }}</span>
                            </td>
                            
                            <td class="text-center">
                                @if($rate->fee == 0)
                                    <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace">Miễn phí</span>
                                @else
                                    <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace">{{ number_format($rate->fee, 0, ',', '.') }}₫</span>
                                @endif
                            </td>

                            <td>
                                @if($rate->min_order_value > 0)
                                    <div class="d-flex align-items-center text-body small">
                                        Đơn từ: 
                                        <span class="fw-bold text-body ms-1">{{ number_format($rate->min_order_value, 0, ',', '.') }}₫</span>
                                    </div>
                                @else
                                    <span class="text-body small fst-italic">Áp dụng cho mọi đơn</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <span class="badge bg-light text-body border rounded-pill px-3 py-2 fw-normal font-monospace">
                                    ~{{ $rate->estimated_days }} ngày
                                </span>
                            </td>

                            <td class="text-center">
                                @if($rate->is_active)
                                    <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 fw-normal font-monospace">
                                        Hoạt động
                                    </span>
                                @else
                                    <span class="badge bg-light-secondary text-body border rounded-pill px-3 py-2 fw-normal font-monospace">
                                        <i class='bx bx-x-circle'></i> Đã tắt
                                    </span>
                                @endif
                            </td>

                            <td class="pe-4 text-center">
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-light border-0 text-body" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card" style="border-radius: 12px; min-width: 180px;">
                                        @can('update', $rate)
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-shipping" 
                                               href="javascript:void(0)"
                                               data-id="{{ $rate->id }}"
                                               data-name="{{ $rate->name }}"
                                               data-fee="{{ (int)$rate->fee }}"
                                               data-min-order="{{ (int)$rate->min_order_value }}"
                                               data-estimated-days="{{ $rate->estimated_days }}"
                                               data-is-active="{{ $rate->is_active ? 'true' : 'false' }}">
                                                <i class='bx bx-edit-alt fs-5 me-3 text-body'></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        @endcan

                                        <li><hr class="dropdown-divider my-1"></li>

                                        @can('delete', $rate)
                                        <li>
                                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger" 
                                               href="javascript:void(0)"
                                               data-bs-toggle="modal" 
                                               data-bs-target="#deleteModal"
                                               data-id="{{ $rate->id }}"
                                               data-name="{{ $rate->name }}">
                                                <i class='bx bx-trash fs-5 me-3'></i> Xóa
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
                                        <i class='bx bx-map-pin fs-1 text-body opacity-50'></i>
                                    </div>
                                    <h6 class="text-body fw-normal mb-0">Chưa có cấu hình phí vận chuyển nào.</h6>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@include('admin.settings.shipping.modal-create')
@include('admin.settings.shipping.modal-edit')
@include('admin.settings.shipping.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/shipping.css') }}">
@endpush

@section('scripts')
<script>
    function toggleStatusLabel(checkbox, labelId) {
        const label = document.getElementById(labelId);
        if (label) {
            if (checkbox.checked) {
                label.textContent = 'Hoạt động';
                label.className = 'form-check-label small fw-bold text-success';
            } else {
                label.textContent = 'Đã tắt';
                label.className = 'form-check-label small fw-bold text-danger';
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Logic Modal Xóa
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');
                document.getElementById('modalRateName').textContent = name;
                document.getElementById('deleteForm').action = `/admin/settings/shipping/${id}`;
            });
        }

        // Logic Modal Sửa (Đổ dữ liệu)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-edit-shipping');
            if (btn) {
                e.preventDefault();
                
                // Lấy data
                const id = btn.dataset.id;
                const name = btn.dataset.name;
                const fee = btn.dataset.fee;
                const minOrder = btn.dataset.minOrder;
                const estimatedDays = btn.dataset.estimatedDays;
                const isActive = btn.dataset.isActive === 'true';

                // Đổ vào form
                document.getElementById('edit_name').value = name;
                document.getElementById('edit_fee').value = fee;
                document.getElementById('edit_min_order').value = minOrder;
                document.getElementById('edit_estimated_days').value = estimatedDays;
                
                // Switch Status
                const switchActive = document.getElementById('edit_is_active');
                switchActive.checked = isActive;
                toggleStatusLabel(switchActive, 'edit_status_label'); // Kích hoạt đổi label ngay

                // Update Action URL
                document.getElementById('editShippingForm').action = `/admin/settings/shipping/${id}`;

                // Mở Modal
                new bootstrap.Modal(document.getElementById('editShippingModal')).show();
            }
        });

        @if($errors->any())
            @if(old('_method') == 'PUT')
                new bootstrap.Modal(document.getElementById('editShippingModal')).show();
            @else
                new bootstrap.Modal(document.getElementById('createShippingModal')).show();
            @endif
        @endif
    });
</script>
@endsection