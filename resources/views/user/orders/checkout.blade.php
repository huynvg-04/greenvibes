@extends('layouts.app')
@section('title', 'Thanh toán')

@section('content')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
<div class="checkout-container">
    <div class="checkout-header">
        <h1 class="checkout-title">Thanh toán</h1>
    </div>
    <div class="row justify-content-center">
        @if(session('error'))
        <div class="col-12 mb-3">
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
        </div>
        @endif
        @if(session('success'))
        <div class="col-12 mb-3">
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        </div>
        @endif

        @if ($errors->any())
        <div class="col-12 mb-3">
            <div class="alert alert-warning">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif

        <div class="col-lg-7">
            <form action="{{ route('user.checkout.process') }}" method="POST" id="checkout-form">
                @csrf
                <input type="hidden" name="applied_coupon" value="{{ session('coupon_code') }}">
                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">1. Thông tin giao hàng</div>
                    <div class="card-body">

                        @php
                        $profileName = $user->customerProfile->full_name ?? $user->name;
                        $profilePhone = $user->customerProfile->phone ?? '';
                        $profileAddress = $user->customerProfile->address ?? '';
                        $hasProfile = !empty($profilePhone) && !empty($profileAddress);
                        @endphp

                        <div class="mb-4 p-3 bg-light rounded border">
                            <label class="form-label fw-bold mb-2">Bạn muốn giao hàng tới đâu?</label>
                            <div class="form-check mb-2">
                                <input class="form-check-input address-option" type="radio" name="address_option" id="addr_default" value="default"
                                    {{ $hasProfile ? 'checked' : '' }}
                                    {{ !$hasProfile ? 'disabled' : '' }}>
                                <label class="form-check-label" for="addr_default" style="cursor: pointer;">
                                    <strong>Sử dụng thông tin mặc định</strong>
                                    @if($hasProfile)
                                    <div class="text-muted small mt-1">
                                        <i class="fas fa-user me-1"></i> {{ $profileName }} - {{ $profilePhone }} <br>
                                        <i class="fas fa-map-marker-alt me-1"></i> {{ $profileAddress }}
                                    </div>
                                    @else
                                    <div class="text-danger small mt-1">
                                        (Bạn chưa cập nhật đầy đủ hồ sơ cá nhân)
                                    </div>
                                    @endif
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input address-option" type="radio" name="address_option" id="addr_new" value="new"
                                    {{ !$hasProfile ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="addr_new" style="cursor: pointer;">
                                    Giao đến địa chỉ khác
                                </label>
                            </div>
                        </div>

                        <div id="shipping-fields">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required fw-bold">Họ tên <span class="text-danger">*</span></label>
                                    <input type="text" name="fullname" id="fullname" class="form-control">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label required fw-bold">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control">
                                </div>
                            </div>

                            <input type="hidden" name="address" id="hidden_address_input">

                            <div id="new-address-area" style="display: none;">
                                <label class="form-label required fw-bold">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                                <div class="row g-2 mb-2">
                                    <div class="col-md-6">
                                        <select class="form-input-custom address-select" id="province" title="Chọn Tỉnh Thành">
                                            <option value="0">Tỉnh/Thành phố</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-input-custom address-select" id="ward" title="Chọn Phường/Xã">
                                            <option value="0">Phường/Xã</option>
                                        </select>
                                    </div>
                                </div>
                                <input type="text" id="specific_address" class="form-control" placeholder="Số nhà, tên đường, tòa nhà...">
                            </div>

                            <div id="default-address-area" class="mb-3">
                                <label class="form-label required fw-bold">Địa chỉ nhận hàng <span class="text-danger">*</span></label>
                                <input type="text" id="readonly_address" class="form-control bg-light" readonly>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="form-label fw-bold">Ghi chú</label>
                                <textarea name="note" class="form-control" rows="2" placeholder="Ghi chú thêm cho người giao hàng..." style="max-height:120px;">{{ old('note') }}</textarea>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-white fw-bold">2. Phương thức thanh toán</div>
                    <div class="card-body">
                        @if($paymentMethods->isEmpty())
                        <div class="alert alert-warning">Chưa có phương thức thanh toán nào được kích hoạt.</div>
                        @else
                        @foreach($paymentMethods as $method)
                        <div class="form-check mb-3 border p-3 {{ $loop->first ? 'border-primary bg-light' : '' }}">
                            <input class="form-check-input mt-2" type="radio" name="payment_method" id="payment_{{ $method->id }}" value="{{ $method->code }}" {{ $loop->first ? 'checked' : '' }}>
                            <label class="form-check-label w-100" for="payment_{{ $method->id }}" style="cursor: pointer;">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-wallet fa-2x color-accent me-3"></i>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $method->name }}</div>
                                        @if($method->description)
                                        <small class="text-muted">{{ $method->description }}</small>
                                        @endif
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-white fw-bold">3. Hình thức vận chuyển</div>
                    <div class="card-body">
                        @if($shippingRates->isEmpty())
                        <div class="alert alert-warning">Chưa có hình thức vận chuyển nào.</div>
                        @else
                        @foreach($shippingRates as $rate)
                        @if($subtotal >= $rate->min_order_value)
                        <div class="form-check mb-2 d-flex justify-content-between align-items-center">
                            <div>
                                <input class="form-check-input shipping-rate-input"
                                    type="radio"
                                    name="shipping_rate_id"
                                    id="shipping_{{ $rate->id }}"
                                    value="{{ $rate->id }}"
                                    data-fee="{{ $rate->fee }}"
                                    {{ $loop->first ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="shipping_{{ $rate->id }}">
                                    {{ $rate->name }}
                                    <span class="text-muted small">({{ $rate->estimated_days }} ngày)</span>
                                </label>
                            </div>
                            <div class="fw-bold">{{ $rate->fee == 0 ? 'Miễn phí' : number_format($rate->fee, 0, ',', '.') . '₫' }}</div>
                        </div>
                        @endif
                        @endforeach
                        @endif
                    </div>
                </div>

            </form>
        </div>

        <div class="col-lg-4">
            <div class="checkout-sticky">
                <div class="card shadow-sm" style="z-index: 1;">
                    <div class="card-header bg-white fw-bold">Thông tin đơn hàng</div>
                    <div class="card-body decorative-top">
                        <div class="checkout-items mb-3" style="max-height: 300px; overflow-y: auto;">
                            @foreach($cartItems as $item)
                            @php
                            $price = $item->variant ? $item->variant->list_price : $item->product->price;
                            if ($item->product->promotion_price) $price = $item->product->promotion_price;

                            $variantInfo = $item->variant ? $item->variant->attributeValues->map(fn($v) => $v->value)->join(' / ') : '';

                            $img = asset('images/no-image.png');
                            if ($item->variant && $item->variant->image) {
                            $img = asset('storage/' . $item->variant->image);
                            } elseif ($item->product->primaryImage) {
                            $img = asset('storage/' . $item->product->primaryImage->image_url);
                            } elseif ($item->product->images->isNotEmpty()) {
                            $img = asset('storage/' . $item->product->images->first()->image_url);
                            }
                            @endphp
                            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $img }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 0px;" class="me-2">
                                    <div>
                                        <div class="fw-bold text-truncate" style="max-width: 180px;">{{ $item->product->name }}</div>
                                        @if($variantInfo)
                                        <div class="d-flex align-items-center gap-1">
                                            <small class="text-muted" style="font-size: 0.85em;">{{ $variantInfo }}</small> <br> @endif
                                            <small>x {{ $item->quantity }}</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="fw-bold">{{ number_format($price * $item->quantity, 0, ',', '.') }}₫</div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Mã giảm giá</label>
                            @if($coupon)
                            <div class="alert alert-success d-flex justify-content-between align-items-center p-2 small mb-0 w-100">
                                <div>
                                    <i class="fas fa-tag me-1"></i> <strong>{{ $coupon->code }}</strong>
                                    <div class="text-muted" style="font-size: 0.85em;">
                                        @if($coupon->type == 'percent')
                                        -{{ (int)$coupon->value }}% (Tối đa {{ number_format($coupon->max_discount_value ?? 0, 0, ',', '.') }}₫)
                                        @else
                                        -{{ number_format($coupon->value, 0, ',', '.') }}₫
                                        @endif
                                    </div>
                                </div>
                                <form action="{{ route('user.checkout.remove_coupon') }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-close btn-sm" title="Gỡ mã">
                                        <i class="bx bx-x" style="color: black;"></i>
                                    </button>
                                </form>
                            </div>
                            @else
                            <form action="{{ route('user.checkout.apply_coupon') }}" method="POST" class="input-group apply-coupon-form">
                                @csrf
                                <input type="text" name="code" class="form-control form-control-sm" placeholder="Nhập mã" required>
                                <button class="btn btn-apply-counpon" type="submit">Áp dụng</button>
                            </form>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span class="fw-bold" id="subtotal-display" data-value="{{ $subtotal }}">{{ number_format($subtotal, 0, ',', '.') }}₫</span>
                        </div>

                        @if(isset($couponDiscount) && $couponDiscount > 0)
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>
                                <i class="fas fa-ticket-alt me-1"></i> Coupon ({{ $couponCode }}):
                            </span>
                            <span id="coupon-discount-display" data-value="{{ $couponDiscount }}">-{{ number_format($couponDiscount, 0, ',', '.') }}₫</span>
                        </div>
                        @endif

                        @if(isset($tierDiscount) && $tierDiscount > 0)
                        <div class="tier-discount d-flex justify-content-between mb-2">
                            <span> Ưu đãi hạng thành viên
                                {{ $activeTier->name ?? 'Thành viên' }}:
                            </span>
                            <span id="tier-discount-display" data-value="{{ $tierDiscount }}">-{{ number_format($tierDiscount, 0, ',', '.') }}₫</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span id="shipping-fee-display" class="text-muted">0₫</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <strong class="fs-5">Tổng thanh toán:</strong>
                            <strong class="fs-4 text-danger" id="total-display">{{ number_format($total, 0, ',', '.') }}₫</strong>
                        </div>

                        <button type="submit" form="checkout-form" class="btn btn-primary submit-checkout w-100 py-2 fw-bold text-uppercase px-3">
                            HOÀN TẤT ĐẶT HÀNG
                        </button>

                        <div class="text-center mt-2">
                            <a href="{{ route('user.cart.index') }}" class="continue-btn">
                                <i class='bx bx-arrow-back'></i>
                                Quay lại giỏ hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profileData = {
            fullname: "{{ $profileName }}",
            phone: "{{ $profilePhone }}",
            address: "{{ $profileAddress }}"
        };

        const fullnameInput = document.getElementById('fullname');
        const phoneInput = document.getElementById('phone');
        const hiddenAddressInput = document.getElementById('hidden_address_input');
        const readonlyAddressInput = document.getElementById('readonly_address');
        const addressOptions = document.querySelectorAll('.address-option');
        const newAddressArea = document.getElementById('new-address-area');
        const defaultAddressArea = document.getElementById('default-address-area');

        function handleAddressOption() {
            const selected = document.querySelector('.address-option:checked').value;

            if (selected === 'default') {
                fullnameInput.value = profileData.fullname;
                phoneInput.value = profileData.phone;
                fullnameInput.setAttribute('readonly', true);
                fullnameInput.classList.add('bg-light');
                phoneInput.classList.add('bg-light');
                newAddressArea.style.display = 'none';
                defaultAddressArea.style.display = 'block';
                hiddenAddressInput.value = profileData.address;
                readonlyAddressInput.value = profileData.address;
            } else {
                @if(!old('fullname')) fullnameInput.value = '';
                @endif
                @if(!old('phone')) phoneInput.value = '';
                @endif
                fullnameInput.removeAttribute('readonly');
                phoneInput.removeAttribute('readonly');
                fullnameInput.classList.remove('bg-light');
                phoneInput.classList.remove('bg-light');
                newAddressArea.style.display = 'block';
                defaultAddressArea.style.display = 'none';
                hiddenAddressInput.value = '';
                fullnameInput.focus();
            }
        }
        addressOptions.forEach(radio => radio.addEventListener('change', handleAddressOption));

        const provinceSelect = document.getElementById('province');
        const wardSelect = document.getElementById('ward');
        const specificAddress = document.getElementById('specific_address');
        let provincesData = [];

        function updateHiddenAddress() {
            const p = provinceSelect.options[provinceSelect.selectedIndex]?.text;
            const w = wardSelect.options[wardSelect.selectedIndex]?.text;
            const s = specificAddress.value.trim();
            if (p && w && s && p !== 'Tỉnh / Thành phố' && w !== 'Phường / Xã') {
                hiddenAddressInput.value = `${s}, ${w}, ${p}`;
            } else {
                hiddenAddressInput.value = '';
            }
        }

        fetch('https://esgoo.net/api-tinhthanh-new/4/0.htm')
            .then(res => res.json())
            .then(json => {
                if (json.error === 0) {
                    provincesData = json.data;
                    provincesData.forEach(province => {
                        provinceSelect.add(new Option(province.full_name, province.id));
                    });
                }
            });

        provinceSelect.addEventListener('change', function() {
            wardSelect.length = 1;
            const provinceId = this.value;
            if (!provinceId) return;
            const province = provincesData.find(p => p.id === provinceId);
            if (!province || !province.data2) return;
            province.data2.forEach(ward => {
                wardSelect.add(new Option(ward.full_name, ward.id));
            });
            updateHiddenAddress();
        });
        wardSelect.addEventListener('change', updateHiddenAddress);
        specificAddress.addEventListener('input', updateHiddenAddress);
        handleAddressOption();

        const shippingInputs = document.querySelectorAll('.shipping-rate-input');

        const subtotalEl = document.getElementById('subtotal-display');
        const subtotal = subtotalEl ? parseFloat(subtotalEl.dataset.value) : 0;

        const couponEl = document.getElementById('coupon-discount-display');
        const couponDiscount = couponEl ? parseFloat(couponEl.dataset.value) : 0;

        const tierEl = document.getElementById('tier-discount-display');
        const tierDiscount = tierEl ? parseFloat(tierEl.dataset.value) : 0;

        function updateTotal() {
            let shippingFee = 0;
            const selectedShipping = document.querySelector('.shipping-rate-input:checked');

            if (selectedShipping) {
                shippingFee = parseFloat(selectedShipping.dataset.fee);
            }

            const feeText = shippingFee === 0 ? 'Miễn phí' : new Intl.NumberFormat('vi-VN').format(shippingFee) + '₫';
            const shipDisplay = document.getElementById('shipping-fee-display');
            if (shipDisplay) shipDisplay.innerText = feeText;


            let total = subtotal - couponDiscount - tierDiscount + shippingFee;

            if (total < 0) total = 0;

            const totalDisplay = document.getElementById('total-display');
            if (totalDisplay) totalDisplay.innerText = new Intl.NumberFormat('vi-VN').format(total) + '₫';
        }

        shippingInputs.forEach(input => input.addEventListener('change', updateTotal));

        updateTotal();

        const paymentInputs = document.querySelectorAll('input[name="payment_method"]');
        paymentInputs.forEach(input => {
            input.addEventListener('change', function() {
                document.querySelectorAll('input[name="payment_method"]').forEach(el => {
                    el.closest('.border').classList.remove('border-primary', 'bg-light');
                });
                if (this.checked) {
                    this.closest('.border').classList.add('border-primary', 'bg-light');
                }
            });
        });
    });

    document.addEventListener("DOMContentLoaded", function() {
        const checkoutForm = document.getElementById("checkout-form");
        const checkoutBtn = document.getElementById("btn-confirm") || document.querySelector("button[form='checkout-form']");

        const couponForms = document.querySelectorAll('.apply-coupon-form');
        couponForms.forEach(form => {
            form.addEventListener('submit', function(e) {
                const btn = this.querySelector('.btn-apply-counpon');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="btn-spinner"></span>';
                }
            });
        });

        if (checkoutForm && checkoutBtn) {
            checkoutForm.addEventListener("submit", function(e) {

                if (!checkoutForm.checkValidity()) {
                    return;
                }
                if (checkoutBtn.disabled) {
                    e.preventDefault();
                    return;
                }
                checkoutBtn.disabled = true;
                checkoutBtn.setAttribute("data-original-text", checkoutBtn.innerHTML);

                checkoutBtn.innerHTML =
                    '<span class="btn-spinner"></span> Đang xử lý...';
            });
        }
    });
</script>
@endsection