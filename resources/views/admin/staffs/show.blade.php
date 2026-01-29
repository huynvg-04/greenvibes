@extends('layouts.admin')
@section('title', 'Chi tiết nhân viên')

@section('content')

    @php
        $profile = $staff->staffProfile;
    @endphp

    <div class="container-fluid">
        <div class="page-header d-flex align-items-center justify-content-between mb-4">
            <div class="header-content">
                <div class="header-info d-flex align-items-center">
                    <div class="mb-3 mb-md-0 border-left-4">
                        <h3 class="fw-bold text-body mb-1 ps-4">Hồ sơ nhân viên</h3>
                    </div>
                </div>
            </div>
            @can('viewAny', App\Models\StaffProfile::class)
                <a href="{{ route('admin.staffs.index') }}" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
                    Trở lại
                </a>
            @endcan
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
                Về trang chủ
            </a>

        </div>

        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="form-card bg-card h-100 text-center p-4 rounded-4 shadow-sm">
                    <div class="mb-3">
                        <div class="avatar-circle mx-auto bg-light d-flex align-items-center justify-content-center rounded-circle"
                            style="width: 100px; height: 100px;">
                            <i class="bx bx-user fs-1 text-secondary"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-1">{{ $profile->full_name ?? $staff->name }}</h4>
                    <span
                        class="badge {{ ($profile->status ?? '') == 'active' ? 'bg-light-success' : 'bg-light-secondary' }} rounded-pill border border-{{ ($profile->status ?? '') == 'active' ? 'success' : 'secondary' }} border-opacity-10 px-3 py-2 fw-normal font-monospace">
                        {{ ($profile->status ?? '') == 'active' ? 'Đang làm việc' : 'Đã nghỉ/Khác' }}
                    </span>

                    <hr class="my-4">

                    <div class="text-start">
                        <p class="mb-2 d-flex align-items-center"><i class="bx bx-envelope me-2 text-muted"></i>
                            {{ $staff->email }}</p>
                        <p class="mb-2 d-flex align-items-center"><i class="bx bx-phone me-2 text-muted"></i>
                            {{ $profile->phone ?? '---' }}</p>
                        <p class="mb-2 d-flex align-items-center"><i class="bx bx-building me-2 text-muted"></i> Chức vụ:
                            {{ $profile->position ?? '---' }}
                        </p>
                        <p class="mb-2 d-flex align-items-center"><i class="bx bx-calendar-alt me-2 text-muted"></i> Ngày
                            vào làm:
                            {{ $profile->start_date ? \Carbon\Carbon::parse($profile->start_date)->format('d/m/Y') : '---' }}
                        </p>
                    </div>

                    <div class="mt-4">
                        @can('update', $staff)
                            <a href="{{ route('admin.staffs.edit', $staff->id) }}"
                                class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 justify-content-center">
                                Chỉnh sửa hồ sơ
                            </a>
                        @endcan
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="h-100 bg-card p-4 rounded-4 shadow-sm">
                    <h5 class="section-title mb-4 pb-2 border-bottom text-uppercase text-body fw-bold small">
                        Phân quyền hệ thống
                    </h5>

                    @if($staff->hasRole('manager'))
                        <div class="text-center py-5">
                            <h5 class="fw-bold text-body">Tài khoản Quản lý (Manager)</h5>
                            <p class="text-muted px-5">
                                Tài khoản thuộc nhóm quản lý cấp cao có quyền truy cập, chỉnh sửa và quản lý toàn bộ các chức
                                năng trong hệ thống.
                            </p>
                            <div class="mt-3 d-flex align-items-center gap-2 justify-content-center">
                                <span class="badge bg-primary px-3 py-2 rounded-pill fs-6">
                                    <i class='bx bx-check-double me-1'></i> Full Access
                                </span>
                            </div>
                        </div>
                    @else
                        @if($staff->permissions->count() > 0)
                            <div class="alert-info border-0 bg-light-info text-info mb-4 p-3 rounded-4">
                                Được cấp riêng <strong>{{ $staff->permissions->count() }}</strong> quyền hạn cụ thể.
                            </div>

                            <div class="row g-3">
                                @foreach($staff->permissions as $permission)
                                    <div class="col-md-6">
                                        <div
                                            class="p-3 border rounded-4 bg-light d-flex align-items-center h-100 transition-all hover-shadow-sm">

                                            <div>
                                                <span class="fw-bold text-body d-block">
                                                    {{ $permission->display_name ?? ucfirst(str_replace('.', ' ', $permission->name)) }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="empty-state text-center py-5">
                                <div class="mb-3">
                                    <span class="fa-stack fa-2x">
                                        <i class="fas fa-circle fa-stack-2x text-light"></i>
                                        <i class="fas fa-user-lock fa-stack-1x text-muted"></i>
                                    </span>
                                </div>
                                <h6 class="text-muted fw-bold">Chưa có quyền hạn riêng</h6>
                                <p class="text-muted small mb-0 px-4">
                                    Nhân viên này chưa được cấp quyền hạn riêng lẻ nào. Họ chỉ sử dụng quyền mặc định của vai trò
                                    <strong>Staff</strong>.
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
    @include('admin.staffs.modal-edit')
@endsection