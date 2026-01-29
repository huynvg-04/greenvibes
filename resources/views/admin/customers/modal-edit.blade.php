<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50 rounded-top-4">
                <div class="d-flex align-items-center">
                    <div>
                        <h5 class="modal-title fw-bold text-body mb-0">Chỉnh sửa hồ sơ khách hàng</h5>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="editCustomerForm" method="POST" action="">
                @csrf
                @method('PUT')

                <div class="modal-body p-4">
                    <div class="row g-4">
                        <div class="col-lg-7 border-end">
                            <h6 class="fw-bold text-uppercase text-body small mb-4">
                                Hồ sơ cá nhân
                            </h6>

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold required">Họ và Tên</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class='bx bx-user'></i></span>
                                        <input type="text" class="form-control bg-light" id="edit_fullname" name="full_name" placeholder="Ví dụ: Nguyễn Văn A">
                                    </div>
                                </div>

                                <div class="col-md-7">
                                    <label class="form-label fw-semibold required">Số điện thoại</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class='bx bx-phone'></i></span>
                                        <input type="text" class="form-control bg-light" id="edit_phone" name="phone" placeholder="09xxxxxxx">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold required">Giới tính</label>
                                    <select name="gender" id="edit_gender" class="form-select cursor-pointer">
                                        <option value="male">Nam</option>
                                        <option value="female">Nữ</option>
                                        <option value="other">Khác</option>
                                    </select>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Địa chỉ nhận hàng</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class='bx bx-map'></i></span>
                                        <input type="text" class="form-control bg-light" id="edit_address" name="address" placeholder="Số nhà, đường, phường/xã..." readonly>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <h6 class="fw-bold text-uppercase text-body small mb-4">
                                Thiết lập tài khoản
                            </h6>

                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold required">Email đăng nhập</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light text-muted"><i class='bx bx-envelope'></i></span>
                                        <input type="email" class="form-control bg-light" id="edit_email" name="email" required readonly>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold required">Hạng TV</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light"><i class='bx bx-trophy'></i></span>

                                        <select
                                            name="membership_tier_id"
                                            id="edit_tier_id"
                                            class="form-select fw-medium text-primary bg-light" 
                                            style="pointer-events: none; cursor: not-allowed;" 
                                            tabindex="-1" 
                                            aria-disabled="true">
                                            <option value="">-- Chọn hạng --</option>
                                            @foreach($tiers as $tier)
                                            <option value="{{ $tier->id }}">
                                                {{ $tier->name }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold required">Trạng thái</label>

                                    <div class="d-flex align-items-center mt-1">
                                        <input type="hidden" name="status" value="blocked">

                                        <div class="form-check form-switch mb-0">
                                            <input class="form-check-input switch-success cursor-pointer"
                                                type="checkbox"
                                                id="edit_status"
                                                name="status"
                                                value="active">

                                            <label class="form-check-label fw-bold small text-success" for="edit_status" id="edit_status_label">
                                                Hoạt động
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 mt-4">
                                    <div class="bg-light bg-opacity-50 p-3 rounded-3 border border-dashed">
                                        <div class="d-flex mb-2">
                                            <i class='bx bx-lock-alt text-body fs-5 me-2'></i>
                                            <span class="fw-bold small text-uppercase text-body">Đổi mật khẩu (Tùy chọn)</span>
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-12">
                                                <input type="password" class="form-control form-control-sm" name="password" placeholder="Mật khẩu mới...">
                                            </div>
                                            <div class="col-12">
                                                <input type="password" class="form-control form-control-sm" name="password_confirmation" placeholder="Nhập lại mật khẩu...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light px-4 py-2" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm">
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>