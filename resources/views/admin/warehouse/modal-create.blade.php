<div class="modal fade" id="createTransactionModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content border-0 shadow-lg rounded-4">
               <div class="modal-header bg-card rounded-top-4 px-4">
                    <h5 class="modal-title fw-bold">
                         Tạo phiếu Nhập / Xuất kho
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>

               <form action="{{ route('admin.warehouse.store') }}" method="POST" id="warehouseForm">
                    @csrf
                    <div class="modal-body p-4">
                         <div class="mb-4">
                              <label class="form-label fw-bold">Chọn Sản phẩm <span class="text-danger">*</span></label>

                              <input name="product_variant_search"
                                   class="form-control"
                                   placeholder="Gõ tên hoặc SKU để tìm kiếm..."
                                   id="tagify_variant">

                              <input type="hidden" name="product_variant_id" id="real_product_variant_id">

                              <small class="text-muted d-block mt-1">Hệ thống sẽ tự động cập nhật tồn kho dựa trên phiếu này.</small>
                         </div>

                         <div class="row g-3">
                              <div class="col-md-6">
                                   <label class="form-label fw-bold">Loại giao dịch <span class="text-danger">*</span></label>
                                   <div class="input-group">
                                        <select name="type" class="form-select form-control">
                                             <option value="in">Nhập hàng (Cộng thêm)</option>
                                             <option value="out">Xuất hàng / Hủy hàng (Trừ đi)</option>
                                        </select>
                                   </div>
                              </div>

                              <div class="col-md-6">
                                   <label class="form-label fw-bold">Số lượng <span class="text-danger">*</span></label>
                                   <input type="number" name="stock" class="form-control" value="1" min="1" required>
                              </div>
                         </div>

                         {{-- 4. Ghi chú --}}
                         <div class="mb-3 mt-3">
                              <label class="form-label fw-bold">Lý do / Ghi chú</label>
                              <textarea name="description" class="form-control" rows="3"
                                   placeholder="VD: Nhập hàng từ NCC, Hủy do vỡ..."></textarea>
                         </div>
                    </div>

                    <div class="modal-footer px-4 pb-4 border-top-0">
                         <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                         <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">
                              Thực hiện
                         </button>
                    </div>
               </form>
          </div>
     </div>
</div>