@extends('layouts.admin')
@section('title', 'Chi tiết yêu cầu hoàn hàng #' . $return->order->code)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3 mb-3 mb-md-0 border-left-4">
            <h3 class="fw-bold text-body mb-1 ps-4">Yêu cầu hoàn hàng</h3>
            <small class="text-muted">Đơn hàng: <strong>{{ $return->order->code }}</strong></small>
        </div>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-light d-flex align-items-center gap-2 px-4 py-2">
            Quay lại danh sách
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-header bg-card p-4 mb-4 rounded-4 shadow-sm">
                <h6 class="m-0 font-weight-bold text-body">Sản phẩm yêu cầu hoàn trả</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive rounded-4 bg-card shadow-sm mb-4">
                    <table class="table modern-table align-middle mb-0 table-hover custom-table">
                        <thead class="bg-light text-dark">
                            <tr>
                                <th width="40%" class="ps-4 py-3 align-middle text-body small fw-bold text-uppercase">Sản phẩm / Phân loại</th>
                                <th class="text-center py-3 align-middle text-body small fw-bold text-uppercase">Đã mua</th>
                                <th class="text-center py-3 align-middle text-body small fw-bold text-uppercase">Hoàn trả</th>
                                <th class="text-end pe-4 py-3 align-middle text-body small fw-bold text-uppercase">Hoàn tiền (Dự kiến)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($return->items as $rItem)
                            @php
                            $orderItem = $rItem->orderItem;
                            $product = $orderItem?->product;
                            $productName = $orderItem->product_name ?? ($product->name ?? 'Sản phẩm đã xóa');
                            $variantInfo = $orderItem->product_variant_id ? ('Biến thể ID: ' . $orderItem->product_variant_id) : '';
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-body">{{ $productName }}</div>
                                    @if($variantInfo)
                                    <small class="text-muted"><i class="fas fa-tag fa-xs me-1"></i>{{ $variantInfo }}</small>
                                    @endif
                                </td>
                                <td class="text-center text-muted">{{ $orderItem->quantity ?? 0 }}</td>
                                <td class="text-center fw-bold text-danger" style="font-size: 1.1em;">
                                    {{ $rItem->quantity }}
                                </td>
                                <td class="text-end pe-4 fw-bold text-body">
                                    {{ number_format(($orderItem->price ?? 0) * $rItem->quantity) }}₫
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-card">
                            <tr>
                                <td colspan="3" class="text-end fw-bold pt-3 text-body">Tổng tiền hoàn dự kiến:</td>
                                <td class="text-end pe-4 fw-bold text-danger pt-3" style="font-size: 1.2em;">
                                    {{ number_format($return->refund_amount) }}₫
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


            <div class="card-header bg-card p-4 mb-4 rounded-4 shadow-sm">
                <h6 class="m-0 font-weight-bold text-body">Lý do & Bằng chứng</h6>
            </div>
            <div class="card-body card-header bg-card p-4 mb-4 rounded-4 shadow-sm">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="small text-uppercase fw-bold text-muted">Lý do khách chọn</label>
                        <div class="p-3 bg-light rounded border border-left-primary">
                            <i class="bx bx-quote-left text-body me-2"></i> {{ $return->reason }}
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="small text-uppercase fw-bold text-muted">Mô tả chi tiết</label>
                        <p class="mb-0 text-body">
                            {{ $return->description ?? 'Khách hàng không nhập mô tả chi tiết.' }}
                        </p>
                    </div>
                </div>

                <div>
                    <label class="small text-uppercase fw-bold text-muted">Hình ảnh đính kèm</label>
                    <div class="d-flex flex-wrap gap-3 mt-1">
                        @if($return->images && count($return->images) > 0)
                        @foreach($return->images as $img)
                        {{-- SỬA ĐOẠN NÀY --}}
                        <a href="javascript:void(0)"
                            class="position-relative d-block border rounded overflow-hidden shadow-sm hover-zoom view-image-btn"
                            data-bs-toggle="modal"
                            data-bs-target="#viewImageModal"
                            data-src="{{ asset('storage/' . $img) }}"> {{-- Truyền link ảnh vào đây --}}

                            <img src="{{ asset('storage/' . $img) }}"
                                class="object-fit-cover"
                                style="width: 120px; height: 120px;"
                                alt="Proof Image">

                            <div class="position-absolute bottom-0 start-0 w-100 bg-dark bg-opacity-50 text-white text-center py-1 small">
                                <i class="fas fa-search-plus"></i> Xem
                            </div>
                        </a>
                        {{-- KẾT THÚC ĐOẠN SỬA --}}
                        @endforeach
                        @else
                        <div class="text-muted fst-italic py-2">
                            <i class="far fa-image me-1"></i> Không có hình ảnh đính kèm.
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card-header bg-card p-4 mb-4 rounded-4 shadow-sm">
                <h6 class="m-0 font-weight-bold text-body">Thông tin phiếu</h6>
            </div>
            <div class="card-body card-header bg-card p-4 mb-4 rounded-4 shadow-sm">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-body">Ngày tạo</span>
                        <span class="fw-bold">{{ $return->created_at->format('d/m/Y H:i') }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-body">Khách hàng</span>
                        <span class="fw-bold">{{ $return->user->name ?? 'N/A' }}</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                        <span class="text-body">Trạng thái</span>
                        @if($return->status == 'pending')
                        <span class="badge bg-light-warning text-body border border-warning border-opacity-10 py-2 px-3 rounded-pill fw-normal font-monospace">Chờ xử lý</span>
                        @elseif($return->status == 'approved')
                        <span class="badge bg-light-success border border-success border-opacity-10 py-2 px-3 rounded-pill fw-normal font-monospace">Đã duyệt</span>
                        @elseif($return->status == 'rejected')
                        <span class="badge bg-light-danger border border-danger border-opacity-10 py-2 px-3 rounded-pill fw-normal font-monospace">Đã từ chối</span>
                        @endif
                    </li>
                </ul>

                @if($return->status != 'pending')
                <div class="{{ $return->status == 'approved' ? 'alert-success' : 'alert-danger' }} mt-3 mb-0">
                    <small class="d-block text-muted mb-2">
                        <i class="bx {{ $return->status == 'approved' ? 'bx-check' : 'bx-x' }} me-2"></i> Xử lý lúc: {{ $return->updated_at->format('d/m H:i') }}
                    </small>
                    <div class="border-top pt-2 mt-2 border-opacity-25" style="border-color: currentColor !important;">
                        <strong>Ghi chú:</strong> {{ $return->admin_note ?? 'Không có ghi chú' }}
                    </div>
                </div>
                @endif
            </div>


            @if($return->status == 'pending')
            <div class="card shadow mb-4 rounded-4 shadow-sm bg-card">
                <div class="card-header py-3 bg-warning text-dark d-flex align-items-center rounded-top-4">
                    <i class="fas fa-gavel me-2"></i>
                    <h6 class="m-0 font-weight-bold">Xử lý yêu cầu</h6>
                </div>
                <div class="card-body bg-card rounded-bottom-4">
                    <form action="{{ route('admin.returns.update', $return) }}" method="POST" id="processReturnForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase">Ghi chú xử lý <span class="text-danger">*</span></label>
                            <textarea name="admin_note" class="form-control" rows="4" required
                                placeholder="Nhập lý do duyệt hoặc từ chối để lưu vào hệ thống..."></textarea>
                            <div class="form-text text-muted small">Ghi chú này sẽ hiển thị cho khách hàng.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#approveModal">
                                <i class="fas fa-check-circle me-1"></i> Duyệt & Hoàn kho
                            </button>

                            <button type="button" class="btn btn-danger py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="fas fa-times-circle me-1"></i> Từ chối yêu cầu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>
<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header text-body border-0 rounded-top-4">
                <h5 class="modal-title fw-bold">Xác nhận Duyệt</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-success">
                    <i class="fas fa-box-open" style="font-size: 3rem;"></i>
                </div>
                <h6 class="fw-bold mb-3">DUYỆT yêu cầu này?</h6>
                <p class="text-muted small mb-0 bg-light p-3 rounded">
                    Hệ thống sẽ tự động <strong>CỘNG LẠI TỒN KHO</strong> cho các sản phẩm trong đơn hàng trả này.<br>
                    Hành động này không thể hoàn tác.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Hủy bỏ</button>
                
                <button type="submit" 
                        form="processReturnForm" 
                        name="action" 
                        value="approve" 
                        class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                    Xác nhận Duyệt
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white border-0 rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i>Xác nhận Từ chối</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-danger">
                    <i class="fas fa-ban" style="font-size: 3rem;"></i>
                </div>
                <h6 class="fw-bold mb-2">TỪ CHỐI yêu cầu này?</h6>
                <p class="text-muted small">
                    Yêu cầu sẽ bị hủy bỏ và kho hàng sẽ <strong>không thay đổi</strong>.<br>
                    Vui lòng đảm bảo đã nhập lý do từ chối.
                </p>
            </div>
            <div class="modal-footer border-0 justify-content-center pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Quay lại</button>
                <button type="submit" 
                        form="processReturnForm" 
                        name="action" 
                        value="reject" 
                        class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                    Xác nhận Từ chối
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="viewImageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-transparent border-0 shadow-none">
            <div class="modal-body p-0 text-center d-flex align-items-center justify-content-center">
                <img id="modal_image_target" src="" class="img-fluid rounded shadow-lg" style="max-height: 90vh;">
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/admin/js/returns.js') }}?v={{ time() }}"></script>
@endpush