@extends('layouts.admin')
@section('title', 'Điều chỉnh Kho hàng')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Điều chỉnh Kho hàng</h1>
        <a href="{{ route('admin.warehouse.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại lịch sử
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Tạo phiếu Nhập / Xuất kho</h6>
                </div>
                <div class="card-body">
                    @can('create', App\Models\WarehouseTransaction::class)
                    <form action="{{ route('admin.warehouse.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Chọn Sản phẩm (Phân loại) <span class="text-danger">*</span></label>
                            <select class="form-control select2-variant" name="product_variant_id">
                                <option value="">-- Gõ tên sản phẩm hoặc mã SKU để tìm --</option>
                                @foreach($variants as $variant)
                                <option value="{{ $variant->id }}">
                                    [SKU: {{ $variant->sku }}] {{ $variant->product->name }}
                                    @if($variant->name && $variant->name != $variant->product->name)
                                    - {{ $variant->name }}
                                    @endif
                                    (Tồn kho hiện tại: {{ $variant->stock }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hệ thống sẽ tự động cập nhật số lượng tồn kho dựa trên phiếu này.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Loại giao dịch <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select name="type" class="form-control">
                                        <option value="in">Nhập hàng (Cộng thêm)</option>
                                        <option value="out">Xuất hàng / Hủy hàng (Trừ đi)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label font-weight-bold">Số lượng điều chỉnh <span class="text-danger">*</span></label>
                                <input type="number" name="stock" class="form-control" value="1" placeholder="Nhập số lượng...">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label font-weight-bold">Lý do / Ghi chú</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Ví dụ: Nhập hàng đầu tháng từ nhà cung cấp A, Hủy do vỡ khi vận chuyển..."></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-success px-4 py-2 font-weight-bold shadow-sm">
                                <i class="fas fa-check-circle"></i> Xác nhận & Lưu
                            </button>
                        </div>
                    </form>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2-variant').select2({
            placeholder: "Gõ tên sản phẩm hoặc SKU để tìm...",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Không tìm thấy sản phẩm nào";
                }
            }
        });
    });
</script>
@endsection