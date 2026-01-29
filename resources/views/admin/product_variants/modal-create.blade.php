<div class="modal fade" id="createVariantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">
                    Thêm phân loại
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createVariantForm" method="POST" action="">
                @csrf
                <input type="hidden" name="product_ref_id" id="create_product_ref_id" value="{{ old('product_ref_id') }}">

                <div class="modal-body pt-4">
                    <div class="row g-4">
                        <div class="col-md-6 border-end">
                            <h6 class="fw-bold text-body mb-3">Thông tin cơ bản</h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold required">SKU <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="sku" class="form-control bg-card"
                                        placeholder="XL-RED" value="{{ old('sku') }}">
                                </div>
                            </div>
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Giá nhập <span class="text-danger">*</span></label>
                                    <input type="number" name="standard_cost" class="form-control bg-card"
                                        value="{{ old('standard_cost', 0) }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-semibold">Giá niêm yết <span class="text-danger">*</span></label>
                                    <input type="number" name="list_price" class="form-control bg-card"
                                        value="{{ old('list_price', 0) }}">
                                </div>
                            </div>
                            <input type="hidden" name="stock" value="0">
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-body mb-3">Thuộc tính phân loại <span class="text-danger">*</span></h6>
                            <div class="bg-card p-3 rounded-3 border">
                                @if(isset($attributes) && $attributes->count() > 0)
                                @foreach($attributes as $attribute)
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small text-body text-uppercase">
                                        {{ $attribute->name }}
                                    </label>

                                    <select name="attributes[{{ $attribute->id }}]" class="form-select form-control border-white shadow-sm cursor-pointer">
                                        <option value="">Chọn thuộc tính...</option>

                                        @foreach($attribute->values as $value)
                                        <option value="{{ $value->id }}"
                                            {{ old('attributes.' . $attribute->id) == $value->id ? 'selected' : '' }}>
                                            {{ $value->value }}
                                        </option>
                                        @endforeach
                                    </select>

                                    @error('attributes.' . $attribute->id)
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endforeach
                                @else
                                <div class="text-center text-body py-3 small">
                                    Chưa có thuộc tính. <a href="{{ route('admin.attributes.index') }}">Thêm ngay</a>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>