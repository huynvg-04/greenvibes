<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-body mb-0">Chỉnh sửa sản phẩm</h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editProductForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="form_mode" value="edit">

                <input type="hidden" name="id" id="hidden_edit_id" value="{{ old('id') }}">

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-7 border-end">
                            <h6 class="text-uppercase text-secondary fw-bold small mb-3">Thông tin cơ bản</h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold required">Tên sản phẩm</label>
                                <input type="text" name="name" id="edit_name"
                                    class="form-control"
                                    placeholder="Nhập tên sản phẩm..."
                                    value="{{ old('name') }}"
                                    onkeyup="generateSlug('edit_name', 'edit_slug')">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">SKU</label>
                                    <input type="text" name="sku" id="edit_sku"
                                        class="form-control"
                                        placeholder="VD: CAY-BANG-01"
                                        value="{{ old('sku') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Slug</label>
                                    <input type="text" name="slug" id="edit_slug"
                                        class="form-control"
                                        placeholder="cay-bang-singapore"
                                        value="{{ old('slug') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Mô tả sản phẩm</label>
                                <textarea name="description" id="edit_description"
                                    class="form-control" rows="8" style="height: 153px;">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-5 ps-lg-4">
                            <h6 class="text-uppercase text-secondary fw-bold small mb-3">Phân loại & Giá</h6>

                            <div class="d-flex gap-3">
                                <div class="mb-3 w-50">
                                    <label class="form-label fw-bold required">Danh mục</label>
                                    <select name="category_id" id="edit_category_id" class="form-select form-control @error('category_id') is-invalid @enderror" required>
                                        <option value="">Chọn danh mục...</option>
                                        @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <div class="mb-3 w-50">
                                    <label class="form-label fw-bold">Giảm giá (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="discount_percent" id="edit_discount"
                                            class="form-control"
                                            value="{{ old('discount_percent') }}">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <div class="form-text small">Tự động tính lại giá các biến thể.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Tags</label>
                                <input name="tags" id="edit_tags"
                                    class="form-control"
                                    placeholder="Gõ tag và nhấn Enter..."
                                    value="{{ old('tags') }}">
                                <div class="form-text small text-muted">
                                    <i class='bx bx-purchase-tag'></i> Nhập tên tag và nhấn <b>Enter</b> hoặc chọn từ danh sách gợi ý.
                                </div>
                            </div>

                            <hr class="border-dashed my-4">

                            <div class="col-12 mt-4">
                                <div class="p-3 bg-light rounded-3 border border-dashed d-flex justify-content-between align-items-center">
                                    <label class="form-label fw-bold mb-0 text-uppercase small text-secondary">Trạng thái hiển thị</label>

                                    <div class="form-check form-switch mb-0 d-flex align-items-center">
                                        <input type="hidden" name="status" value="0">

                                        <input class="form-check-input cursor-pointer"
                                            type="checkbox"
                                            name="status"
                                            value="1"
                                            id="edit_status_switch"
                                            style="width: 3rem; height: 1.5rem;"
                                            onchange="toggleStatusLabel(this, 'edit_status_label')">

                                        <label class="form-check-label fw-bold ms-3"
                                            for="edit_status_switch"
                                            id="edit_status_label"
                                            style="min-width: 80px;">
                                            ...
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">
                        Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>