<div class="modal fade" id="editCouponModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content border-0 shadow rounded-4">
               <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50">
                    <h5 class="modal-title fw-bold text-body">Cập nhật mã giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <form id="editCouponForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-4">
                         <div class="row">
                              <div class="col-md-6 border-end">
                                   <h6 class="text-uppercase text-secondary fw-bold small mb-3">Thông tin cơ bản</h6>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold required">Mã Code</label>
                                        <input type="text" name="code" id="edit_code" class="form-control text-uppercase" required>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea name="description" id="edit_description" class="form-control" rows="2"></textarea>
                                   </div>

                                   <div class="row">
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label fw-bold required">Loại giảm</label>
                                             <select name="type" class="form-select" id="edit_type_select" onchange="toggleMaxDiscount('edit_type_select', 'edit_max_discount_area')">
                                                  <option value="fixed">Số tiền (VNĐ)</option>
                                                  <option value="percent">Phần trăm (%)</option>
                                             </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label fw-bold required">Giá trị giảm</label>
                                             <input type="number" name="value" id="edit_value" class="form-control" required min="0">
                                        </div>
                                   </div>
                              </div>

                              <div class="col-md-6 ps-md-4">
                                   <h6 class="text-uppercase text-secondary fw-bold small mb-3">Điều kiện áp dụng</h6>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold text-body">Đơn hàng tối thiểu (VNĐ)</label>
                                        <input type="number" name="min_order_value" id="edit_min_order" class="form-control" min="0">
                                   </div>

                                   <div class="mb-3" id="edit_max_discount_area" style="display: none;">
                                        <label class="form-label fw-bold text-body">Giảm tối đa (VNĐ)</label>
                                        <input type="number" name="max_discount_value" id="edit_max_discount" class="form-control" min="0">
                                   </div>

                                   <div class="row">
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label">Bắt đầu</label>
                                             <input type="datetime-local" name="start_date" id="edit_start_date" class="form-control">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label">Kết thúc</label>
                                             <input type="datetime-local" name="end_date" id="edit_end_date" class="form-control">
                                        </div>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label">Giới hạn số lần dùng</label>
                                        <input type="number" name="usage_limit" id="edit_usage_limit" class="form-control">
                                   </div>

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
                         <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Cập nhật</button>
                    </div>
               </form>
          </div>
     </div>
</div>