<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">Cập nhật danh mục</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_mode" value="edit">

                <input type="hidden" name="id" value="{{ old('id') }}">
                <div class="modal-body pt-4">
                    <div class="d-flex gap-3">
                        <div class="mb-3">
                            <label class="form-label fw-semibold required">Tên danh mục <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="edit_name" name="name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Slug</label>
                            <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="edit_slug" name="slug">
                            </div>

                        </div>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-semibold required">Loại danh mục</label>
                        <span class="text-danger">*</span></label>
                        <select class="form-select form-control bg-card" id="edit_type" name="type">    
                            <option value="product">Sản phẩm</option>
                            <option value="post">Bài viết</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ảnh</label>
                        <input type="file" class="form-control border-light" name="image">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>