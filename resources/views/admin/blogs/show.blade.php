@extends('layouts.admin')
@section('title', 'Chi tiết bài viết')

@section('content')
<div class="container-fluid px-0">

    <div class="mb-4">
        <div class="d-flex align-items-center">
            <div class="text-body border-left-4 ">
                <h3 class="fw-bold mb-1 ps-4">
                    Nội dung bài viết
                </h3>
            </div>

            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('admin.blogs.index') }}"
                    class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
                    Trở lại
                </a>

                @can('update', $blog)
                <a href="{{ route('admin.blogs.edit', $blog->id) }}"
                    class="btn btn-primary btn-create d-flex align-items-center gap-2 px-4 py-2 fw-bold">
                    Sửa bài viết
                </a>
                @endcan
            </div>
        </div>

    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class=" bg-card border-0 shadow-sm rounded-4 overflow-hidden">
                @if($blog->thumbnail)
                <div class="position-relative" style="height: 350px; background-color: #f8f9fa;">
                    <img src="{{ asset('storage/' . $blog->thumbnail) }}"
                        class="w-100 h-100 object-fit-cover"
                        alt="{{ $blog->title }}">
                </div>
                @else
                <div class="d-flex align-items-center justify-content-center bg-light text-body" style="height: 200px;">
                    <div class="text-center">
                        <i class='bx bx-image-alt fs-1 opacity-50'></i>
                        <p class="mb-0 small">Không có ảnh đại diện</p>
                    </div>
                </div>
                @endif

                <div class="card-body p-4 p-md-5">
                    <h1 class="fw-bold text-body mb-4" style="font-size: 2rem; line-height: 1.4;">
                        {{ $blog->title }}
                    </h1>

                    <div class="blog-content text-body" style="font-size: 1.1rem; line-height: 1.8;">
                        {{-- Lưu ý: Nếu dùng CKEditor thì bỏ nl2br, chỉ dùng {!! $blog->content !!} --}}
                        {!! $blog->content !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="bg-card p-4 rounded-4 shadow-sm">
                    <h6 class="fw-bold text-uppercase text-body small mb-3">Thông tin xuất bản</h6>

                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="text-body">Trạng thái:</span>
                        @if($blog->is_published)
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 border border-success border-opacity-25">
                            Công khai
                        </span>
                        @else
                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2 border border-secondary border-opacity-25">
                            Bản nháp
                        </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="text-body d-block mb-1 small">Ngày tạo:</label>
                        <div class="d-flex align-items-center gap-2 fw-medium text-body">
                            <i class='bx bx-calendar'></i>
                            {{ $blog->created_at->format('d/m/Y') }}
                            <span class="text-body small">({{ $blog->created_at->format('H:i') }})</span>
                        </div>
                    </div>

                    <div>
                        <label class="text-body d-block mb-1 small">Cập nhật lần cuối:</label>
                        <div class="d-flex align-items-center gap-2 fw-medium text-body">
                            <i class='bx bx-time-five'></i>
                            {{ $blog->updated_at->format('d/m/Y') }}
                            <span class="text-body small">({{ $blog->updated_at->diffForHumans() }})</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-card border-0 shadow-sm rounded-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-uppercase text-body small mb-3">Tác giả</h6>
                    <div class="d-flex align-items-center gap-3">
                        <div>
                            <h6 class="fw-bold mb-0">{{ $blog->user->name ?? 'Admin' }}</h6>
                            <small class="text-body">{{ $blog->user->email ?? 'admin@system.com' }}</small>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection