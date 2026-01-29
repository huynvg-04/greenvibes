@extends('layouts.admin')

@section('title', 'Thống kê')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Thống kê</h3>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">
                <form action="{{ route('admin.revenue.index') }}" method="GET" class="d-flex flex-wrap gap-3 align-items-center mb-0">

                    <div class="bg-light rounded-pill p-1 m-1 d-flex text-body">
                        @foreach(['date' => 'Ngày', 'month' => 'Tháng', 'quarter' => 'Quý', 'year' => 'Năm'] as $val => $label)
                        <button type="submit" name="filter_type" value="{{ $val }}"
                            class="btn btn-sm rounded-pill px-3 transition-all {{ $filterType == $val ? 'bg-white shadow-sm text-body fw-bold' : 'text-body border-0' }}" id="chartTypeToggle">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>

                    @if($filterType == 'date')
                    <div class="d-flex align-items-center gap-2 animate__animated animate__fadeInRight">

                        <div class="input-group bg-white border border-light shadow-sm rounded-pill p-0" style="width: 280px;">
                            <span class="input-group-text border-0 bg-transparent ps-3 pe-2">
                                <i class='bx bx-calendar text-body fs-5'></i>
                            </span>

                            <input type="text" id="dateRangePicker"
                                class="form-control border-0 bg-transparent shadow-none fw-medium text-body small p-0"
                                placeholder="Chọn khoảng thời gian..."
                                readonly>

                            <input type="hidden" name="date_from" id="date_from" value="{{ $dateFrom->format('Y-m-d') }}">
                            <input type="hidden" name="date_to" id="date_to" value="{{ $dateTo->format('Y-m-d') }}">
                        </div>

                        <button type="submit" class="btn primary-color btn-icon rounded-circle shadow-sm d-flex align-items-center justify-content-center me-1"
                            style="width: 33px; height: 33px;" title="Lọc dữ liệu">
                            <i class='bx bx-filter-alt'></i>
                        </button>
                    </div>
                    @endif

                </form>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-dollar'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng Doanh Thu</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($totalRevenue, 0, ',', '.') }}<small class="small ms-1 text-body">₫</small></h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-wallet'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Lợi Nhuận Gộp</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($totalProfit, 0, ',', '.') }}<small class="small ms-1 text-body">₫</small></h4>
                        <div class="small mt-1">
                            @php $margin = $totalRevenue > 0 ? ($totalProfit/$totalRevenue)*100 : 0; @endphp
                            <span class="badge {{ $margin > 30 ? 'bg-light-success text-success' : 'bg-light-warning text-warning' }} rounded-pill border border-0">
                                {{ round($margin, 1) }}% Margin
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-cart'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng Đơn Hàng</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($totalOrders) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                        style="width: 48px; height: 48px; font-size: 28px;">
                        <i class='bx bxs-receipt'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Trung Bình Đơn</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($averageOrderValue, 0, ',', '.') }}<small class="small ms-1 text-body">₫</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-8 col-lg-7">

            <div class="card-header bg-card p-3 d-flex flex-row align-items-center justify-content-between mb-4 rounded-4 shadow-sm">
                <h5 class="fw-bold text-body mb-0"><i class='bx bx-line-chart me-2 text-body'></i>Biểu đồ Tăng trưởng</h5>
                <div class="bg-light rounded-pill p-1 d-flex align-items-center" id="chartTypeToggle">
                    <button type="button"
                        class="btn btn-sm rounded-pill px-3 fw-bold transition-all d-flex align-items-center gap-1 active bg-white shadow-sm text-body"
                        data-type="line">
                        <i class='bx bx-line-chart'></i> Đường
                    </button>

                    <button type="button"

                        class="btn btn-sm rounded-pill px-3 fw-bold transition-all d-flex align-items-center gap-1 text-body"

                        data-type="bar">

                        <i class='bx bxs-bar-chart-alt-2'></i> Cột

                    </button>

                </div>
            </div>
            <div class="card-body p-4 bg-card rounded-4 shadow-sm">
                <div class="chart-area" style="height: 350px; position: relative; width: 100%;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>

        </div>

        <div class="col-xl-4 col-lg-5">

            <div class="card-header bg-card p-4 rounded-4 shadow-sm mb-4">
                <h5 class="fw-bold text-body mb-0 d-flex align-items-center"><i class='bx bxs-doughnut-chart me-2 text-body'></i>Tỷ lệ đơn hàng</h5>
            </div>
            <div class="card-body p-4 d-flex flex-column justify-content-center bg-card rounded-4 shadow-sm">
                <div class="chart-pie mb-4" style="height: 250px; position: relative;">
                    <canvas id="orderStatusChart"></canvas>
                    @if(empty($orderStatusStats))
                    <div class="position-absolute top-50 start-50 translate-middle text-body text-center w-100">
                        <i class='bx bxs-ghost fs-1 d-block mb-2'></i>
                        Chưa có dữ liệu
                    </div>
                    @endif
                </div>
                <div class="text-center small d-flex justify-content-center gap-3 flex-wrap">

                    <span class="d-flex align-items-center text-body">
                        <i class='bx bxs-circle text-success me-1'></i> Thành công
                    </span>

                    <span class="d-flex align-items-center text-body">
                        <i class='bx bxs-circle text-warning me-1'></i> Chờ xử lý
                    </span>

                    <span class="d-flex align-items-center text-body">
                        <i class='bx bxs-circle text-muted me-1'></i> Đã xác nhận
                    </span>

                    <span class="d-flex align-items-center text-body">
                        <i class='bx bxs-circle text-danger me-1'></i> Đã hủy
                    </span>

                </div>
            </div>
        </div>
    </div>

    <div class="row g-4" style="margin-bottom: 170px;">
        <div class="col-lg-6">
            <div class="card-header bg-card p-4 rounded-4 shadow-sm mb-4">
                <h5 class="fw-bold text-body mb-0 d-flex align-items-center"><i class='bx bxs-trophy me-2 text-body'></i>Sản phẩm bán chạy</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive rounded-4 shadow-sm">
                    <table class="table align-middle mb-0 custom-table">
                        <thead class="card-header">
                            <tr>
                                <th class="ps-4 py-3 text-body small fw-bold">Sản phẩm</th>
                                <th class="text-center py-3 text-body small fw-bold">Đã bán</th>
                                <th class="text-end pe-4 py-3 text-body small fw-bold">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topProducts as $top)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        @if($top->product_image)
                                        <img src="{{ Storage::url($top->product_image) }}" class="rounded-3 me-3 border" width="48" height="48" style="object-fit: cover;">
                                        @else
                                        <div class="bg-light rounded-3 me-3 d-flex align-items-center justify-content-center border" style="width: 48px; height: 48px;">
                                            <i class='bx bx-image text-body fs-4'></i>
                                        </div>
                                        @endif
                                        <div>
                                            <div class="fw-semibold text-body text-truncate" style="max-width: 200px;">{{ Str::limit($top->name, 40) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light-success text-success border border-success border-opacity-10 fw-normal rounded-pill px-3 py-2 font-monospace">{{ $top->total_qty }}</span>
                                </td>
                                <td class="text-end pe-4 fw-bold text-success">{{ number_format($top->total_rev, 0, ',', '.') }}₫</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-body">
                                    <i class='bx bxs-ghost fs-1 d-block mb-2'></i>
                                    Chưa có dữ liệu sản phẩm
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card-header bg-card p-4 rounded-4 shadow-sm mb-4">
                <h5 class="fw-bold text-body mb-0 d-flex align-items-center">
                    <i class='bx bxs-doughnut-chart me-2 text-body'></i>Doanh thu theo danh mục
                </h5>
            </div>

            <div class="bg-card p-4 rounded-4 shadow-sm">
                @if(count($catLabels) > 0)
                <div class="chart-pie mb-4" style="height: 260px; position: relative;">
                    <canvas id="categoryChart"></canvas>
                </div>

                @php
                $chartColors = ['#0d9f6e', '#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#6b7280'];
                $totalRevenueSum = $revenueByCategory->sum('revenue');
                @endphp

                <div class="category-list pe-2" style="max-height: 200px; overflow-y: auto;">
                    @foreach($revenueByCategory as $index => $cat)
                    @php
                    $color = $chartColors[$index % count($chartColors)];
                    $percent = $totalRevenueSum > 0 ? ($cat->revenue / $totalRevenueSum) * 100 : 0;
                    @endphp

                    <div class="d-flex align-items-center justify-content-between mb-3 last-mb-0 p-2 rounded hover-bg-light transition-all">
                        <div class="d-flex align-items-center">
                            <span class="d-flex align-items-center justify-content-center me-3">
                                <i class='bx bxs-circle' style="color: {{ $color }}; font-size: 10px;"></i>
                            </span>
                            <div>
                                <div class="fw-semibold text-body small">{{ $cat->name }}</div>
                                <div class="text-body" style="font-size: 11px;">{{ round($percent, 1) }}%</div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-body">{{ number_format($cat->revenue, 0, ',', '.') }}₫</div>
                        </div>
                    </div>
                    @endforeach
                </div>

                @else
                <div class="text-center text-body py-5 h-100 d-flex flex-column justify-content-center align-items-center">
                    <i class='bx bxs-ghost fs-1 d-block mb-2'></i>
                    <p class="mb-0">Chưa có dữ liệu danh mục</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">

            <div class="card-header bg-card p-4 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 rounded-4 shadow-sm mb-4">

                <h5 class="fw-bold text-body mb-0 d-flex align-items-center">
                    <i class='bx bxs-crown me-2 text-body'></i>
                    Top khách hàng chi tiêu nhiều nhất
                </h5>

                <form action="{{ route('admin.revenue.index') }}" method="GET" class="d-flex align-items-center gap-2 mb-0">
                    {{-- Giữ nguyên các input hidden --}}
                    @if(request('filter_type')) <input type="hidden" name="filter_type" value="{{ request('filter_type') }}"> @endif
                    @if(request('date_from')) <input type="hidden" name="date_from" value="{{ request('date_from') }}"> @endif
                    @if(request('date_to')) <input type="hidden" name="date_to" value="{{ request('date_to') }}"> @endif

                    {{-- Select Tháng --}}
                    <select name="customer_month" class="form-select form-select-sm form-control px-3 py-2 rounded-pill" style="width: 110px;" onchange="this.form.submit()">
                        <option value="" {{ $custMonth == '' ? 'selected' : '' }}>Cả năm</option>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ $custMonth == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                    </select>

                    <select name="customer_year" class="form-select form-select-sm form-control px-3 py-2 rounded-pill" style="width: 130px;" onchange="this.form.submit()">
                        <option value="" {{ request()->has('customer_year') && request('customer_year') == '' ? 'selected' : '' }}>
                            Tất cả các năm
                        </option>

                        @for($y = now()->year; $y >= now()->year - 5; $y--)
                        <option value="{{ $y }}" {{ (request('customer_year') !== null && request('customer_year') != '' && $custYear == $y) ? 'selected' : ($custYear == $y && !request()->has('customer_year') ? 'selected' : '') }}>
                            Năm {{ $y }}
                        </option>
                        @endfor
                    </select>
                </form>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive rounded-4 shadow-sm">
                    <table class="modern-table table align-middle mb-0 custom-table">
                        <thead class="card-header">
                            <tr>
                                <th class="ps-4 py-3 text-body small fw-bold">Khách hàng</th>
                                <th class="py-3 text-body small fw-bold">Email</th>
                                <th class="py-3 text-center text-body small fw-bold">Số đơn</th>
                                <th class="pe-4 py-3 text-end text-body small fw-bold">Tổng chi tiêu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topCustomers as $customer)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <div class="fw-bold text-body">{{ $customer->name ?? 'Unknown' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-body">{{ $customer->email ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <span class="badge bg-light-secondary border border-secondary border-opacity-10 text-dark rounded-pill px-3 py-2 fw-normal font-monospace">{{ $customer->total_orders }} đơn</span>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="fw-bold text-danger">{{ number_format($customer->total_spent, 0, ',', '.') }}₫</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-body">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class='bx bx-ghost fs-1 text-muted mb-2'></i>
                                        <span>Không có dữ liệu trong tháng {{ $custMonth }}/{{ $custYear }}</span>
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

</div>
@endsection
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/revenue.css') }}">
@endpush
<script>
    window.RevenueConfig = {
        chartLabels: @json($chartLabels ?? []),
        chartRevenue: @json($chartRevenue ?? []),
        chartProfit: @json($chartProfit ?? []),
        orderStatusStats: @json($orderStatusStats ?? []),
        catLabels: @json($catLabels ?? []),
        catValues: @json($catValues ?? []),
        dates: {
            from: "{{ $dateFrom->format('d/m/Y') }}",
            to: "{{ $dateTo->format('d/m/Y') }}"
        }
    };
</script>
@push('scripts')
<script src="{{ asset('assets/admin/js/revenue.js') }}?v={{ time() }}"></script>
@endpush