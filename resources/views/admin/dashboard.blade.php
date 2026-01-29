@extends('layouts.admin')

@section('title', 'Dashboard - Tổng quan')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Dashboard</h3>
        </div>
        <p class="text-body mb-0">
            Số liệu kinh doanh tháng <strong>{{ \Carbon\Carbon::now()->format('m/Y') }}</strong>
        </p>
    </div>


    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                            <i class='bx bx-dollar'></i>
                        </div>
                        <div>
                            <p class="text-body text-uppercase fw-semibold small mb-1">Doanh thu tháng</p>
                            <h4 class="fw-bold text-body mb-0">{{ number_format($revenueStats['this_month'] ?? 0, 0, ',', '.') }}<small class="small ms-1 text-body">₫</small></h4>
                        </div>
                    </div>
                    @php $growth = $revenueStats['growth'] ?? 0; $isPositive = $growth >= 0; @endphp
                    <div class="d-flex align-items-center"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Tháng trước: {{ number_format($revenueStats['last_month'] ?? 0, 0, ',', '.') }}₫">
                        <span class="badge {{ $isPositive ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-2 py-1 me-2 border border-0">
                            <i class='bx {{ $isPositive ? 'bx-trending-up' : 'bx-trending-down' }}'></i> {{ $isPositive ? '+' : '' }}{{ $growth }}%
                        </span>
                        <span class="text-body small">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                            <i class='bx bxs-cart'></i>
                        </div>
                        <div>
                            <p class="text-body text-uppercase fw-semibold small mb-1">Đơn hàng tháng này</p>
                            <h4 class="fw-bold text-body mb-0">{{ number_format($orderStats['this_month'] ?? 0) }}</h4>
                        </div>
                    </div>
                    @php $growth = $orderStats['growth'] ?? 0; $isPositive = $growth >= 0; @endphp
                    <div class="d-flex align-items-center"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Tháng trước: {{ number_format($orderStats['last_month'] ?? 0) }} đơn">
                        <span class="badge {{ $isPositive ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-2 py-1 me-2 border border-0">
                            <i class='bx {{ $isPositive ? 'bx-trending-up' : 'bx-trending-down' }}'></i> {{ $isPositive ? '+' : '' }}{{ $growth }}%
                        </span>
                        <span class="text-body small">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                            <i class='bx bxs-plant-pot'></i>
                        </div>
                        <div>
                            <p class="text-body text-uppercase fw-semibold small mb-1">Sản phẩm mới</p>
                            <h4 class="fw-bold text-body mb-0">{{ number_format($productStats['this_month'] ?? 0) }}</h4>
                        </div>
                    </div>
                    @php $growth = $productStats['growth'] ?? 0; $isPositive = $growth >= 0; @endphp
                    <div class="d-flex align-items-center"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Tháng trước: {{ number_format($productStats['last_month'] ?? 0) }} SP">
                        <span class="badge {{ $isPositive ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-2 py-1 me-2 border border-0">
                            <i class='bx {{ $isPositive ? 'bx-trending-up' : 'bx-trending-down' }}'></i> {{ $isPositive ? '+' : '' }}{{ $growth }}%
                        </span>
                        <span class="text-body small">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 28px;">
                            <i class='bx bxs-user-plus'></i>
                        </div>
                        <div>
                            <p class="text-body text-uppercase fw-semibold small mb-1">Khách hàng mới</p>
                            <h4 class="fw-bold text-body mb-0">{{ number_format($userStats['this_month'] ?? 0) }}</h4>
                        </div>
                    </div>
                    @php $growth = $userStats['growth'] ?? 0; $isPositive = $growth >= 0; @endphp
                    <div class="d-flex align-items-center"
                        data-bs-toggle="tooltip"
                        data-bs-placement="top"
                        title="Tháng trước: {{ number_format($userStats['last_month'] ?? 0) }} khách">
                        <span class="badge {{ $isPositive ? 'bg-light-success text-success' : 'bg-light-danger text-danger' }} rounded-pill px-2 py-1 me-2 border border-0">
                            <i class='bx {{ $isPositive ? 'bx-trending-up' : 'bx-trending-down' }}'></i> {{ $isPositive ? '+' : '' }}{{ $growth }}%
                        </span>
                        <span class="text-body small">so với tháng trước</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8 col-lg-7">

            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center mb-4 shadow-sm rounded-4">
                <h5 class="fw-bold text-body mb-0"><i class='bx bxs-time-five me-2 text-body'></i>Đơn hàng gần đây</h5>
                @can('viewAny', App\Models\Order::class)
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light text-body fw-medium rounded-pill px-3">
                    Xem tất cả
                </a>
                @endcan
            </div>
            <div class="card-body p-0 shadow-sm rounded-4">
                <div class="table-responsive rounded-4">
                    <table class="table modern-table align-middle mb-0 table-hover custom-table">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-body small fw-bold">MÃ ĐƠN</th>
                                <th class="py-3 text-body small fw-bold">KHÁCH HÀNG</th>
                                <th class="py-3 text-body small fw-bold">TỔNG TIỀN</th>
                                <th class="py-3 text-body small fw-bold">TRẠNG THÁI</th>
                                <th class="pe-4 py-3 text-end text-body small fw-bold">NGÀY ĐẶT</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="ps-4">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-body text-decoration-none">
                                        #{{ $order->code ?? $order->id }}
                                    </a>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-semibold text-body small">{{ $order->user->name ?? 'Khách lẻ' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">
                                        {{ number_format($order->total_amount ?? $order->total, 0, ',', '.') }}₫
                                    </span>
                                </td>
                                <td>
                                    @php
                                    $stConfig = [
                                    'completed' => ['class' => 'success', 'label' => 'Hoàn thành', 'color' => 'success'],
                                    'pending' => ['class' => 'warning', 'label' => 'Chờ xử lý', 'color' => 'warning'],
                                    'cancelled' => ['class' => 'danger', 'label' => 'Đã hủy', 'color' => 'danger'],
                                    'shipping' => ['class' => 'primary', 'label' => 'Đang giao', 'color' => 'primary'],
                                    'confirmed' => ['class' => 'info', 'label' => 'Đã xác nhận', 'color' => 'info'],
                                    ];
                                    $statusData = $stConfig[$order->status] ?? ['class' => 'secondary', 'label' => $order->status];
                                    @endphp

                                    <span class="badge bg-light-{{ $statusData['class'] }} text-{{ $statusData['class'] }} fw-normal rounded-pill px-3 py-2 border border-{{ $statusData['color'] }} border-opacity-10 me-2 font-monospace">
                                        {{ $statusData['label'] }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <span class="text-body small">
                                        {{ $order->created_at ? $order->created_at->format('d/m/Y') : 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-body opacity-50 mb-2"><i class='bx bx-list-ul fs-1'></i></div>
                                    <p class="text-body small mb-0">Chưa có đơn hàng nào.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-5">
            <div class="card-header bg-white border-bottom p-4 rounded-4 mb-4 shadow-sm">
                <h5 class="fw-bold text-body mb-0 d-flex align-items-center"><i class='bx bxs-trophy me-2 text-body'></i>Top 5 sản phẩm bán chạy</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded-4 shadow-sm bg-card">
                    @forelse($topProducts as $index => $product)
                    <div class="list-group-item border-0 py-3 px-4 d-flex align-items-center hover-bg-light transition-all">
                        <div class="me-3">
                            @if($index == 0)
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px; font-size: 12px; font-weight: bold;">1</div>
                            @elseif($index == 1)
                            <div class="text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px; font-size: 12px; background-color: #9E9E9E; font-weight: bold;">2</div>
                            @elseif($index == 2)
                            <div class="text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px; font-size: 12px; background-color: #CD7F32; font-weight: bold;">3</div>
                            @else
                            <div class="bg-light text-body rounded-circle d-flex align-items-center justify-content-center border" style="width: 24px; height: 24px; font-size: 12px; font-weight: bold;">{{ $index + 1 }}</div>
                            @endif
                        </div>
                        <div class="me-3">
                            @if(isset($product->image_url) && $product->image_url)
                            <img src="{{ $product->image_url }}" class="rounded-3 border" width="48" height="48" style="object-fit: cover;" alt="{{ $product->name }}">
                            @else
                            <div class="bg-light rounded-3 d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                <i class='bx bx-image text-body fs-4'></i>
                            </div>
                            @endif
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h6 class="mb-0 fw-semibold text-body text-truncate" style="font-size: 14px;" title="{{ $product->name }}">
                                {{ $product->name }}
                            </h6>
                            <small class="text-body d-block text-truncate">{{ $product->category->name ?? 'Chưa phân loại' }}</small>
                        </div>
                        <div class="text-end ps-2">
                            <small class="text-body" style="font-size: 11px;">
                                {{ $product->sold_count ?? 0 }} đã bán
                            </small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class='bx bx-package fs-1 text-body opacity-50 mb-2'></i>
                        <p class="text-body small mb-0">Chưa có dữ liệu</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/dashboard.css') }}">
@endpush