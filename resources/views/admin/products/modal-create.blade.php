<div class="modal fade" id="createProductModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
          <div class="modal-content border-0 shadow rounded-4">
               <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50">
                    <div class="d-flex align-items-center">
                         <h5 class="modal-title fw-bold text-body mb-0">Thêm sản phẩm mới</h5>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>

               <form id="createProductForm" method="POST" action="{{ route('admin.products.store') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="form_mode" value="create">

                    <div class="modal-body p-4">
                         <div class="row g-4">
                              <div class="col-lg-7 border-end">
                                   <h6 class="text-uppercase text-secondary fw-bold small mb-3">Thông tin cơ bản</h6>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold required">Tên sản phẩm <span
                                                  class="text-danger">*</span></label>
                                        <input type="text" name="name" id="create_name"
                                             class="form-control @error('name') is-invalid @enderror"
                                             placeholder="Nhập tên sản phẩm..." value="{{ old('name') }}"
                                             onkeyup="generateSlug('create_name', 'create_slug')">
                                   </div>

                                   <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                             <label class="form-label fw-bold">SKU <span
                                                       class="text-danger">*</span></label>
                                             <input type="text" name="sku" id="create_sku"
                                                  class="form-control @error('sku') is-invalid @enderror"
                                                  placeholder="VD: SP-001" value="{{ old('sku') }}">
                                        </div>
                                        <div class="col-md-6">
                                             <label class="form-label fw-bold">Slug <span
                                                       class="text-danger">*</span></label>
                                             <input type="text" name="slug" id="create_slug"
                                                  class="form-control  @error('slug') is-invalid @enderror"
                                                  placeholder="tu-dong-tao-tu-ten" value="{{ old('slug') }}">
                                        </div>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold">Mô tả sản phẩm</label>
                                        <textarea name="description" id="create_description"
                                             class="form-control bg-card" rows="8" placeholder="Nhập mô tả chi tiết..."
                                             style="height: 153px;">{{ old('description') }}</textarea>
                                   </div>
                              </div>

                              <div class="col-lg-5 ps-lg-4">
                                   <h6 class="text-uppercase text-secondary fw-bold small mb-3">Phân loại & Cài đặt</h6>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold required">Danh mục <span
                                                  class="text-danger">*</span></label>

                                        <select name="category_id"
                                             class="form-select form-control @error('category_id') is-invalid @enderror">
                                             <option value="">Chọn danh mục...</option>
                                             @foreach($categories as $category)
                                                  <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                       {{ $category->name }}
                                                  </option>
                                             @endforeach
                                        </select>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold">Giảm giá (%)</label>
                                        <div class="input-group">
                                             <input type="number" name="discount_percent" class="form-control" min="0"
                                                  max="100" value="{{ old('discount_percent', 0) }}">
                                             <span class="input-group-text">%</span>
                                        </div>
                                   </div>

                                   <div class="mb-3">
                                        <label class="form-label fw-bold">Tags</label>
                                        <input name="tags" id="create_tags" class="form-control"
                                             placeholder="Gõ tag và nhấn Enter..." value="{{ old('tags') }}">
                                        <div class="form-text small text-muted">
                                             <i class='bx bx-purchase-tag'></i>Chọn tag đã có hoặc nhập tên tag và nhấn
                                             <b>Enter</b>.
                                        </div>
                                   </div>

                                   <hr class="border-dashed my-4">

                                   <div class="col-12 mt-4">
                                        <div
                                             class="p-3 bg-card rounded-3 border border-dashed d-flex justify-content-between align-items-center">
                                             <label
                                                  class="form-label fw-bold mb-0 text-uppercase small text-secondary">Trạng
                                                  thái ban đầu</label>

                                             <div class="form-check form-switch mb-0 d-flex align-items-center">
                                                  <input type="hidden" name="status" value="0">


                                                  <input class="form-check-input cursor-pointer" type="checkbox"
                                                       name="status" value="1" id="create_status_switch" checked
                                                       style="width: 2.5rem; height: 1.2rem;"
                                                       onchange="toggleStatusLabel(this, 'create_status_label')">

                                                  <label class="form-check-label fw-bold ms-3 text-success"
                                                       for="create_status_switch" id="create_status_label">
                                                       Kích hoạt
                                                  </label>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                         <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2"
                              data-bs-dismiss="modal">Hủy bỏ</button>
                         <button type="submit"
                              class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">
                              Thêm mới
                         </button>
                    </div>
               </form>
          </div>
     </div>
</div>