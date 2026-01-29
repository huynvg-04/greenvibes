<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content border-0 shadow rounded-4">

               <div class="modal-header border-bottom py-3 px-4 bg-light bg-opacity-50 rounded-top-4">
                    <div class="d-flex align-items-center">
                         <div>
                              <h5 class="modal-title fw-bold text-body mb-0">Thêm mới khách hàng</h5>
                         </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>

               <form action="{{ route('admin.customers.store') }}" method="POST" autocomplete="off">
                    @csrf
                    <input type="email" style="display:none; opacity:0; visibility:hidden" name="fake_email_prevent_autofill">
                    <input type="password" style="display:none; opacity:0; visibility:hidden" name="fake_password_prevent_autofill">

                    <div class="modal-body p-4">
                         <div class="row g-4">
                              <div class="col-lg-7 border-end">
                                   <h6 class="fw-bold text-uppercase text-body small mb-4">
                                        Hồ sơ cá nhân
                                   </h6>

                                   <div class="row g-3">
                                        <div class="col-md-12">
                                             <label class="form-label fw-semibold required">Họ và Tên <span class="text-danger">*</span></label>
                                             <div class="input-group">
                                                  <span class="input-group-text bg-light text-muted"><i class='bx bx-user'></i></span>
                                                  <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" placeholder="Nhập tên khách hàng...">
                                             </div>
                                        </div>

                                        <div class="col-md-7">
                                             <label class="form-label fw-semibold required">Số điện thoại <span class="text-danger">*</span></label>
                                             <div class="input-group">
                                                  <span class="input-group-text bg-light text-muted"><i class='bx bx-phone'></i></span>
                                                  <input type="text" class="form-control" name="phone" value="{{ old('phone') }}" placeholder="09xxxxxxx">
                                             </div>
                                        </div>
                                        <div class="col-md-5">
                                             <label class="form-label fw-semibold required">Giới tính <span class="text-danger">*</span></label>
                                             <select name="gender" class="form-select form-control cursor-pointer">
                                                  <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Nam</option>
                                                  <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Nữ</option>
                                                  <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Khác</option>
                                             </select>
                                        </div>

                                        <div class="col-md-12">
                                             <label class="form-label fw-semibold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                             <div class="input-group">
                                                  <span class="input-group-text bg-light text-muted"><i class='bx bx-map'></i></span>
                                                  <input type="text" class="form-control" name="address" value="{{ old('address') }}" placeholder="Số nhà, đường, phường/xã...">
                                             </div>
                                        </div>
                                   </div>
                              </div>

                              <div class="col-lg-5">
                                   <h6 class="fw-bold text-uppercase text-body small mb-4">
                                        Tài khoản đăng nhập
                                   </h6>

                                   <div class="row g-3">
                                        <div class="col-12">
                                             <label class="form-label fw-semibold required">Email</label>
                                             <div class="input-group">
                                                  <span class="input-group-text bg-light text-muted"><i class='bx bx-envelope'></i></span>
                                                  <input type="email" class="form-control bg-light" name="email" value="{{ old('email') }}" placeholder="email@domain.com" autocomplete="new-password">
                                             </div>
                                        </div>

                                        <div class="col-12">
                                             <label class="form-label fw-semibold required">Mật khẩu</label>
                                             <div class="input-group">
                                                  <span class="input-group-text bg-light text-muted"><i class='bx bx-lock-alt'></i></span>
                                                  <input type="password" class="form-control bg-light" name="password" placeholder="Nhập mật khẩu..." autocomplete="new-password">
                                             </div>
                                        </div>

                                        <div class="col-12">
                                             <label class="form-label fw-semibold required">Xác nhận mật khẩu</label>
                                             <div class="input-group">
                                                  <span class="input-group-text bg-light text-muted"><i class='bx bx-check-shield'></i></span>
                                                  <input type="password" class="form-control bg-light" name="password_confirmation" placeholder="Nhập lại mật khẩu..." autocomplete="new-password">
                                             </div>
                                        </div>

                                        <div class="col-12 mt-3">
                                             <div class="p-3 bg-light rounded-3 border border-dashed">
                                                  <div class="d-flex justify-content-between align-items-center mb-2">
                                                       <label class="form-label fw-semibold mb-0 small text-uppercase">Hạng thành viên</label>
                                                       <select name="membership_tier_id" class="form-select form-control form-select-sm w-auto fw-bold text-body border-0 bg-transparent py-0">
                                                            <option value="">Mặc định</option>
                                                            @foreach($tiers as $tier)
                                                            <option value="{{ $tier->id }}">{{ $tier->name }}</option>
                                                            @endforeach
                                                       </select>
                                                  </div>
                                                  <div class="d-flex justify-content-between align-items-center">
                                                       <label class="form-label fw-semibold mb-0 small text-uppercase">Trạng thái</label>

                                                       <input type="hidden" name="status" value="blocked">

                                                       <div class="form-check form-switch">
                                                            <input class="form-check-input cursor-pointer switch-success"
                                                                 type="checkbox"
                                                                 name="status"
                                                                 value="active"
                                                                 id="create_status"
                                                                 checked
                                                                 onchange="toggleCreateStatus(this)">

                                                            {{-- SỬA Ở ĐÂY: Thêm style min-width và display inline-block --}}
                                                            <label class="form-check-label small fw-bold text-success"
                                                                 for="create_status"
                                                                 id="create_status_label"
                                                                 style="min-width: 80px; display: inline-block;">
                                                                 Hoạt động
                                                            </label>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>

                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                         <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                         <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm">
                              Thêm mới
                         </button>
                    </div>
               </form>
          </div>
     </div>
</div>