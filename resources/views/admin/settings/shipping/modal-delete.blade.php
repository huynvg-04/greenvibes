
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light-danger text-danger" style="width: 64px; height: 64px; font-size: 32px;">
                    <i class='bx bx-trash'></i>
                </div>
                <h5 class="mb-2 fw-bold text-body">Xóa phí vận chuyển này?</h5>
                <p class="text-body small mb-4">
                    Hành động này không thể hoàn tác.<br>
                    Cấu hình <strong id="modalRateName" class="text-body">...</strong> sẽ bị xóa vĩnh viễn.
                </p>
                
                <form id="deleteForm" method="POST" action="" class="d-grid gap-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill fw-bold py-2">Xác nhận xóa</button>
                    <button type="button" class="btn btn-light text-body rounded-pill fw-bold py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                </form>
            </div>
        </div>
    </div>
</div>