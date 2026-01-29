<div class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">Cập nhật thuộc tính và giá trị</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body pt-4">
                <div class="row g-4">

                    <div class="col-12 col-md-5 border-end">
                        <h6 class="fw-bold text-body mb-3">Thông tin chung</h6>
                        <form id="editForm" method="POST" action="">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tên thuộc tính <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Slug <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_slug" name="slug">
                            </div>
                            <button type="submit"
                                class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm">
                                Cập nhật Tên
                            </button>
                        </form>
                    </div>

                    <div class="col-12 col-md-7">
                        <h6 class="fw-bold text-body mb-3">Các giá trị</h6>

                        <div class="bg-light p-3 rounded-3 mb-3 border border-light">
                            <form id="addValueForm" method="POST" action="">
                                @csrf
                                <label class="small fw-bold text-body mb-2">Thêm giá trị mới <span
                                        class="text-danger">*</span>:</label>
                                <div class="row g-2">
                                    <div class="d-flex align-items-stretch gap-2">
                                        <div class="col-7">
                                            <input type="text" name="value"
                                                class="form-control form-control-sm border-white" style="height: 31px;"
                                                placeholder="Tên (VD: Đỏ)">
                                        </div>
                                        <div class="col-5">
                                            <button type="submit"
                                                class="btn btn-primary btn-create d-flex align-items-center gap-2"
                                                style="height: 31px;">
                                                Thêm
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="code" class="form-control form-control-sm border-white"
                                            placeholder="Mã màu (VD: #FF0000) nếu thuộc tính là màu sắc">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive rounded-4 border table-scroll"
                            style="max-height: 250px; overflow-y: auto;">
                            <table class="table table-sm align-middle mb-0 rounded-left-4">
                                <thead class="bg-white sticky-top">
                                    <tr>
                                        <th class="small ps-3">Giá trị</th>
                                        <th class="small">Mã</th>
                                        <th class="small text-end pe-3">Xóa</th>
                                    </tr>
                                </thead>
                                <tbody id="modalValuesTable">
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2"
                    data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>