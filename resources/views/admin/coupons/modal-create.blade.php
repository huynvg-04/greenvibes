<div class="modal fade" id="createCouponModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content border-0 shadow rounded-4">
               <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50 rounded-top-4">
                    <h5 class="modal-title fw-bold text-body">Thêm mới mã giảm giá</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <form action="{{ route('admin.coupons.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="modal-body p-4">
                         <div class="row">
                              <div class="col-md-6 border-end">
                                   <h6 class="text-uppercase text-body fw-bold small mb-3">Thông tin cơ bản</h6>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold required">Mã Code</label>
                                        <input type="text" name="code" class="form-control text-uppercase @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="VD: SALE50">
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea name="description" class="form-control" rows="2" placeholder="VD: Giảm giá nhân dịp lễ...">{{ old('description') }}</textarea>
                                   </div>

                                   <div class="row">
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label fw-bold required">Loại giảm</label>
                                             <select name="type" class="form-select" id="create_type_select" onchange="toggleMaxDiscount('create_type_select', 'create_max_discount_area')">
                                                  <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Số tiền (VNĐ)</option>
                                                  <option value="percent" {{ old('type') == 'percent' ? 'selected' : '' }}>Phần trăm (%)</option>
                                             </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label fw-bold required">Giá trị giảm</label>
                                             <input type="number" name="value" class="form-control @error('value') is-invalid @enderror" value="{{ old('value') }}">
                                        </div>
                                   </div>
                              </div>

                              <div class="col-md-6 ps-md-4">
                                   <h6 class="text-uppercase text-body fw-bold small mb-3">Điều kiện áp dụng</h6>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold text-body">Đơn hàng tối thiểu (VNĐ)</label>
                                        <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value', 0) }}" min="0">
                                   </div>

                                   <div class="mb-3" id="create_max_discount_area" style="display: none;">
                                        <label class="form-label fw-bold text-body">Giảm tối đa (VNĐ)</label>
                                        <input type="number" name="max_discount_value" class="form-control" value="{{ old('max_discount_value') }}" min="0" placeholder="VD: 50000">
                                        <small class="text-muted">Chỉ áp dụng cho loại Phần trăm.</small>
                                   </div>

                                   <div class="row">
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label">Bắt đầu</label>
                                             <input type="datetime-local" name="start_date" class="form-control" value="{{ old('start_date') }}">
                                        </div>
                                        <div class="col-md-6 mb-3">
                                             <label class="form-label">Kết thúc</label>
                                             <input type="datetime-local" name="end_date" class="form-control" value="{{ old('end_date') }}">
                                        </div>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label">Giới hạn số lần dùng</label>
                                        <input type="number" name="usage_limit" class="form-control" placeholder="Để trống là không giới hạn" value="{{ old('usage_limit') }}">
                                   </div>

                                   <div class="d-flex justify-content-between align-items-center mt-3">
                                        <label class="form-label fw-bold mb-0">Trạng thái</label>
                                        <input type="hidden" name="is_active" value="0"> 
                                        <div class="form-check form-switch">
                                             <input class="form-check-input cursor-pointer switch-success"
                                                  type="checkbox"
                                                  name="is_active"
                                                  value="1"
                                                  id="create_is_active"
                                                  checked
                                                  onchange="toggleStatusLabel(this, 'create_status_label')">
                                             <label class="form-check-label small fw-bold text-success"
                                                  for="create_is_active"
                                                  id="create_status_label"
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
                         <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Thêm mới</button>
                    </div>
               </form>
          </div>
     </div>
</div>