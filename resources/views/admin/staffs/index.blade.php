@extends('layouts.admin')

@section('title', 'Quản lý nhân viên')

@section('content')
<div class="container-fluid px-0">

     <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
          <div class="mb-3 mb-md-0 border-left-4">
               <h3 class="fw-bold text-body mb-1 ps-4">Quản lý nhân viên</h3>
          </div>

          @can('create', App\Models\User::class)
          <button type="button" class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 shadow-sm"
               data-bs-toggle="modal" data-bs-target="#createStaffModal">
               <i class='bx bx-plus fs-5'></i> <span class="fw-semibold">Thêm mới</span>
          </button>
          @endcan
     </div>

     <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
          <div class="card-body p-0">
               <div class="table-responsive rounded-4" style="min-height: 400px;">
                    <table class="table modern-table align-middle mb-0 table-hover custom-table">
                         <thead class="bg-light">
                              <tr>
                                   <th class="ps-4 py-3 text-uppercase text-body small fw-bold" style="width: 50px;">ID</th>
                                   <th class="py-3 text-uppercase text-body small fw-bold">Nhân viên</th>
                                   <th class="py-3 text-uppercase text-body small fw-bold">Vị trí</th>
                                   <th class="py-3 text-uppercase text-body small fw-bold text-center">Quyền hạn</th>
                                   <th class="py-3 text-uppercase text-body small fw-bold text-center">Trạng thái</th>
                                   <th class="pe-4 py-3 text-center text-uppercase text-body small fw-bold" style="width: 120px;">Hành động</th>
                              </tr>
                         </thead>
                         <tbody>
                              @foreach($staffs as $staff)
                              @php
                              $profile = $staff->staffProfile;
                              $displayName = $profile->full_name ?? $staff->name;
                              @endphp
                              <tr class="transition-all hover-bg-light">
                                   <td class="ps-4 fw-semibold text-body">#{{ $staff->id }}</td>
                                   <td>
                                        <div class="d-flex align-items-center">
                                             <div>
                                                  <div class="fw-bold text-body">{{ $displayName }}</div>
                                                  <div class="small text-muted">{{ $staff->email }}</div>
                                             </div>
                                        </div>
                                   </td>
                                   <td>
                                        <span class="fw-semibold text-body position-text">
                                             {{ $profile->position ?? 'Chưa cập nhật' }}
                                        </span>
                                   </td>
                                   <td class="text-center">
                                        @if($staff->hasRole('manager'))
                                        <span class="badge bg-light-primary text-primary rounded-pill px-3 py-2 border border-primary border-opacity-10 fw-normal font-monospace"
                                             data-bs-toggle="tooltip"
                                             title="Quản lý có toàn quyền hệ thống">
                                             <i class='bx bxs-shield-star me-1'></i>Toàn quyền
                                        </span>
                                        @else
                                        <span class="badge bg-light-info text-info rounded-pill px-3 py-2 border border-info border-opacity-10 fw-normal font-monospace">
                                             {{ $staff->permissions->count() }} quyền
                                        </span>
                                        @endif
                                   </td>
                                   <td class="text-center">
                                        @if(($profile->status ?? '') == 'active')
                                        <span class="badge bg-light-success text-success rounded-pill px-3 py-2 fw-normal font-monospace border border-success border-opacity-10">
                                             Đang làm việc
                                        </span>
                                        @elseif(($profile->status ?? '') == 'blocked')
                                        <span class="badge bg-light-danger text-danger rounded-pill px-3 py-2 fw-normal font-monospace border border-danger border-opacity-10">
                                             Đã nghỉ/khóa
                                        </span>
                                        @else
                                        <span class="badge bg-light-secondary text-secondary rounded-pill px-3 py-2 fw-normal font-monospace border border-secondary border-opacity-10">
                                             Khác
                                        </span>
                                        @endif
                                   </td>
                                   <td class="pe-4 text-center">
                                        <div class="dropdown">
                                             <button class="btn btn-icon btn-light border-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                  <i class='bx bx-dots-vertical-rounded fs-4'></i>
                                             </button>
                                             <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 bg-card" style="border-radius: 12px; min-width: 180px;">
                                                  @can('update', $staff)
                                                  <li>
                                                       <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-body btn-edit-staff"
                                                            href="javascript:void(0)"
                                                            data-id="{{ $staff->id }}"
                                                            data-email="{{ $staff->email }}"
                                                            data-fullname="{{ $profile->full_name ?? $staff->name }}"
                                                            data-phone="{{ $profile->phone ?? '' }}"
                                                            data-position="{{ $profile->position ?? '' }}"
                                                            data-salary="{{ $profile->salary ?? '' }}"
                                                            data-start-date="{{ $profile->start_date ? $profile->start_date->format('Y-m-d') : '' }}"
                                                            data-status="{{ $profile->status ?? 'active' }}"
                                                            data-permissions='@json($staff->permissions->pluck("name"))'>
                                                            <i class='bx bx-edit-alt fs-5 me-3 text-muted'></i> Chỉnh sửa
                                                       </a>
                                                  </li>
                                                  @endcan

                                                  <li>
                                                       <hr class="dropdown-divider my-1">
                                                  </li>

                                                  @if($staff->id !== Auth::id())
                                                  @can('delete', $staff)
                                                  <li>
                                                       <a class="dropdown-item rounded-3 py-2 d-flex align-items-center text-danger"
                                                            href="javascript:void(0)"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#deleteModal"
                                                            data-staff-id="{{ $staff->id }}"
                                                            data-staff-name="{{ $profile->full_name ?? $staff->name }}">
                                                            <i class='bx bx-trash fs-5 me-3'></i> Xóa nhân viên
                                                       </a>
                                                  </li>
                                                  @endcan
                                                  @else
                                                  <li>
                                                       <span class="dropdown-item disabled fst-italic small text-muted">Đang đăng nhập</span>
                                                  </li>
                                                  @endif
                                             </ul>
                                        </div>
                                   </td>
                              </tr>
                              @endforeach
                         </tbody>
                    </table>
               </div>

               @if($staffs->hasPages())
               <div class="card-footer bg-white border-top py-3 px-4">
                    {{ $staffs->links('vendor.pagination.bootstrap-4') }}
               </div>
               @endif
          </div>
     </div>
</div>

@include('admin.staffs.modal-create')
@include('admin.staffs.modal-edit')
@include('admin.staffs.modal-delete')
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/staffs.css') }}">
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/js/staffs.js') }}?v={{ time() }}"></script>
<script>
     document.addEventListener('DOMContentLoaded', function() {
          @if($errors -> any())
          @if(old('_method') == 'PUT')
          var editModal = new bootstrap.Modal(document.getElementById('editStaffModal'));
          editModal.show();
          @else
          var createModal = new bootstrap.Modal(document.getElementById('createStaffModal'));
          createModal.show();
          @endif
          @endif
     });
</script>
@endpush