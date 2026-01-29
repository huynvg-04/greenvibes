<div class="modal fade" id="createBannerModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold ls-1 text-body">
                    Thêm mới Banner
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label fw-bold required">Hình ảnh Banner</label>
                            <div class="upload-zone bg-body-tertiary rounded-4 border-2 border-dashed border-secondary border-opacity-25 text-center position-relative overflow-hidden hover-shadow transition-all" 
                                 style="height: 280px;">

                                <input type="file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer z-index-2" 
                                       name="image" accept="image/*"
                                       onchange="previewImage(this, 'create_preview_img', 'create_upload_placeholder')">

                                <div id="create_upload_placeholder" class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                    <i class='bx bx-cloud-upload fs-1 mb-2'></i>
                                    <span class="fw-semibold">Nhấn để chọn ảnh</span>
                                    <small class="d-block mt-1 text-xs">JPG, PNG (Max 5MB)</small>
                                </div>

                                <img id="create_preview_img" src="#" class="d-none w-100 h-100 object-fit-cover position-absolute top-0 start-0 z-index-1">
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="bg-body-tertiary p-4 rounded-4 h-100">
                                <div class="mb-3">
                                    <label class="form-label fw-bold required">Tiêu đề</label>
                                    <textarea class="form-control" name="title" id="create_title_editor"></textarea>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Đường dẫn (Link)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary border-secondary-subtle"><i class='bx bx-link'></i></span>
                                            <input type="url" class="form-control bg-body border-secondary-subtle shadow-none" name="link" placeholder="https://...">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required">Trạng thái</label>
                                        <select class="form-select bg-body border-secondary-subtle shadow-none cursor-pointer" name="status">
                                            <option value="1" selected>Hiển thị</option>
                                            <option value="0">Ẩn tạm thời</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 px-4 pb-4">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Lưu Banner</button>
                </div>
            </form>
        </div>
    </div>
</div>