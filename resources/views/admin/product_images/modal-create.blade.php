<div class="modal fade" id="createImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom bg-card pb-0 px-4 pt-4 rounded-top-4">
                <div>
                    <h5 class="modal-title fw-bold text-body">Thêm ảnh sản phẩm</h5>
                    <p class="text-muted small mb-0">Sản phẩm: <span id="create_product_name_display" class="fw-bold text-body">...</span></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="createImageForm" action="{{ route('admin.product_images.store', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="product_id" id="create_modal_product_id">
                
                <div class="modal-body p-4 bg-card">
                    <div class="upload-zone mb-3" id="uploadZone">
                        <input type="file" name="images[]" multiple accept="image/*" id="upload_images" onchange="handleFileSelect(this)">
                        <div class="pe-none">
                            <div class="bg-white rounded-circle shadow-sm d-inline-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">
                                <i class='bx bx-cloud-upload fs-1 text-body'></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Kéo thả hoặc click để chọn ảnh</h6>
                            <p class="text-muted small mb-0">Hỗ trợ: JPG, PNG, WEBP (Max 5MB)</p>
                        </div>
                    </div>

                    <div class="row g-2" id="preview_container"></div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light fw-semibold px-4 py-2 text-body" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2" id="btnUpload" disabled>
                        Thêm mới
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>  