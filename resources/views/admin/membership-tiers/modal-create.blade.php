<div class="modal fade" id="createTierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl rounded-4">
        <div class="modal-content border-0 shadow rounded-4">
            
            <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-body mb-0">Thêm Hạng Mới</h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('admin.membership-tiers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        
                        <div class="col-lg-6 border-end">
                            <h6 class="text-uppercase text-body fw-bold small mb-4">
                             Thông tin hiển thị
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold required">Tên hạng (Display Name)</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="VD: Gold Member">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold required">Độ ưu tiên</label>
                                    <input type="number" name="rank_priority" class="form-control" value="{{ old('rank_priority') }}" placeholder="VD: 1, 2...">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Màu đại diện</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="create_colorPicker" value="{{ old('color_hex', '#0d6efd') }}" style="max-width: 50px;">
                                        <input type="text" name="color_hex" id="create_colorInput" class="form-control" value="{{ old('color_hex', '#0d6efd') }}" maxlength="7">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Giảm giá (%)</label>
                                <div class="input-group">
                                    <input type="number" name="discount" class="form-control" value="{{ old('discount', 0) }}" min="0" max="100" step="0.01">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text small">Nhập 0 nếu không giảm giá.</div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Giới hạn dùng</label>
                                    <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', 0) }}" min="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Chu kỳ</label>
                                    <select name="usage_period" class="form-select">
                                        <option value="week" {{ old('usage_period') == 'week' ? 'selected' : '' }}>Tuần</option>
                                        <option value="month" {{ old('usage_period') == 'month' ? 'selected' : '' }}>Tháng</option>
                                        <option value="year" {{ old('usage_period') == 'year' ? 'selected' : '' }}>Năm</option>
                                        <option value="lifetime" {{ old('usage_period') == 'lifetime' ? 'selected' : '' }}>Vĩnh viễn</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 ps-lg-4">
                            <h6 class="text-uppercase text-body fw-bold small mb-4">
                                Điều kiện thăng hạng
                            </h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chi tiêu tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" name="min_spent" class="form-control" value="{{ old('min_spent', 0) }}" min="0">
                                    <span class="input-group-text bg-light text-muted">VNĐ</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Số đơn tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" name="min_orders" class="form-control" value="{{ old('min_orders', 0) }}" min="0">
                                    <span class="input-group-text bg-light text-muted">Đơn</span>
                                </div>
                            </div>
                            
                            <hr class="border-dashed my-4">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời hạn giữ hạng (Ngày)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class='bx bx-calendar'></i></span>
                                    <input type="number" name="validity_days" class="form-control" value="{{ old('validity_days', 0) }}" min="0" placeholder="0 = Vĩnh viễn">
                                </div>
                                <div class="form-text small">Sau thời gian này nếu không đủ điều kiện sẽ bị giảm 1 hạng.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Thêm mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
