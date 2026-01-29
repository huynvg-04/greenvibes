@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Quản lý đơn hàng</h3>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
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

        <div class="col-12 col-sm-6 col-lg">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-stamp'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đã xác nhận</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['confirmed']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-truck'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đang giao</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['shipping']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bx-check'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Hoàn thành</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['completed']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bx-x'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đã hủy</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($stats['cancelled']) }}</h4>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="card-header bg-card border-bottom p-3 mb-4 rounded-4 shadow-sm">

        <form method="GET" action="{{ route('admin.orders.index') }}" class="d-flex flex-wrap align-items-center justify-content-between gap-3">

            <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring flex-grow-1 flex-md-grow-0" style="min-width: 200px; max-width: 300px;">
                <span class="input-group-text bg-transparent border-0 pe-2 text-body"><i class='bx bx-search'></i></span>

                @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif

                <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none text-body small"
                    value="{{ request('search') }}" placeholder="Tìm đơn hàng...">
            </div>

            <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">

                @if(request()->anyFilled(['search', 'status', 'month', 'year']))
                <a href="{{ route('admin.orders.index') }}"
                    class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0 order-last order-xl-first ms-auto ms-xl-0"
                    data-bs-toggle="tooltip" title="Xóa bộ lọc"
                    style="width: 36px; height: 36px;">
                    <i class='bx bx-refresh fs-5'></i>
                </a>
                @endif

                <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto shadow-sm" style="max-width: 100%; white-space: nowrap;">

                    <a href="{{ request()->fullUrlWithQuery(['status' => null, 'page' => 1]) }}"
                        class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}" data-bs-toggle="tooltip" title="Tất cả">
                        Tất cả
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'pending', 'page' => 1]) }}"
                        class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'pending' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}" data-bs-toggle="tooltip" title="Chờ xử lý">
                        <i class="bx bx-time"></i>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'confirmed', 'page' => 1]) }}"
                        class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'confirmed' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}" data-bs-toggle="tooltip" title="Đã xác nhận">
                        <i class="bx bx-stamp"></i>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'shipping', 'page' => 1]) }}"
                        class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'shipping' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}" data-bs-toggle="tooltip" title="Đang giao hàng">
                        <i class="bx bx-truck"></i>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'completed', 'page' => 1]) }}"
                        class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'completed' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}" data-bs-toggle="tooltip" title="Hoàn thành">
                        <i class="bx bx-check"></i>
                    </a>

                    <a href="{{ request()->fullUrlWithQuery(['status' => 'cancelled', 'page' => 1]) }}"
                        class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('status') == 'cancelled' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}" data-bs-toggle="tooltip" title="Đã hủy">
                        <i class="bx bx-x"></i>
                    </a>
                </div>

                <div class="vr d-none d-xl-block mx-1 text-muted opacity-25" style="height: 40px;"></div>

                <div class="d-flex align-items-center gap-2">
                    <select name="month" class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 px-3"
                        style="width: auto; min-width: 90px;" onchange="this.form.submit()">
                        <option value="">Cả năm</option>
                        @for ($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                    </select>

                    <select name="year" class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 px-3"
                        style="width: auto; min-width: 85px;" onchange="this.form.submit()">
                        @foreach ($years as $y)
                        <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

            </div>
        </form>
    </div>
    @if($orders->count() > 0)
    <div class="table-responsive rounded-4 bg-card shadow-sm mb-4" style="min-height: 400px;">
        <table class="table modern-table align-middle mb-0 table-hover custom-table">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3 text-body small fw-bold text-uppercase">Mã đơn</th>
                    <th class="py-3 text-body small fw-bold text-uppercase">Khách hàng</th>
                    <th class="py-3 text-body small fw-bold text-uppercase">Tổng tiền</th>
                    <th class="py-3 text-body small fw-bold text-uppercase">Trạng thái</th>
                    <th class="text-center py-3 text-body small fw-bold text-uppercase">Phương thức TT</th>
                    <th class="py-3 text-body small fw-bold text-uppercase">Ngày đặt</th>
                    <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr class="transition-all hover-bg-light">
                    <td class="ps-4">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-body text-decoration-none font-monospace">
                            #{{ $order->code }}
                        </a>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-body small">{{ $order->user->name ?? 'Khách vãng lai' }}</span>
                                @if($order->user)<span class="text-body" style="font-size: 10px;">Thành viên</span>@endif
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="fw-bold text-success">{{ number_format($order->total_amount, 0, ',', '.') }}₫</span>
                    </td>
                    <td>
                        @php
                        $st = [
                        'pending' => ['bg' => 'bg-light-warning', 'text' => 'text-warning', 'icon' => 'bx-time', 'label' => 'Chờ xử lý', 'color' => 'warning'],
                        'confirmed' => ['bg' => 'bg-light-info', 'text' => 'text-info', 'icon' => 'bx-check', 'label' => 'Đã xác nhận', 'color' => 'info'],
                        'shipping' => ['bg' => 'bg-light-primary', 'text' => 'text-body', 'icon' => 'bx-truck', 'label' => 'Đang giao', 'color' => 'primary'],
                        'completed' => ['bg' => 'bg-light-success', 'text' => 'text-success', 'icon' => 'bx-check-double', 'label' => 'Hoàn thành', 'color' => 'success'],
                        'cancelled' => ['bg' => 'bg-light-danger', 'text' => 'text-danger', 'icon' => 'bx-x', 'label' => 'Đã hủy', 'color' => 'danger'],
                        ][$order->status] ?? ['bg' => 'bg-light', 'text' => 'text-body', 'icon' => 'bxs-question-mark', 'label' => $order->status];
                        @endphp
                        <span class="badge {{ $st['bg'] }} {{ $st['text'] }} rounded-pill px-3 py-2 border border-{{ $st['color'] }} border-opacity-10 d-inline-flex align-items-center gap-1 fw-normal font-monospace">
                            {{ $st['label'] }}
                        </span>
                    </td>
                    <td>
                        <div class="text-center">
                            <span class="badge bg-light-{{ $order->payment_method == 'cod' ? 'secondary' : 'primary' }} rounded-pill px-3 py-2 text-body border border-{{ $order->payment_method == 'cod' ? 'secondary' : 'primary' }} border-opacity-10 fw-normal font-monospace"> {{$order->payment_method}}</span>
                        </div>

                    </td>
                    <td>
                        <div class="d-flex flex-column">
                            <span class="text-body small fw-medium">{{ $order->created_at->format('d/m/Y') }}</span>
                            <span class="text-body" style="font-size: 10px;">{{ $order->created_at->format('H:i') }}</span>
                        </div>
                    </td>
                    <td class="pe-4 text-center">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-icon btn-light-primary rounded-circle shadow-sm" data-bs-toggle="tooltip" title="Xem chi tiết">
                            <i class='bx bxs-show'></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="text-center py-5">
        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
            <i class='bx bx-cart-alt fs-1 text-body opacity-50'></i>
        </div>
        <h5 class="fw-bold text-body">Không tìm thấy đơn hàng</h5>
        <p class="text-body small">Thử thay đổi bộ lọc hoặc kiểm tra lại sau.</p>
    </div>
    @endif

    <div class="card-footer py-3 px-4 bg-card shadow-sm rounded-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">

            <div class="d-flex flex-wrap align-items-center gap-3">
                <form method="GET" action="{{ url()->current() }}" class="d-flex align-items-center gap-2">
                    @foreach(request()->except(['page', 'per_page']) as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                    @endforeach

                    <label class="text-muted small mb-0 text-nowrap">Hiển thị:</label>
                    <select name="per_page" class="form-select form-select-sm form-control border-0 shadow-sm bg-light"
                        style="width: 70px;" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                        <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>

                <div class="vr h-100 mx-1"></div>

                <div class="text-muted small">
                    @if($orders->total() > 0)
                    Từ <span class="fw-bold text-body">{{ $orders->firstItem() }}</span>
                    đến <span class="fw-bold text-body">{{ $orders->lastItem() }}</span>
                    trong tổng số <span class="fw-bold text-body">{{ $orders->total() }} </span>đơn hàng
                    @else
                    Không có dữ liệu
                    @endif
                </div>
            </div>

            <div>
                @if($orders->hasPages())
                {{ $orders->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/orders.css') }}">
@endpush