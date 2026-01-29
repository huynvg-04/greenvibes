@extends('layouts.app')
@section('title', 'Xác nhận đơn hàng')
@section('content')
<div class="order-confirmation-page">
    <!-- Progress Indicator -->
    <div class="progress-indicator">
        <div class="container px-0">
            <div class="progress-steps">
                <div class="step completed">
                    <div class="step-circle">✓</div>
                    <span class="step-label">Giỏ hàng</span>
                </div>
                <div class="step active">
                    <div class="step-circle">2</div>
                    <span class="step-label">Xác nhận đơn hàng</span>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <span class="step-label">Hoàn thành</span>
                </div>
            </div>
        </div>
    </div>

    <div class="container px-0">
        <div class="order-layout">
            <!-- Order Summary Section -->
            <div class="order-summary">
                <div class="summary-card">
                    <div class="card-header">
                        <h2 class="section-title">Thông tin đơn hàng</h2>
                        <div class="order-badge">
                            <span class="badge-text">Đơn hàng mới</span>
                        </div>
                    </div>

                    <div class="order-items" id="orderItemsContainer">
                    </div>

                    <div class="order-summary-footer">
                        <div class="total-row">
                            <span class="total-label">Tổng cộng:</span>
                            <span class="total-amount" id="orderTotal">0₫</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="delivery-form">
                <div class="form-card">
                    <div class="card-header">
                        <h2 class="section-title">Thông tin giao hàng</h2>
                        <p class="section-description">Vui lòng cung cấp địa chỉ giao hàng chính xác</p>
                    </div>

                    <form method="POST" action="{{ route('user.orders.store') }}" class="delivery-form-content">
                        @csrf
                        <div id="hiddenInputs"></div>
                        <input type="hidden" name="full_address" id="full_address">
                        <div class="form-group">
                            <label class="form-label" for="city">
                                Tỉnh/Thành phố
                                <span class="required-indicator">*</span>
                            </label>
                            <select class="form-input" id="city" name="city" required>
                                <option value="" disabled selected>-- Chọn Tỉnh/Thành phố --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="ward">
                                Phường/Xã
                                <span class="required-indicator">*</span>
                            </label>
                            <select class="form-input" id="ward" name="ward" required>
                                <option value="" disabled selected>-- Chọn Phường/Xã --</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="address">
                                Số nhà, tên đường
                                <span class="required-indicator">*</span>
                            </label>
                            <textarea
                                class="form-input"
                                id="address"
                                name="address"
                                rows="2"
                                placeholder="Nhập số nhà, tên đường..."
                                required></textarea>
                        </div>
                        <div class="form-actions">
                            <button type="button" class="btn-back" onclick="history.back()">
                                Quay lại giỏ hàng
                            </button>
                            <button type="submit" class="btn-confirm">
                                <span class="btn-icon"></span>
                                Đặt hàng
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Green Vibes Order Confirmation Page Styles */
    .order-confirmation-page {
        padding: 0;
        margin: 0;
    }

    /* Progress Indicator */
    .progress-indicator {
        background: var(--color-white);
        border-bottom: 1px solid var(--color-light);
        padding: var(--space-lg) 0;
        margin-bottom: var(--space-xl);
        box-shadow: var(--shadow-light);
    }

    .progress-steps {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: var(--space-xxl);
        max-width: 600px;
        margin: 0 auto;
    }

    .step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: var(--space-xs);
        position: relative;
    }

    .step:not(:last-child)::after {
        content: '';
        position: absolute;
        top: 20px;
        right: -calc(var(--space-xxl) / 2);
        width: var(--space-xxl);
        height: 2px;
        background: var(--color-light);
        z-index: 1;
    }

    .step.completed::after,
    .step.active::after {
        background: var(--color-accent);
    }

    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--color-light);
        color: var(--color-muted);
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: var(--font-ui);
        font-weight: 600;
        font-size: var(--type-small);
        position: relative;
        z-index: 2;
        transition: var(--btn-transition);
    }

    .step.completed .step-circle {
        background: var(--color-success);
        color: var(--color-white);
    }

    .step.active .step-circle {
        background: var(--color-accent);
        color: var(--color-white);
        transform: scale(1.1);
        box-shadow: 0 4px 12px rgba(243, 156, 18, 0.3);
    }

    .step-label {
        font-family: var(--font-ui);
        font-size: var(--type-caption);
        font-weight: 500;
        color: var(--color-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .step.completed .step-label,
    .step.active .step-label {
        color: var(--color-primary);
        font-weight: 600;
    }

    /* Order Layout */
    .order-layout {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: var(--space-xl);
        align-items: start;
    }

    /* Order Summary */
    .summary-card,
    .form-card {
        background: var(--color-white);
        border: 1px solid var(--color-light);
        border-radius: var(--btn-radius);
        box-shadow: var(--shadow-light);
        overflow: hidden;
    }

    .card-header {
        background: var(--color-light);
        padding: var(--space-lg);
        border-bottom: 1px solid rgba(44, 62, 80, 0.1);
    }

    .section-title {
        font-family: var(--font-ui);
        font-size: var(--type-h3);
        font-weight: 400;
        color: var(--color-primary);
        margin: 0 0 var(--space-xs) 0;
        letter-spacing: 0.5px;
    }

    .section-description {
        font-family: var(--font-body);
        font-size: var(--type-small);
        color: var(--color-muted);
        margin: 0;
    }

    .order-badge {
        margin-top: var(--space-sm);
    }

    .badge-text {
        display: inline-block;
        background: rgba(39, 174, 96, 0.1);
        color: var(--color-success);
        padding: 4px var(--space-sm);
        border-radius: var(--btn-radius);
        font-family: var(--font-ui);
        font-size: var(--type-caption);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Order Items */
    .order-items {
        padding: var(--space-lg);
        max-height: 400px;
        overflow-y: auto;
    }

    .order-item {
        display: grid;
        grid-template-columns: 300px 1fr auto;
        gap: var(--space-sm);
        padding: var(--space-sm) 0;
        border-bottom: 1px solid var(--color-light);
        align-items: center;
    }

    .order-item:last-child {
        border-bottom: none;
    }

    .item-image {
        width: 60px;
        height: 60px;
        border-radius: var(--btn-radius);
        object-fit: cover;
        background: var(--color-light);
    }

    .item-details {
        min-width: 0;
    }

    .item-name {
        font-family: var(--font-body);
        font-size: var(--type-small);
        font-weight: 600;
        color: var(--color-primary);
        margin: 0 0 var(--space-xs) 0;
        line-height: 1.4;
    }

    .item-meta {
        font-family: var(--font-body);
        font-size: var(--type-caption);
        color: var(--color-muted);
        margin: 0;
    }

    .item-price {
        text-align: right;
    }

    .item-unit-price {
        font-family: var(--font-ui);
        font-size: var(--type-small);
        color: var(--color-muted);
        display: block;
        margin-bottom: var(--space-xs);
    }

    .item-total {
        font-family: var(--font-ui);
        font-size: var(--type-base);
        font-weight: 600;
        color: var(--color-accent);
    }

    /* Order Summary Footer */
    .order-summary-footer {
        padding: var(--space-lg);
        background: rgba(243, 156, 18, 0.05);
        border-top: 1px solid var(--color-light);
    }

    .total-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: var(--space-sm) 0;
        border-top: 2px solid var(--color-accent);
        border-bottom: 2px solid var(--color-accent);
    }

    .total-label {
        font-family: var(--font-ui);
        font-size: var(--type-base);
        font-weight: 600;
        color: var(--color-primary);
    }

    .total-amount {
        font-family: var(--font-ui);
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--color-accent);
    }

    .delivery-form-content {
        padding: var(--space-lg);
    }

    .form-group {
        margin-bottom: var(--space-sm);
    }

    .form-label {
        display: block;
        font-family: var(--font-ui);
        font-size: var(--type-small);
        font-weight: 600;
        color: var(--color-primary);
        margin-bottom: var(--space-xs);
    }

    .required-indicator {
        color: var(--color-error);
        font-weight: 700;
    }

    .form-input {
        width: 100%;
        padding: var(--space-xs);
        border: 2px solid var(--color-light);
        border-radius: var(--btn-radius);
        font-family: var(--font-body);
        font-size: var(--type-base);
        color: var(--color-dark);
        background: var(--color-white);
        transition: var(--btn-transition);
        resize: vertical;
    }

    .form-input:focus {
        border-color: var(--color-accent);
        box-shadow: var(--color-accent);
    }

    .form-input::placeholder {
        color: var(--color-muted);
        font-style: italic;
    }

    .form-hint {
        font-family: var(--font-body);
        font-size: var(--type-caption);
        color: var(--color-muted);
        margin-top: var(--space-xs);
        line-height: 1.5;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        gap: var(--space-sm);
        justify-content: space-between;
        padding-top: var(--space-lg);
        border-top: 1px solid var(--color-light);
    }

    .btn-confirm,
    .btn-back {
        padding: var(--btn-padding);
        border-radius: var(--btn-radius);
        font-family: var(--font-ui);
        font-weight: 500;
        cursor: pointer;
        transition: var(--btn-transition);
        display: inline-flex;
        align-items: center;
        gap: var(--space-xs);
        text-decoration: none;
        border: 2px solid;
    }

    .btn-confirm {
        background: transparent;
        color: var(--color-accent);
        border-color: var(--color-accent);
        flex: 1;
        justify-content: center;
    }

    .btn-confirm:hover {
        border-color: var(--color-accent);
        background: var(--color-accent);
        color: var(--color-white);
    }

    .btn-confirm:focus {
        border-color: var(--color-accent);
        box-shadow: var(--color-accent);
    }

    .btn-back {
        background: transparent;
        color: var(--color-muted);
        border-color: var(--color-light);
        padding: 5px;
    }

    .btn-back:hover {
        background: var(--color-light);
        color: var(--color-dark);
        text-decoration: none;
    }

    .btn-back:focus {
        border-color: var(--color-accent);
        box-shadow: var(--color-accent);
    }

    .btn-icon {
        font-size: 1.1rem;
    }

    /* Loading & Error States */
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .error-message {
        background: rgba(231, 76, 60, 0.1);
        color: var(--color-error);
        padding: var(--space-sm);
        border-radius: var(--btn-radius);
        border-left: 4px solid var(--color-error);
        font-family: var(--font-body);
        font-size: var(--type-small);
        margin-top: var(--space-sm);
    }

    /* Empty State */
    .empty-order {
        text-align: center;
        padding: var(--space-xxl);
        color: var(--color-muted);
    }

    .empty-order i {
        font-size: 3rem;
        margin-bottom: var(--space-md);
        color: var(--color-light);
    }

    /* Responsive Design */
    @media (max-width: 767px) {
        .progress-steps {
            gap: var(--space-lg);
        }

        .step:not(:last-child)::after {
            right: -calc(var(--space-lg) / 2);
            width: var(--space-lg);
        }

        .step-circle {
            width: 32px;
            height: 32px;
            font-size: 0.75rem;
        }

        .order-layout {
            grid-template-columns: 1fr;
            gap: var(--space-lg);
        }

        .order-summary {
            order: 2;
        }

        .delivery-form {
            order: 1;
        }

        .order-item {
            grid-template-columns: 50px 1fr auto;
        }

        .item-image {
            width: 50px;
            height: 50px;
        }

        .form-actions {
            flex-direction: column;
            gap: var(--space-sm);
        }

        .total-amount {
            font-size: 1.25rem;
        }
    }

    @media (min-width: 768px) and (max-width: 1023px) {
        .order-layout {
            grid-template-columns: 1fr 350px;
            gap: var(--space-lg);
        }

        .progress-steps {
            gap: var(--space-xl);
        }
    }

    /* Accessibility & Motion */
    @media (prefers-reduced-motion: reduce) {
        * {
            transition: none !important;
            animation: none !important;
        }

        .step.active .step-circle {
            transform: none;
        }

        .loading-spinner {
            animation: none;
        }
    }

    /* Print Styles */
    @media print {

        .progress-indicator,
        .form-actions {
            display: none;
        }

        .order-layout {
            grid-template-columns: 1fr;
        }

        .summary-card,
        .form-card {
            box-shadow: none;
            border: 1px solid var(--color-dark);
        }
    }

    /* High Contrast Mode */
    @media (prefers-contrast: high) {

        .step-circle,
        .form-input,
        .summary-card,
        .form-card {
            border-width: 2px;
        }

        .badge-text {
            border: 1px solid var(--color-success);
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(function() {

        showLoadingState();

        // Get order data from localStorage
        let orderDataString = localStorage.getItem("orderData");
        if (!orderDataString) {
            showEmptyState();
            return;
        }

        try {
            let orderData = JSON.parse(orderDataString);
            displayOrderItems(orderData);
            setupFormSubmission(orderData);
        } catch (error) {
            console.error("Error parsing order data:", error);
            showErrorState();
        }
    });

    function showLoadingState() {
        const loadingHtml = `
        <div class="order-item">
            <div class="loading-skeleton" style="height: 20px; width: 60%;"></div>
            <div class="loading-skeleton" style="height: 16px; width: 40px;"></div>
            <div class="loading-skeleton" style="height: 16px; width: 80px;"></div>
            <div class="loading-skeleton" style="height: 18px; width: 100px;"></div>
        </div>
    `;
        $("#orderItemsContainer").html(loadingHtml.repeat(2));
    }

    function showEmptyState() {
        $("#orderItemsContainer").html(`
        <div class="empty-state" style="text-align: center; padding: 48px 16px; color: #64748b;">
            <h3 style="color: #1e293b; margin-bottom: 8px;">Không tìm thấy đơn hàng</h3>
            <p>Vui lòng quay lại giỏ hàng để tiếp tục mua sắm.</p>
            <button onclick="history.back()" class="btn-confirm" style="margin-top: 16px;">
                ← Quay lại giỏ hàng
            </button>
        </div>
    `);
    }

    function showErrorState() {
        $("#orderItemsContainer").html(`
        <div class="error-state" style="text-align: center; padding: 48px 16px; color: #ef4444;">
            <div style="font-size: 48px; margin-bottom: 16px;">⚠️</div>
            <h3 style="color: #1e293b; margin-bottom: 8px;">Có lỗi xảy ra</h3>
            <p>Không thể tải thông tin đơn hàng. Vui lòng thử lại.</p>
        </div>
    `);
    }

    function displayOrderItems(orderData) {
        let itemsHtml = "";

        orderData.items.forEach(item => {
            itemsHtml += `
            <div class="order-item">
                <div class="item-name">${escapeHtml(item.name)}</div>
                <div class="item-quantity">SL: ${item.quantity}</div>
                <div class="item-price">${formatCurrency(item.price)}</div>
                <div class="item-subtotal">${formatCurrency(item.subtotal)}</div>
            </div>
        `;
        });

        // Animate in the content
        $("#orderItemsContainer").fadeOut(200, function() {
            $(this).html(itemsHtml).fadeIn(300);
        });

        $(".total-amount").fadeOut(200, function() {
            $("#orderTotal").text(formatCurrency(orderData.total)).fadeIn(300);
        });
    }

    function setupFormSubmission(orderData) {
        $("#hiddenInputs").html(`<input type="hidden" name="order_data" value='${JSON.stringify(orderData)}'>`);

        $("form").on("submit", function(e) {
            const submitBtn = $(this).find('button[type="submit"]');
            const form = $(this);

            form.addClass('form-submitted');

            // Update button state
            submitBtn.html(`
            <span class="btn-icon"><i class="fas fa-sync"></i></span>
            Đang xử lý...
        `).prop('disabled', true);

            localStorage.removeItem("orderData");

        });
    }

    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + '₫';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Add smooth scroll behavior
    document.documentElement.style.scrollBehavior = 'smooth';

    // Add loading animation for form validation
    $('textarea[required]').on('blur', function() {
        const value = $(this).val().trim();
        const parent = $(this).closest('.form-group');

        if (value.length === 0) {
            parent.addClass('error');
            if (!parent.find('.error-message').length) {
                $(this).after('<div class="error-message" style="color: #ef4444; font-size: 12px; margin-top: 4px;">Vui lòng nhập địa chỉ giao hàng</div>');
            }
        } else {
            parent.removeClass('error');
            parent.find('.error-message').remove();
        }
    });

    const vietnamData = [{
            "name": "Hà Nội",
            "wards": ["Phường Phúc Xá", "Phường Trúc Bạch", "Phường Vĩnh Phúc", "Phường Liễu Giai", "Phường Ba Đình", "Phường Hoàng Liệt"]
        },
        {
            "name": "Hồ Chí Minh",
            "wards": ["Phường Tân Định", "Phường Bến Nghé", "Phường Bến Thành", "Phường Đa Kao", "Phường Nguyễn Hữu Cảnh", "Phường 1"]
        },
        {
            "name": "Đà Nẵng",
            "wards": ["Phường Hải Châu 1", "Phường Hải Châu 2", "Phường Thạch Thang", "Phường Nước Ngọt", "Phường Mỹ An"]
        },
        {
            "name": "Hải Phòng",
            "wards": ["Phường Minh Khai", "Phường Hòn Gai", "Phường Máy Tơ", "Phường Quán Toan", "Phường Đông Hải"]
        },
        {
            "name": "An Giang",
            "wards": ["Phường Châu Đốc", "Phường Tân Phú", "Phường Tân Châu", "Phường Hòa Phú", "Phường Vĩnh Mỹ"]
        },
        {
            "name": "Bà Rịa - Vũng Tàu",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Rạch Dừa"]
        },
        {
            "name": "Bạc Liêu",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Đông Hải"]
        },
        {
            "name": "Bắc Giang",
            "wards": ["Phường Suối Hoa", "Phường Ngoại Thành", "Phường Yên Duyên", "Phường Hoàng Văn Thụ", "Phường Mỹ Phương"]
        },
        {
            "name": "Bắc Kạn",
            "wards": ["Phường Nông Cống", "Phường Cao Bằng", "Phường Ba Bể", "Phường Pác Nặm", "Phường Bác Phụng"]
        },
        {
            "name": "Bắc Ninh",
            "wards": ["Phường Kinh Bắc", "Phường Thành Công", "Phường Năm Sao", "Phường Võ Cường", "Phường Yên Phong"]
        },
        {
            "name": "Bến Tre",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Phú Tân"]
        },
        {
            "name": "Bình Dương",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Lái Thiêu"]
        },
        {
            "name": "Bình Phước",
            "wards": ["Phường Đồng Xoài", "Phường Bình Long", "Phường Bù Đốp", "Phường Chơn Thành", "Phường Bù Gia Mập"]
        },
        {
            "name": "Bình Thuận",
            "wards": ["Phường Lạc Sơn", "Phường Phú Hài", "Phường Thắng Lợi", "Phường Mũi Né", "Phường Hàm Tiến"]
        },
        {
            "name": "Cà Mau",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Nguyễn Văn Cừ"]
        },
        {
            "name": "Cao Bằng",
            "wards": ["Phường Quảng Hòa", "Phường Pác Nặm", "Phường Cao Bằng", "Phường Hòa An", "Phường Nguyễn Huệ"]
        },
        {
            "name": "Cần Thơ",
            "wards": ["Phường An Phú", "Phường Xuân Khánh", "Phường Tân An", "Phường Ninh Kiều", "Phường Hưng Phú"]
        },
        {
            "name": "Đắk Lắk",
            "wards": ["Phường Tây Sơn", "Phường Cam Ly", "Phường An Phú", "Phường Thống Nhất", "Phường Hoa Lư"]
        },
        {
            "name": "Đắk Nông",
            "wards": ["Phường Hòa Bình", "Phường Tân Lập", "Phường Thống Nhất", "Phường Đông Hòa", "Phường Tân Trang"]
        },
        {
            "name": "Điện Biên",
            "wards": ["Phường Điện Biên Phủ", "Phường Thanh Nông", "Phường Tân Yên", "Phường Hoàng Đế", "Phường Nậm Sai"]
        },
        {
            "name": "Đồng Nai",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Bình Chính"]
        },
        {
            "name": "Đồng Tháp",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Bình Minh"]
        },
        {
            "name": "Gia Lai",
            "wards": ["Phường Tây Sơn", "Phường An Phú", "Phường Hội Phú", "Phường Quảng Trung", "Phường Chánh Mỹ"]
        },
        {
            "name": "Hà Giang",
            "wards": ["Phường Quảng Hòa", "Phường Nguyễn Huệ", "Phường Minh Khai", "Phường Đông Hỷ", "Phường Yên Hòa"]
        },
        {
            "name": "Hà Nam",
            "wards": ["Phường Cồn", "Phường An Tây", "Phường Thanh Liệt", "Phường Trường Yên", "Phường Hồ Sơn"]
        },
        {
            "name": "Hà Tĩnh",
            "wards": ["Phường Hà Tĩnh", "Phường Xuân Phương", "Phường Hồng Lĩnh", "Phường Quảng Phong", "Phường Hương Sơn"]
        },
        {
            "name": "Hải Dương",
            "wards": ["Phường Lê Hồng Phong", "Phường Quang Trung", "Phường Cầu Dền", "Phường Kinh Dương Vương", "Phường Tương Mai"]
        },
        {
            "name": "Hậu Giang",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Vị Thủy"]
        },
        {
            "name": "Hòa Bình",
            "wards": ["Phường Hòa Bình", "Phường Phương Lâm", "Phường Mai Châu", "Phường Yên Phong", "Phường Tân Lạc"]
        },
        {
            "name": "Hưng Yên",
            "wards": ["Phường Hàng Mận", "Phường Vĩnh Phúc", "Phường Sơn Tây", "Phường Thanh Thủy", "Phường Minh Đàm"]
        },
        {
            "name": "Khánh Hòa",
            "wards": ["Phường Vĩnh Lộc", "Phường Xương Huân", "Phường Tây Sơn", "Phường Vạn Thạnh", "Phường Vĩnh Nguyên"]
        },
        {
            "name": "Kiên Giang",
            "wards": ["Phường Vị Thanh", "Phường Châu Đốc", "Phường Hà Tiên", "Phường An Hải", "Phường Hòa Bình"]
        },
        {
            "name": "Kon Tum",
            "wards": ["Phường Kon Tum", "Phường Thanh Bình", "Phường Trà Kót", "Phường Trà Bồng", "Phường Đắk Hà"]
        },
        {
            "name": "Lai Châu",
            "wards": ["Phường Lai Châu", "Phường Tân Hợp", "Phường Phong Hồ", "Phường Nậm Sai", "Phường Nậm Mùn"]
        },
        {
            "name": "Lâm Đồng",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Thạo Điền"]
        },
        {
            "name": "Lạng Sơn",
            "wards": ["Phường Lạng Sơn", "Phường Quảng Đông", "Phường Vĩnh Xương", "Phường Đức Long", "Phường Hữu Nghị"]
        },
        {
            "name": "Lào Cai",
            "wards": ["Phường Lào Cai", "Phường Bắc Hà", "Phường Sa Pa", "Phường Phong Hồ", "Phường Bảo Thắng"]
        },
        {
            "name": "Long An",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Tân An"]
        },
        {
            "name": "Nam Định",
            "wards": ["Phường Lý Tự Trọng", "Phường Thống Nhất", "Phường Quảng Trung", "Phường Lê Thanh Nghị", "Phường Nguyễn Văn Linh"]
        },
        {
            "name": "Nghệ An",
            "wards": ["Phường Quỳnh Lưu", "Phường Con Cuông", "Phường Diễn Châu", "Phường Hưởng Sơn", "Phường Nghi Lộc"]
        },
        {
            "name": "Ninh Bình",
            "wards": ["Phường Ninh Khánh", "Phường Ninh Phong", "Phường Ninh Xá", "Phường Hoa Lư", "Phường Yên Mỹ"]
        },
        {
            "name": "Ninh Thuận",
            "wards": ["Phường Ninh Phước", "Phường Thủy Nguyên", "Phường Ninh Sơn", "Phường Mỹ Tú", "Phường An Phước"]
        },
        {
            "name": "Phú Thọ",
            "wards": ["Phường Vinh Tuy", "Phường Minh Khai", "Phường Hàng Hòa", "Phường Phú Thọ", "Phường Vĩnh Phúc"]
        },
        {
            "name": "Phú Yên",
            "wards": ["Phường Ngô Mây", "Phường Trần Hưng Đạo", "Phường Lê Thánh Tông", "Phường Thừa Phú", "Phường Hòa Tài"]
        },
        {
            "name": "Quảng Bình",
            "wards": ["Phường Đông Hà", "Phường Quảng Trị", "Phường Vĩnh Linh", "Phường Bố Trạch", "Phường Tuyên Hóa"]
        },
        {
            "name": "Quảng Nam",
            "wards": ["Phường Cẩm Phô", "Phường Cẩm Hòa", "Phường Cẩm Chính", "Phường Cẩm An", "Phường Thanh Chương"]
        },
        {
            "name": "Quảng Ngãi",
            "wards": ["Phường Quảng Phú", "Phường Quảng Tây", "Phường Quảng Chính", "Phường Quảng Tân", "Phường Tịnh Ngoài"]
        },
        {
            "name": "Quảng Ninh",
            "wards": ["Phường Hạ Long", "Phường Bãi Cháy", "Phường Hồng Gai", "Phường Geleximco", "Phường Cẩm Phả"]
        },
        {
            "name": "Quảng Trị",
            "wards": ["Phường Đông Hà", "Phường Vinh Mỹ", "Phường Hải Lăng", "Phường Giao Thủy", "Phường Con Cuông"]
        },
        {
            "name": "Sóc Trăng",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Mỹ Tú"]
        },
        {
            "name": "Sơn La",
            "wards": ["Phường Sơn La", "Phường Nước Ngoài", "Phường Pú Nông", "Phường Thừa Phong", "Phường Phù Yên"]
        },
        {
            "name": "Tây Ninh",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Phú Mỹ"]
        },
        {
            "name": "Thái Bình",
            "wards": ["Phường Kỳ Bá", "Phường Lê Hồng Phong", "Phường Hàng Bột", "Phường Hàng Lược", "Phường Phú Đức"]
        },
        {
            "name": "Thái Nguyên",
            "wards": ["Phường Tích Lương", "Phường Thái Hòa", "Phường Nông Cống", "Phường Hòa Mỹ", "Phường Hoa Phong"]
        },
        {
            "name": "Thanh Hóa",
            "wards": ["Phường Lam Sơn", "Phường Tân Thịnh", "Phường Trường Sơn", "Phường Nông Cống", "Phường Bỉm Sơn"]
        },
        {
            "name": "Thừa Thiên Huế",
            "wards": ["Phường Phú Nhuận", "Phường Tây Lộc", "Phường Vỹ Dạ", "Phường Kim Long", "Phường Thủy Xuân"]
        },
        {
            "name": "Tiền Giang",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Tân Phú"]
        },
        {
            "name": "Trà Vinh",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Tháp Mười"]
        },
        {
            "name": "Tuyên Quang",
            "wards": ["Phường Tuyên Quang", "Phường Chiêm Hóa", "Phường Yên Sơn", "Phường Hàm Yên", "Phường Nà Hang"]
        },
        {
            "name": "Vĩnh Long",
            "wards": ["Phường 1", "Phường 2", "Phường 3", "Phường 4", "Phường Long Tuyên"]
        },
        {
            "name": "Vĩnh Phúc",
            "wards": ["Phường Tây Hạ Thành", "Phường Tân Cương", "Phường Yên Hòa", "Phường Lê Hồng Phong", "Phường Cô Động"]
        },
        {
            "name": "Yên Bái",
            "wards": ["Phường Yên Bái", "Phường Trấn Yên", "Phường Văn Chấp", "Phường Mộc Châu", "Phường Lục Yên"]
        }
    ];



    const citySelect = document.getElementById("city");
    const wardSelect = document.getElementById("ward");

    // Tạo option cho select
    function createOption(value, text) {
        const option = document.createElement("option");
        option.value = value;
        option.textContent = text;
        return option;
    }

    vietnamData.forEach(city => {
        citySelect.appendChild(createOption(city.name, city.name));
    });

    citySelect.addEventListener("change", () => {
        const city = vietnamData.find(c => c.name === citySelect.value);
        wardSelect.innerHTML = '<option value="" disabled selected>-- Chọn Phường/Xã --</option>';
        if (city) {
            city.wards.forEach(ward => {
                wardSelect.appendChild(createOption(ward, ward));
            });
        }
    });
    const form = document.querySelector('.delivery-form-content');
    form.addEventListener('submit', function(e) {
        const city = document.getElementById('city').value;
        const ward = document.getElementById('ward').value;
        const address = document.getElementById('address').value;

        // Gộp thành chuỗi
        const fullAddress = `${address}, ${ward}, ${city}`;
        document.getElementById('full_address').value = fullAddress;
    });
</script>
@endsection