<div class="modal fade" id="editTierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50">
                <h5 class="modal-title fw-bold text-body mb-0">Cập nhật Hạng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTierForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-6 border-end">
                            <h6 class="text-uppercase text-body fw-bold small mb-3">Thông tin hiển thị</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold required">Tên hạng</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required placeholder="VD: Gold Member">
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Độ ưu tiên</label>
                                    <input type="number" name="rank_priority" id="edit_rank_priority" class="form-control" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Màu sắc</label>
                                    <div class="input-group">
                                        <input type="color" class="form-control form-control-color" id="edit_colorPicker" style="max-width: 50px;">
                                        <input type="text" name="color_hex" id="edit_colorInput" class="form-control" maxlength="7">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Giảm giá (%)</label>
                                <div class="input-group">
                                    <input type="number" name="discount" id="edit_discount" class="form-control" min="0" max="100" step="0.01">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <label class="form-label fw-bold">Giới hạn dùng</label>
                                    <input type="number" name="usage_limit" id="edit_usage_limit" class="form-control" min="0">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-bold">Chu kỳ</label>
                                    <select name="usage_period" id="edit_usage_period" class="form-select form-control">
                                        <option value="week">Tuần</option>
                                        <option value="month">Tháng</option>
                                        <option value="year">Năm</option>
                                        <option value="lifetime">Vĩnh viễn</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 ps-md-4">
                            <h6 class="text-uppercase text-body fw-bold small mb-3">Điều kiện thăng hạng</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold">Chi tiêu tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" name="min_spent" id="edit_min_spent" class="form-control" min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Số đơn tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" name="min_orders" id="edit_min_orders" class="form-control" min="0">
                                    <span class="input-group-text">Đơn</span>
                                </div>
                            </div>
                            
                            <hr class="border-dashed">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời hạn hiệu lực (Ngày)</label>
                                <input type="number" name="validity_days" id="edit_validity_days" class="form-control" min="0">
                                <small class="text-body d-block mt-1">Để 0 = Vĩnh viễn</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>