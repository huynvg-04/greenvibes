<div class="modal fade" id="editStaffModal" tabindex="-1" aria-hidden="true">
     <div class="modal-dialog modal-dialog-centered modal-xl">
          <div class="modal-content border-0 shadow rounded-4">
               <div class="modal-header border-bottom py-3 px-4 bg-opacity-50">
                    <div class="d-flex align-items-center">
                         <div>
                              <h5 class="modal-title fw-bold text-body mb-0">Cập nhật thông tin</h5>
                         </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>

               <form id="editStaffForm" action="" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-0">
                         <ul class="nav nav-tabs border-bottom" id="editStaffTab" role="tablist">
                              <li class="nav-item" role="presentation">
                                   <button class="nav-link py-2 small fw-semibold text-uppercase rounded-0 hover-text-body border-bottom-0" id="edit-info-tab" data-bs-toggle="tab" data-bs-target="#edit-info-pane" type="button" role="tab">Thông tin chung</button>
                              </li>
                              <li class="nav-item" role="presentation">
                                   <button class="nav-link py-2 small fw-semibold text-uppercase rounded-0 hover-text-body border-bottom-0" id="edit-permission-tab" data-bs-toggle="tab" data-bs-target="#edit-permission-pane" type="button" role="tab">Phân quyền</button>
                              </li>
                         </ul>

                         <div class="tab-content p-4">
                              <div class="tab-pane fade show active" id="edit-info-pane" role="tabpanel">
                                   <div class="row g-4">
                                        <div class="col-lg-6 border-end">
                                             <h6 class="text-uppercase text-secondary fw-bold small mb-3">Tài khoản</h6>
                                             <div class="mb-3">
                                                  <label class="form-label fw-bold required">Họ và tên</label>
                                                  <input type="text" name="full_name" id="edit_full_name" class="form-control">
                                             </div>
                                             <div class="mb-3">
                                                  <label class="form-label fw-bold required">Email</label>
                                                  <input type="email" name="email" id="edit_email" class="form-control" required>
                                             </div>
                                             <div class="row g-2">
                                                  <div class="col-md-6">
                                                       <label class="form-label fw-bold">Mật khẩu mới</label>
                                                       <input type="password" name="password" class="form-control" autocomplete="new-password">
                                                  </div>
                                                  <div class="col-md-6">
                                                       <label class="form-label fw-bold">Nhập lại</label>
                                                       <input type="password" name="password_confirmation" class="form-control">
                                                  </div>
                                                  <div class="col-12"><small class="text-muted fst-italic">Để trống nếu không đổi.</small></div>
                                             </div>
                                        </div>
                                        <div class="col-lg-6 ps-lg-4">
                                             <h6 class="text-uppercase text-secondary fw-bold small mb-3">Hồ sơ</h6>
                                             <div class="row g-3">
                                                  <div class="col-md-6">
                                                       <label class="form-label fw-bold">Số điện thoại</label>
                                                       <input type="text" name="phone" id="edit_phone" class="form-control">
                                                  </div>
                                                  <div class="col-md-6">
                                                       <label class="form-label fw-bold">Vị trí</label>
                                                       <input type="text" name="position" id="edit_position" class="form-control">
                                                  </div>
                                                  <div class="col-md-6">
                                                       <label class="form-label fw-bold">Lương cơ bản</label>
                                                       <div class="input-group">
                                                            <input type="number" name="salary" id="edit_salary" class="form-control">
                                                            <span class="input-group-text small">VNĐ</span>
                                                       </div>
                                                  </div>
                                                  <div class="col-md-6">
                                                       <label class="form-label fw-bold">Ngày bắt đầu</label>
                                                       <input type="date" name="start_date" id="edit_start_date" class="form-control">
                                                  </div>
                                                  <div class="col-12 mt-4">
                                                       <div class="p-3 bg-light rounded-3 border border-dashed d-flex justify-content-between align-items-center">
                                                            <label class="form-label fw-bold mb-0 text-uppercase small">Trạng thái</label>
                                                            <div class="form-check form-switch mb-0">
                                                                 <input type="hidden" name="status" id="real_edit_status" value="active">
                                                                 <input class="form-check-input cursor-pointer switch-success"
                                                                      type="checkbox"
                                                                      id="edit_status_switch"
                                                                      onchange="toggleStatusLabel(this, 'edit_status_label')">

                                                                 <label class="form-check-label fw-bold text-success ms-2"
                                                                      for="edit_status_switch"
                                                                      id="edit_status_label"
                                                                      style="min-width: 110px; display: inline-block;">
                                                                      Đang làm việc
                                                                 </label>
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                        </div>
                                   </div>
                              </div>

                              <div class="tab-pane fade" id="edit-permission-pane" role="tabpanel">
                                   <div class="permissions-container p-3 bg-light border rounded-3" style="max-height: 450px; overflow-y: auto;">
                                        <div class="row g-4">
                                             @foreach($permissions->groupBy('group_name') as $groupName => $perms)
                                             @php $groupId = $loop->index; @endphp
                                             <div class="col-md-6 col-lg-4">
                                                  <div class="card h-100 border shadow-sm">
                                                       <div class="card-header py-2 d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold text-body small text-uppercase">{{ $groupName ?: 'Khác' }}</span>
                                                            <div class="form-check m-0 d-flex align-items-center gap-2">
                                                                 <input class="form-check-input edit-select-all cursor-pointer" type="checkbox" data-group-id="{{ $groupId }}">
                                                                 <div class="text-body small">Chọn tất cả</div>
                                                            </div>
                                                       </div>
                                                       <div class="card-body py-2">
                                                            <div class="d-flex flex-column gap-2">
                                                                 @foreach($perms as $permission)
                                                                 <div class="form-check">
                                                                      <input class="form-check-input edit-permission-item edit-group-{{ $groupId }} cursor-pointer"
                                                                           type="checkbox"
                                                                           name="permissions[]"
                                                                           value="{{ $permission->name }}"
                                                                           id="edit_perm_{{ $permission->id }}">
                                                                      <label class="form-check-label small user-select-none cursor-pointer text-body" for="edit_perm_{{ $permission->id }}">
                                                                           {{ $permission->display_name ?? $permission->name }}
                                                                      </label>
                                                                 </div>
                                                                 @endforeach
                                                            </div>
                                                       </div>
                                                  </div>
                                             </div>
                                             @endforeach
                                        </div>
                                   </div>
                              </div>
                         </div>
                    </div>
                    <div class="modal-footer border-top-0 px-4 pb-4 pt-0">
                         <button type="button" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2" data-bs-dismiss="modal">Hủy bỏ</button>
                         <button type="submit" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm">Cập nhật</button>
                    </div>
               </form>
          </div>
     </div>
</div>