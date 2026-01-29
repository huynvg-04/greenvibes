@extends('layouts.admin')

@section('title', 'Quản lý đánh giá')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div class="mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Đánh giá sản phẩm</h3>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bxs-message-square-detail'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Tổng đánh giá</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($reviews->total()) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bxs-star'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Trung bình sao</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($averageRating ?? 0, 1) }} / 5.0</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 card-hover">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="avatar-circle bg-light-primary text-primary me-3 flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle" style="width: 56px; height: 56px; font-size: 28px;">
                        <i class='bx bxs-like'></i>
                    </div>
                    <div>
                        <p class="text-body text-uppercase fw-semibold small mb-1">Đánh giá 5 sao</p>
                        <h4 class="fw-bold text-body mb-0">{{ number_format($fiveStarCount ?? 0) }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-header bg-card p-3 mb-4 rounded-4 shadow-sm">

        <form action="{{ route('admin.reviews.index') }}" method="GET">
            @if(request('rating'))
            <input type="hidden" name="rating" value="{{ request('rating') }}">
            @endif
            @if(request('sort_direction'))
            <input type="hidden" name="sort_direction" value="{{ request('sort_direction') }}">
            @endif

            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div style="min-width: 250px;" class="flex-grow-1 flex-md-grow-0">
                    <div class="input-group bg-light rounded-pill px-3 py-1 border border-light focus-ring">
                        <span class="input-group-text bg-transparent border-0 pe-2 text-body">
                            <i class='bx bx-search'></i>
                        </span>
                        <input type="text" name="keyword"
                            class="form-control border-0 bg-transparent shadow-none text-body small"
                            placeholder="Tên khách, sản phẩm..."
                            value="{{ request('keyword') }}">
                    </div>
                </div>

                <div class="d-flex flex-wrap align-items-center gap-2 justify-content-end flex-grow-1">

                    @if(request()->anyFilled(['keyword', 'rating', 'month', 'year']))
                    <a href="{{ route('admin.reviews.index') }}"
                        class="btn btn-icon btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center text-danger hover-text-danger flex-shrink-0"
                        data-bs-toggle="tooltip" title="Xóa bộ lọc"
                        style="width: 36px; height: 36px;">
                        <i class='bx bx-refresh fs-5'></i>
                    </a>
                    @endif

                    <div class="bg-light rounded-pill p-1 d-inline-flex align-items-center overflow-auto shadow-sm" style="max-width: 100%; white-space: nowrap;">

                        <a href="{{ request()->fullUrlWithQuery(['rating' => null, 'page' => 1]) }}"
                            class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('rating') === null ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                            Tất cả
                        </a>

                        @for($i = 5; $i >= 1; $i--)
                        <a href="{{ request()->fullUrlWithQuery(['rating' => $i, 'page' => 1]) }}"
                            class="d-flex align-items-center justify-content-center btn btn-sm rounded-pill px-3 transition-all {{ request('rating') == $i ? 'bg-white shadow-sm text-body fw-bold' : 'text-muted border-0 hover-text-body' }}">
                            <span class="me-1">{{ $i }}</span> <i class='bx bxs-star {{ request('rating') == $i ? '' : 'text-secondary opacity-50' }}'></i>
                        </a>
                        @endfor
                    </div>

                    <div class="vr d-none d-xl-block mx-1 text-muted opacity-25" style="height: 40px;"></div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="position-relative">
                           
                            <select name="month"
                                class="form-select form-select-sm rounded-pill bg-light border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 me-1 w-auto"
                                style="appearance: none;"
                                onchange="this.form.submit()">
                                <option value="">Tháng</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>T.{{ $m }}</option>
                                    @endfor
                            </select>
                        </div>

                        <div class="position-relative">
                          
                            <select name="year"
                                class="form-select form-select-sm rounded-pill bg-light border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 me-1 w-auto"
                                style="appearance: none;"
                                onchange="this.form.submit()">
                                @foreach ($years as $y)
                                <option value="{{ $y }}" {{ request('year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="position-relative ms-1">
                           
                            <select name="sort_direction"
                                class="form-select form-select-sm rounded-pill bg-light border-0 shadow-sm cursor-pointer text-body fw-medium py-2 ps-3 me-1 w-auto"
                                style="appearance: none;"
                                onchange="this.form.submit()">
                                <option value="desc" {{ request('sort_direction') == 'desc' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
                            </select>
                        </div>

                    </div>

                </div>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive rounded-4 shadow-sm mb-4 bg-card" style="min-height: 400px;">
            <table class="table modern-table align-middle mb-0 table-hover custom-table">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-body small fw-bold text-uppercase" style="width: 60px;">ID</th>
                        <th class="py-3 text-body small fw-bold text-uppercase" style="width: 200px;">Sản phẩm</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Khách hàng</th>
                        <th class="py-3 text-body small fw-bold text-uppercase" style="width: 120px;">Đánh giá</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Nội dung</th>
                        <th class="py-3 text-body small fw-bold text-uppercase">Ngày tạo</th>
                        <th class="pe-4 py-3 text-center text-body small fw-bold text-uppercase">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                    <tr>
                        <td class="ps-4 text-body font-monospace">#{{ $review->id }}</td>
                        <td>
                            @if($review->orderItem && $review->orderItem->product)
                            <div class="d-flex align-items-center">
                                <a href="{{ route('admin.products.show', $review->orderItem->product->id) }}" class="fw-bold text-body text-decoration-none product-link text-truncate" style="max-width: 180px;" title="{{ $review->orderItem->product->name }}">
                                    {{ $review->orderItem->product->name }}
                                </a>
                            </div>
                            @else
                            <span class="badge bg-light-secondary text-secondary">Sản phẩm đã xóa</span>
                            @endif
                        </td>

                        <td>
                            @if($review->orderItem && $review->orderItem->order && $review->orderItem->order->user)
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-semibold text-body small">{{ $review->orderItem->order->user->name }}</span>
                            </div>
                            @else
                            <span class="text-body small fst-italic">Khách hàng đã xóa</span>
                            @endif
                        </td>

                        <td>
                            <div class="text-warning small" style="letter-spacing: -1px;">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class='bx {{ $i <= $review->rating ? 'bxs-star' : 'bx-star text-body opacity-25' }}'></i>
                                    @endfor
                            </div>
                        </td>

                        <td>
                            <span class="d-inline-block text-body small text-truncate" style="max-width: 300px;" title="{{ $review->comment }}">
                                {{ $review->comment ?? 'Không có bình luận' }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex flex-column">
                                <span class="text-body small fw-medium">{{ $review->created_at->format('d/m/Y') }}</span>
                                <span class="text-body" style="font-size: 10px;">{{ $review->created_at->format('H:i') }}</span>
                            </div>
                        </td>

                        <td class="pe-4 text-center">
                            @can('delete', $review)
                            <button type="button"
                                class="btn btn-icon btn-light-primary rounded-circle shadow-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#deleteModal"
                                data-review-id="{{ $review->id }}"
                                title="Xóa đánh giá">
                                <i class='bx bx-trash'></i>
                            </button>
                            @endcan
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <div class="py-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class='bx bx-message-square-x fs-1 text-body opacity-50'></i>
                                </div>
                                <h6 class="text-body fw-normal mb-0">Chưa có đánh giá nào.</h6>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($reviews->hasPages())
    <div class="card-footer bg-card border-top py-3 px-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
            <div class="text-body small">
                Hiển thị <span class="fw-bold text-body">{{ $reviews->firstItem() }}</span> - <span class="fw-bold text-body">{{ $reviews->lastItem() }}</span>
                trong tổng số <span class="fw-bold text-body">{{ $reviews->total() }}</span> đánh giá
            </div>
            <div>
                {{ $reviews->links('vendor.pagination.bootstrap-4') }}
            </div>
        </div>
    </div>
    @endif
</div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow rounded-4 overflow-hidden">
            <div class="modal-body text-center p-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle bg-light-danger text-danger" style="width: 64px; height: 64px; font-size: 32px;">
                    <i class='bx bx-trash'></i>
                </div>
                <h5 class="mb-2 fw-bold text-body">Xóa đánh giá này?</h5>
                <p class="text-body small mb-4">
                    Hành động này không thể hoàn tác.<br>
                    Đánh giá sẽ bị xóa vĩnh viễn khỏi hệ thống.
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
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/admin/css/reviews.css') }}">
@endpush
@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        const deleteModal = document.getElementById('deleteModal');
        if (deleteModal) {
            deleteModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const reviewId = button.getAttribute('data-review-id');
                document.getElementById('deleteForm').action = `/admin/reviews/${reviewId}`;
            });
        }
    });
</script>
@endsection