<div class="modal fade" id="createModal" tabindex="-1" data-has-errors="{{ $errors->any() ? 'true' : 'false' }}"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold"></i> Thêm mới thuộc tính</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.attributes.store') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên thuộc tính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            id="name" placeholder="Ví dụ: Kích thước">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" id="slug" placeholder="slug-tao-tu-dong"
                            readonly>
                    </div>

                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2"
                        data-bs-dismiss="modal">Hủy bỏ</button>
                    <button type="submit"
                        class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2">Thêm mới</button>
                </div>
            </form>
        </div>
    </div>
</div>