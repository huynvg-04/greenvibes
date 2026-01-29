@extends('layouts.admin')
@section('title', 'Quản lý yêu cầu hoàn hàng')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Yêu cầu hoàn hàng</h3>
        </div>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-layer'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng yêu cầu</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['total']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-time'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Chờ xử lý</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['pending']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bx-check'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đã chấp nhận</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['approved']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bx-x'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đã từ chối</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['rejected']) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="card-header bg-card p-3 mb-4 rounded-4 shadow-sm">

        <form action="{{ route('admin.returns.index') }}" method="GET">
            @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div style="min-width: 200px;" class="flex-grow-1 flex-md-grow-0">
                    <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                        <span class="input-group-text bg-transparent border-0 pe-2 text-body">
                            <i class='bx bx-search'></i>
                        </span>
                        <input type="text" name="keyword"
                            class="form-control border-0 bg-transparent shadow-none text-body small"
                            placeholder="Tìm kiếm..."
                            value="{{ request('keyword') }}">
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">

                    @if(request()->anyFilled(['keyword', 'status', 'month', 'year']))
                    <a href="{{ route('admin.returns.index') }}"
                        class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0"
                        data-bs-toggle="tooltip" title="Xóa bộ lọc"
                        style="width: 36px; height: 36px;">
                        <i class='bx bx-refresh fs-5'></i>
                    </a>
                    @endif

                    <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto shadow-sm" style="max-width: 100%; white-space: nowrap;">
                        <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}"
                            class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                            Tất cả
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => 1]) }}"
                            class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'pending' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}"
                            data-bs-toggle="tooltip" title="Chờ xử lý">
                            <i class="bx bx-time fs-6"></i>
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['status' => 'approved', 'page' => 1]) }}"
                            class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'approved' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}"
                            data-bs-toggle="tooltip" title="Đã duyệt">
                            <i class="bx bx-check fs-6"></i>
                        </a>

                        <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected', 'page' => 1]) }}"
                            class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'rejected' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}"
                            data-bs-toggle="tooltip" title="Từ chối">
                            <i class="bx bx-x fs-6"></i>
                        </a>
                    </div>

                    <div class="vr d-none d-md-block mx-1 text-muted opacity-25" style="height: 40px;"></div>

                    <div class="d-flex align-items-center gap-2">

                        <div class="position-relative">


                            <select name="month"
                                class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 px-3 pe-4"
                                onchange="this.form.submit()">
                                <option value="">Tháng</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                    @endfor
                            </select>
                        </div>

                        <div class="position-relative">
                            <select name="year"
                                class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 pe-5 w-auto"

                                onchange="this.form.submit()">
                                @foreach ($years as $y)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive rounded-4 shadow-sm mb-4 bg-card" style="min-height: 400px;">
            <table class="table modern-table align-middle mb-0 table-hover custom-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase" style="width: 80px;">ID</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Đơn hàng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Khách hàng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Lý do</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Ngày tạo</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Trạng thái</th>
                        <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($returns as $return)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4 text-body font-monospace">#{{ $return->id }}</td>
                        
                        <td>
                            @if($return->order)
                            <a href="{{ route('admin.orders.show', $return->order->id) }}" class="fw-bold text-body text-decoration-none d-flex align-items-center gap-1">
                                #{{ $return->order->code }}
                            </a>
                            @else
                            <span class="badge bg-light-danger text-danger">Đơn đã xóa</span>
                            @endif
                        </td>

                        <td>
                            @if($return->user)
                            <div class="d-flex align-items-center gap-2">
                                <div class="d-flex flex-column">
                                    <span class="fw-semibold text-body small">{{ $return->user->name }}</span>
                                    <small class="text-body" style="font-size: 10px;">{{ $return->user->email }}</small>
                                </div>
                            </div>
                            @else
                            <span class="text-body small fst-italic">Khách vãng lai</span>
                            @endif
                        </td>

                        <td>
                            <span class="d-inline-block text-truncate text-body small" style="max-width: 200px;" title="{{ $return->reason }}">
                                {{ $return->reason }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-body small fw-medium">{{ $return->created_at->format('d/m/Y') }}</span>
                                <span class="text-body" style="font-size: 10px;">{{ $return->created_at->format('H:i') }}</span>
                            </div>
                        </td>

                        <td>
                            @php
                            $statusMap = [
                            'pending' => ['bg' => 'bg-light-warning', 'text' => 'text-warning', 'icon' => 'bxs-time', 'label' => 'Chờ xử lý', 'color' => 'warning'],
                            'approved' => ['bg' => 'bg-light-success', 'text' => 'text-success', 'icon' => 'bxs-check-circle', 'label' => 'Đã duyệt', 'color' => 'success'],
                            'rejected' => ['bg' => 'bg-light-danger', 'text' => 'text-danger', 'icon' => 'bxs-x-circle', 'label' => 'Từ chối', 'color' => 'danger'],
                            ];
                            $status = $statusMap[$return->status] ?? ['bg' => 'bg-light', 'text' => 'text-body', 'icon' => 'bxs-help-circle', 'label' => $return->status];
                            @endphp
                            <span class="badge {{ $status['bg'] }} {{ $status['text'] }} rounded-pill px-3 py-2 border border-{{$status['color']}} border-opacity-10 d-inline-flex align-items-center gap-1 fw-normal font-monospace">
                                {{ $status['label'] }}
                            </span>
                        </td>

                        <td class="pe-4 text-center">
                            @can('view', $return)
                            <a href="{{ route('admin.returns.show', $return->id) }}"
                                class="btn btn-icon btn-light-primary rounded-circle shadow-sm"
                                data-bs-toggle="tooltip" title="Xử lý yêu cầu">
                                <i class='bx bxs-show'></i>
                            </a>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bx-package fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có yêu cầu nào.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($returns->hasPages())
    <div class="card-footer bg-card py-3 px-4 shadow-sm rounded-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-body small">
                Từ <span class="fw-bold text-body">{{ $returns->firstItem() }}</span>
                đến <span class="fw-bold text-body">{{ $returns->lastItem() }}</span>
                trong tổng số <span class="fw-bold text-body">{{ $returns->total() }}</span> yêu cầu
            </div>
            <div>
                {{ $returns->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/returns.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/returns.js') }}?v={{ time() }}"></script>
@endpush