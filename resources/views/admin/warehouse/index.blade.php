@extends('layouts.admin')

@section('title', 'Quản lý kho hàng')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Kho hàng</h3>
        </div>

        @can('create', App\Models\WarehouseTransaction::class)
        <button type="button"
            class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
            data-bs-toggle="modal"
            data-bs-target="#createTransactionModal">
            <i class='bx bx-transfer-alt fs-5'></i>
            <span class="fw-semibold">Nhập/Xuất thủ công</span>
        </button>
        @endcan
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">

            <form action="{{ route('admin.warehouse.index') }}" method="GET">
                @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
                @endif

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                    {{-- 1. Ô TÌM KIẾM --}}
                    <div style="min-width: 250px;" class="flex-grow-1 flex-md-grow-0">
                        <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                            <span class="input-group-text bg-transparent border-0 pe-2 text-body">
                                <i class='bx bx-search'></i>
                            </span>
                            <input type="text"
                                name="keyword"
                                class="form-control border-0 bg-transparent shadow-none text-body small"
                                placeholder="Tìm tên SP, SKU..."
                                value="{{ request('keyword') }}">
                        </div>
                    </div>

                    {{-- 2. CÁC BỘ LỌC --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">

                        {{-- Nút Reset --}}
                        @if(request()->anyFilled(['keyword', 'type', 'source', 'month', 'year']))
                        <a href="{{ route('admin.warehouse.index') }}"
                            class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0"
                            data-bs-toggle="tooltip" title="Xóa bộ lọc"
                            style="width: 36px; height: 36px;">
                            <i class='bx bx-refresh fs-5'></i>
                        </a>
                        @endif

                        {{-- THANH LỌC LOẠI KHO (PILL TABS) --}}
                        <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto shadow-sm" style="max-width: 100%; white-space: nowrap;">
                            <a href="{{ request()->fullUrlWithQuery(['type' => null, 'page' => 1]) }}"
                                class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('type') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Tất cả
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'in', 'page' => 1]) }}"
                                class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('type') == 'in' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Nhập kho
                            </a>
                            <a href="{{ request()->fullUrlWithQuery(['type' => 'out', 'page' => 1]) }}"
                                class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('type') == 'out' ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                                Xuất kho
                            </a>
                        </div>

                        <div class="vr d-none d-xl-block mx-1 text-muted opacity-25" style="height: 40px;"></div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="position-relative">
                                <select name="source"
                                    class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 me-1 w-auto"
                                    style="appearance: none;"
                                    onchange="this.form.submit()">
                                    <option value="">Nguồn gốc</option>
                                    <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Thủ công</option>
                                    <option value="order" {{ request('source') == 'order' ? 'selected' : '' }}>Đơn hàng</option>
                                    <option value="return" {{ request('source') == 'return' ? 'selected' : '' }}>Hoàn hàng</option>
                                </select>
                            </div>

                            <div class="position-relative">
                                <select name="month"
                                    class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 me-1 w-auto"
                                    style="appearance: none;"
                                    onchange="this.form.submit()">
                                    <option value="">Tất cả tháng</option>
                                    @for ($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                        @endfor
                                </select>
                            </div>

                            <div class="position-relative">
                                <select name="year"
                                    class="form-select form-select-sm rounded-pill bg-card border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 me-1 w-auto"
                                    style="appearance: none;"
                                    onchange="this.form.submit()">
                                    <option value="">Tất cả năm</option>
                                    @foreach ($years as $y)
                                    <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive rounded-4 bg-card shadow-sm mb-4" style="min-height: 400px;">
            <table class="table modern-table align-middle mb-0 table-hover custom-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 align-middle text-body small fw-bold text-uppercase">Thời gian</th>
                        <th class="align-middle text-body small fw-bold text-uppercase">Sản phẩm / Phân loại</th>
                        <th class="text-center align-middle text-body small fw-bold text-uppercase">Loại</th>
                        <th class="text-center align-middle text-body small fw-bold text-uppercase">Số lượng</th>
                        <th class="text-center align-middle text-body small fw-bold text-uppercase">Tồn cuối</th>
                        <th class="align-middle text-body small fw-bold text-uppercase">Nguồn / Lý do</th>
                        <th class="pe-4 text-end align-middle text-body small fw-bold text-uppercase">Người thực hiện</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trans)
                    <tr class="transition-all hover-bg-light">
                        <td class="ps-4 text-body small">
                            <div class="d-flex flex-column">
                                <span class="fw-bold text-body">{{ $trans->created_at->format('d/m/Y') }}</span>
                                <span>{{ $trans->created_at->format('H:i') }}</span>
                            </div>
                        </td>

                        <td>
                            @if($trans->variant)
                            <div class="d-flex flex-column">
                                <span class="fw-semibold text-body text-truncate" style="max-width: 250px;">
                                    {{ $trans->variant->product->name ?? 'Sản phẩm đã xóa' }}
                                </span>
                                <small class="text-body font-monospace mt-1">
                                    SKU: {{ $trans->variant->sku }}
                                    @if($trans->variant->name && $trans->variant->name != $trans->variant->product->name)
                                    @endif
                                </small>
                            </div>
                            @else
                            <span class="badge bg-light-danger text-danger">Phân loại đã xóa</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($trans->type == 'in')
                            <span class="badge bg-light-success text-success border border-success border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 fw-normal font-monospace">
                                <i class='bx bx-down-arrow-alt'></i> Nhập
                            </span>
                            @else
                            <span class="badge bg-light-danger text-danger border border-danger border-opacity-10 rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 fw-normal font-monospace">
                                <i class='bx bx-up-arrow-alt'></i> Xuất
                            </span>
                            @endif
                        </td>

                        <td class="text-center">
                            <span class="fw-bold small {{ $trans->type == 'in' ? 'text-success' : 'text-danger' }}">
                                {{ $trans->type == 'in' ? '+' : '-' }}{{ number_format($trans->quantity) }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if($trans->balance_after !== null)
                            <span class="fw-medium text-body">{{ number_format($trans->balance_after) }}</span>
                            @else
                            <span class="text-body small">-</span>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                @if($trans->reference_type == 'App\Models\Order')
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light-info text-info border border-0 rounded-1 px-2" style="font-size: 10px;">Đơn hàng</span>
                                    <a href="{{ route('admin.orders.show', $trans->reference_id) }}" class="text-decoration-none fw-bold small primary-color">
                                        #{{ $trans->reference->code ?? $trans->reference_id }}
                                    </a>
                                </div>

                                @elseif($trans->reference_type == 'App\Models\OrderReturn')
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light-danger text-danger border border-0 rounded-1 px-2" style="font-size: 10px;">Hoàn hàng</span>
                                    <a href="{{ route('admin.returns.show', $trans->reference_id) }}" class="text-decoration-none fw-bold small text-danger">
                                        Return #{{ $trans->reference->id ?? $trans->reference_id }}
                                    </a>
                                </div>

                                @else
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-light-warning text-warning border border-0 rounded-1 px-2" style="font-size: 10px;">Thủ công</span>
                                </div>
                                @endif

                                <small class="text-body text-truncate" style="max-width: 200px;" title="{{ $trans->description }}">
                                    {{ $trans->description ?? 'Không có ghi chú' }}
                                </small>
                            </div>
                        </td>

                        <td class="pe-4 text-end">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <span class="small text-body">{{ $trans->user->name ?? 'System' }}</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bx-box fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có giao dịch kho nào.</h6>
                                @if(request()->anyFilled(['keyword', 'type']))
                                <p class="text-body small">Thử thay đổi bộ lọc tìm kiếm.</p>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('admin.warehouse.modal-create')

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
                    @if($transactions->total() > 0)
                    Hiển thị <span class="fw-bold text-body">{{ $transactions->firstItem() }}</span> - <span class="fw-bold text-body">{{ $transactions->lastItem() }}</span>
                    trong tổng số <span class="fw-bold text-body">{{ $transactions->total() }}</span> giao dịch
                    @else
                    Không có dữ liệu
                    @endif
                </div>
            </div>

            <div>
                @if($transactions->hasPages())
                {{ $transactions->onEachSide(2)->links('vendor.pagination.bootstrap-4') }}
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/warehouse.css') }}">
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<style>
    .tagify {
        --tags-border-color: #dee2e6;
        --tags-focus-border-color: #1f503a;
        border-radius: 0.375rem;
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        var input = document.querySelector('#tagify_variant');
        var hiddenInput = document.querySelector('#real_product_variant_id');
        
        var stockArea = document.getElementById('stock_display_area');
        var stockValue = document.getElementById('current_stock_value');

        var whitelist = [
            @foreach($variants as $variant)
            {
                "value": "[{{ $variant->sku }}] {{ $variant->product->name }} {{ ($variant->name && $variant->name != $variant->product->name) ? '- '.$variant->name : '' }} (Tồn: {{ $variant->stock }})",
                
                "id": "{{ $variant->id }}", 
                "stock": "{{ $variant->stock }}",
                "searchBy": "{{ $variant->sku }} {{ $variant->product->name }} {{ $variant->name }}"
            },
            @endforeach
        ];

        var tagify = new Tagify(input, {
            mode: 'select',
            whitelist: whitelist,
            enforceWhitelist: true,
            keepInvalidTags: false,
            dropdown: {
                maxItems: 20,
                classname: "tags-look",
                enabled: 0,
                closeOnSelect: true,
                searchKeys: ["searchBy"] 
            },
            placeholder: "Gõ SKU hoặc tên sản phẩm..."
        });

        tagify.on('add', function(e) {
            if(e.detail.data) {
                hiddenInput.value = e.detail.data.id; 
                
                stockValue.textContent = e.detail.data.stock;
                stockArea.classList.remove('d-none');
            }
        });

        tagify.on('remove', function(e) {
            hiddenInput.value = '';
            stockArea.classList.add('d-none');
        });

        var oldVariantId = "{{ old('product_variant_id') }}";
        if (oldVariantId) {
            var foundItem = whitelist.find(item => item.id == oldVariantId);
            if (foundItem) {
                tagify.addTags([foundItem]);
                hiddenInput.value = oldVariantId;
                stockValue.textContent = foundItem.stock;
                stockArea.classList.remove('d-none');
            }
        }

        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('createTransactionModal'));
            myModal.show();
        @endif

        document.getElementById('warehouseForm').addEventListener('submit', function() {
            var btn = document.getElementById('btnSubmitTransaction');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
        });
    });
</script>
@endsection