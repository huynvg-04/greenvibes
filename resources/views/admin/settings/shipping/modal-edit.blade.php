<div class="modal fade" id="editShippingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50">
                <h5 class="modal-title fw-bold text-body">Cập nhật Phí Vận Chuyển</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editShippingForm" action="" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-7 border-end">
                            <h6 class="text-uppercase text-body fw-bold small mb-3">Thông tin cơ bản</h6>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold required">Tên hiển thị</label>
                                <input type="text" name="name" id="edit_name" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold required">Phí ship (VNĐ)</label>
                                <div class="input-group">
                                    <input type="number" name="fee" id="edit_fee" class="form-control" required min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Thời gian ước tính (Ngày)</label>
                                <div class="input-group">
                                    <input type="number" name="estimated_days" id="edit_estimated_days" class="form-control" min="1">
                                    <span class="input-group-text">Ngày</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-5 ps-md-4">
                            <h6 class="text-uppercase text-body fw-bold small mb-3">Điều kiện & Trạng thái</h6>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Đơn hàng tối thiểu</label>
                                <div class="input-group">
                                    <input type="number" name="min_order_value" id="edit_min_order" class="form-control" min="0">
                                    <span class="input-group-text">VNĐ</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 form-text small text-body mt-2">
                                    <i class='bx bx-info-circle'></i> Áp dụng khi tổng đơn lớn hơn số này.
                                </div>
                            </div>

                            <hr class="border-dashed">

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <label class="form-label fw-bold mb-0">Trạng thái</label>
                                <input type="hidden" name="is_active" value="0">
                                <div class="form-check form-switch">
                                    <input class="form-check-input cursor-pointer switch-success" 
                                           type="checkbox" 
                                           name="is_active" 
                                           value="1" 
                                           id="edit_is_active" 
                                           onchange="toggleStatusLabel(this, 'edit_status_label')">
                                    <label class="form-check-label small fw-bold text-success" 
                                           for="edit_is_active" 
                                           id="edit_status_label"
                                           style="min-width: 80px; display: inline-block;">
                                        Hoạt động
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>