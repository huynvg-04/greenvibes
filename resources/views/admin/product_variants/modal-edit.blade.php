<div class="modal fade" id="editVariantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">
                    Chỉnh sửa phân loại
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editVariantForm" method="POST" action="">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" id="edit_variant_id" value="{{ old('id') }}">
                
                <input type="hidden" name="modal_edit_sku_display" id="modal_edit_sku_display" value="{{ old('modal_edit_sku_display') }}">

                <div class="modal-body pt-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-body mb-3">Thông tin giá & Kho</h6>

                            <div class="mb-3">
                                <label class="form-label fw-semibold required">Mã SKU</label>
                                <input type="text" name="sku" id="edit_sku" class="form-control bg-card border-light"
                                    required value="{{ old('sku') }}">
                            </div>

                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Giá nhập</label>
                                    <input type="number" name="standard_cost" id="edit_standard_cost" class="form-control bg-card border-light"
                                        required value="{{ old('standard_cost') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Giá niêm yết</label>
                                    <input type="number" name="list_price" id="edit_list_price" class="form-control bg-card border-light"
                                        required value="{{ old('list_price') }}">
                                </div>
                            </div>

                            <div class="rounded-pill border border-light d-flex align-items-center p-2 mb-0">
                                <div class="small text-muted flex-grow-1">
                                    Tồn kho được quản lý tại kho hàng.
                                </div>
                                <a href="{{ route('admin.warehouse.create') }}" class="btn btn-sm btn-create rounded-pill text-nowrap ms-2">
                                    Đi đến
                                </a>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-body mb-3">Thuộc tính phân loại</h6>
                            <div class="bg-card p-3 rounded-3 border border-light">
                                @if(isset($attributes) && $attributes->count() > 0)
                                @foreach($attributes as $attribute)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-body text-uppercase">{{ $attribute->name }}</label>
                                    <select name="attributes[{{ $attribute->id }}]" class="form-select border-white shadow-sm focus-ring attribute-select">
                                        <option value="">{{ $attribute->name }}</option>
                                        @foreach($attribute->values as $value)
                                        {{-- Logic để giữ lại option đã chọn khi có lỗi (old array) --}}
                                        <option value="{{ $value->id }}"
                                            {{ (collect(old('attributes'))->contains($value->id)) ? 'selected' : '' }}>
                                            {{ $value->value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endforeach
                                @else
                                <div class="text-center text-muted small">Chưa có thuộc tính nào.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>