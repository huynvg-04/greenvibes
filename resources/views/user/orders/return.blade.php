@extends('layouts.app')
@section('title', 'Yêu cầu hoàn hàng')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/order.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product.css') }}">

    <div class="orders-container">
        <nav class="breadcrumb-wrapper">
            <ul class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('user.orders.index') }}">Đơn hàng</a>
                </li>
                <li class="breadcrumb-item active">
                    Yêu cầu hoàn hàng #{{ $order->code }}
                </li>
            </ul>
        </nav>

        <div class="row justify-content-center" style="margin-top: 20px;">
            <div class="col-lg-10">
                @if ($errors->any())
                    <div class="alert-modern alert-error alert-auto shadow-sm bg-white">
                        <i class="fas fa-exclamation-circle alert-icon"></i>
                        <div class="alert-text">
                            <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <button type="button" class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                @endif

                <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                    <div class="card-body p-4">
                        <form action="{{ route('user.orders.return.store', $order) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <h5 class="details-title fw-bold mb-4">1. Chọn sản phẩm cần trả <span
                                    class="text-danger">*</span></h5>

                            <div class="items-list rounded-3 mb-4">
                                @foreach($order->items as $item)
                                    @php
                                        $product = $item->product;
                                        $variant = $item->variant;
                                        $img = asset('images/no-image.png');
                                        if ($variant && $variant->image) {
                                            $img = asset('storage/' . $variant->image);
                                        } elseif ($product && $product->primaryImage) {
                                            $img = asset('storage/' . $product->primaryImage->image_url);
                                        } elseif ($product && $product->images->first()) {
                                            $img = asset('storage/' . $product->images->first()->image_url);
                                        }
                                    @endphp
                                    <div class="order-item-wrapper bg-white p-3 rounded-3 border mb-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="form-check">
                                                <input class="form-check-input item-check" type="checkbox"
                                                    name="items[{{ $item->id }}]" value="{{ $item->quantity }}"
                                                    id="item_{{ $item->id }}"
                                                    style="width: 20px; height: 20px; cursor: pointer;">
                                            </div>

                                            <div class="item-image" style="width: 60px; height: 60px; flex-shrink: 0;">
                                                <img src="{{ $img }}" class="rounded border w-100 h-100 object-fit-cover">
                                            </div>

                                            <div class="flex-grow-1">
                                                <label class="item-name d-block cursor-pointer mb-1" for="item_{{ $item->id }}">
                                                    {{ $item->product_name ?? 'Sản phẩm' }}
                                                </label>
                                                <div class="item-meta">
                                                    <span>Đã mua: {{ $item->quantity }}</span>
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column align-items-end gap-2">
                                                <span class="item-subtotal">{{ number_format($item->price) }}₫</span>

                                                <div class="input-group input-group-sm" style="width: 110px;">
                                                    <span class="input-group-text bg-light border-secondary">Trả</span>
                                                    <input type="number" name="quantities[{{ $item->id }}]"
                                                        class="form-control text-center border-secondary"
                                                        value="{{ $item->quantity }}" min="1" max="{{ $item->quantity }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                @error('items') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                            </div>

                            <h5 class="details-title fw-bold mb-3">2. Thông tin hoàn hàng <span class="text-danger">*</span>
                            </h5>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Lý do hoàn hàng</label>
                                <select name="reason" class="form-select filter-select" style="height: 45px;" required>
                                    <option value="">-- Chọn lý do --</option>
                                    <option value="Cây gãy / Dập nát / Héo">Cây bị gãy / Dập nát / Héo úa</option>
                                    <option value="Cây chết / Thối rễ">Cây bị chết / Thối rễ / Vàng lá</option>
                                    <option value="Sai loại cây / Kích thước">Giao sai loại cây / Sai kích thước (Size)
                                    </option>
                                    <option value="Không giống mô tả">Cây thực tế khác xa mô tả / Ảnh</option>
                                    <option value="Vỡ chậu / Thiếu phụ kiện">Bể vỡ chậu / Thiếu phụ kiện</option>
                                    <option value="Khác">Lý do khác</option>
                                </select>
                                @error('reason') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Mô tả chi tiết</label>
                                <textarea name="description" class="form-control" rows="4"
                                    style="border: 2px solid #e2e8f0; border-radius: 8px;"
                                    placeholder="Vui lòng mô tả rõ tình trạng hàng hóa...">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium">Hình ảnh bằng chứng (Tối đa 3 ảnh)</label>
                                <input type="file" name="images[]" class="form-control" multiple accept="image/*"
                                    style="border: 2px solid #e2e8f0; border-radius: 8px; padding: 10px;">
                                <small class="text-muted mt-2 d-block">Cung cấp hình ảnh rõ nét để được duyệt nhanh
                                    hơn.</small>
                                @error('images.*') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <hr class="my-4">

                            <div class="d-flex justify-content-end gap-3">
                                <a href="{{ route('user.orders.index') }}"
                                    class="btn-secondary px-4 py-2 h-auto rounded-3">Hủy bỏ</a>
                                <button type="submit" class="btn-danger px-4 py-2 h-auto rounded-3 border-0">Gửi Yêu
                                    Cầu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('input[name^="quantities"]').forEach(input => {
            input.addEventListener('change', function () {
                const row = this.closest('.order-item-wrapper');
                const checkbox = row.querySelector('input[type="checkbox"]');
                checkbox.checked = true;
            });
        });
    </script>
@endsection