<div class="modal fade" id="editBannerModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom-0 pb-0 ps-4 pt-4">
                <h5 class="modal-title fw-bold ls-1 text-body">
                    Cập nhật Banner
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="editBannerForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <label class="form-label fw-bold">Hình ảnh hiện tại</label>
                            <div class="upload-zone bg-body-tertiary rounded-4 border-2 border-dashed border-warning border-opacity-50 text-center position-relative overflow-hidden hover-shadow transition-all" 
                                 style="height: 280px;">

                                <input type="file" 
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                       style="z-index: 10;"
                                       name="image" 
                                       accept="image/*"
                                       onchange="previewImage(this, 'edit_preview_img', 'edit_placeholder_text')">

                                <img id="edit_preview_img" src="" 
                                     class="w-100 h-100 object-fit-cover position-absolute top-0 start-0"
                                     style="z-index: 5;">
                                
                                <div id="edit_placeholder_text" 
                                     class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-75 text-white py-2"
                                     style="z-index: 6; pointer-events: none;">
                                    <small><i class='bx bx-camera'></i> Nhấn vào hình để thay đổi</small>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="bg-body-tertiary p-4 rounded-4 h-100">
                                <div class="mb-3">
                                    <label class="form-label fw-bold required">Tiêu đề</label>
                                    <textarea class="form-control" name="title" id="edit_title_editor" required></textarea>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold">Đường dẫn (Link)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-secondary border-secondary-subtle"><i class='bx bx-link'></i></span>
                                            <input type="url" class="form-control bg-body border-secondary-subtle shadow-none" id="edit_link" name="link">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold required">Trạng thái</label>
                                        <select class="form-select bg-body border-secondary-subtle shadow-none cursor-pointer" id="edit_status" name="status">
                                            <option value="1">Hiển thị</option>
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
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

