<div class="modal fade" id="editImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom py-3 px-4">
                <h5 class="modal-title fw-bold text-body">Cập nhật hình ảnh</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editImageForm" action="" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="image_id" id="edit_modal_image_id">

                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-5 border-end text-center">
                            <label class="form-label fw-bold small text-uppercase text-muted">Ảnh hiện tại / Mới</label>
                            <div class="ratio ratio-1x1 bg-light rounded-3 border overflow-hidden position-relative group-hover-overlay">
                                <img id="edit_preview_img" src="" class="object-fit-contain w-100 h-100">
                                <label for="edit_image_input" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center bg-dark bg-opacity-50 opacity-0 hover-opacity-100 transition-all cursor-pointer">
                                    <span class="text-white fw-bold"><i class='bx bx-camera me-1'></i> Đổi ảnh</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-md-7 ps-md-4 d-flex flex-column justify-content-center">
                            <div class="mb-4">
                                <label class="form-label fw-bold">Chọn ảnh thay thế</label>
                                <input type="file" name="image" id="edit_image_input" class="form-control" accept="image/*" onchange="previewSingleImage(this, 'edit_preview_img')">
                                <div class="form-text small text-muted">Chỉ chọn nếu bạn muốn thay đổi file ảnh gốc.</div>
                            </div>

                            <div class="p-3 bg-light rounded-3 border border-dashed d-flex align-items-center justify-content-between">
                                <div>
                                    <label class="form-check-label fw-bold d-block" for="edit_is_primary">Ảnh đại diện</label>
                                    <small class="text-muted">Đặt làm ảnh chính cho sản phẩm này.</small>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input cursor-pointer switch-warning" style="width: 3em; height: 1.5em;" type="checkbox" name="is_primary" id="edit_is_primary" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>