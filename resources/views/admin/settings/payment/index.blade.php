@extends('layouts.admin')

@section('title', 'Phương thức thanh toán')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Phương thức thanh toán</h3>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4" style="border-radius: 16px;">
        <div class="card-body p-0">
            <div class="table-responsive rounded-4">
                <table class="table modern-table align-middle mb-0 table-hover custom-table">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 text-body small fw-bold text-uppercase">Tên phương thức</th>
                            <th class="py-3 text-body small fw-bold text-uppercase">Mã (Code)</th>
                            <th class="py-3 text-body small fw-bold text-uppercase">Mô tả</th>
                            <th class="py-3 text-body small fw-bold text-uppercase text-center">Trạng thái</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($methods as $method)
                        <tr class="transition-all hover-bg-light">
                            <td>
                                <span class="fw-bold text-body">{{ $method->name }}</span>
                            </td>

                            <td>
                                <span class="badge bg-light-info rounded-pill px-3 py-2 text-info border border-0 fw-normal font-monospace">
                                    {{ $method->code }}
                                </span>
                            </td>

                            <td>
                                <span class="text-body small text-truncate d-inline-block" style="max-width: 250px;">
                                    {{ $method->description ?? 'Không có mô tả' }}
                                </span>
                            </td>

                            <td class="text-center">
                                @if($method->is_active)
                                <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 fw-normal font-monospace">
                                    Bật
                                </span>
                                @else
                                <span class="badge bg-light-secondary text-secondary border border-secondary border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 fw-normal font-monospace">
                                    Tắt
                                </span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                        <i class='bx bx-credit-card-front fs-1 text-body opacity-50'></i>
                                    </div>
                                    <h6 class="text-body fw-normal mb-0">Chưa có phương thức thanh toán nào.</h6>
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

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light-danger text-danger" style="width: 64px; height: 64px; font-size: 32px;">
                    <i class='bx bx-trash'></i>
                </div>
                <h5 class="mb-2 fw-bold text-body">Xóa phương thức này?</h5>
                <p class="text-body small mb-4">
                    Khách hàng sẽ không thể chọn phương thức <strong id="modalMethodName" class="text-body">...</strong> này nữa.
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
<link rel="stylesheet" href="{{ asset('assets/admin/css/paymentmethods.css') }}">
@endpush
@section('scripts')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const name = button.getAttribute('data-name');

                document.getElementById('modalMethodName').textContent = name;
                document.getElementById('deleteForm').action = `/admin/settings/payment/${id}`;
            });
        }
    });
</script>
@endsection