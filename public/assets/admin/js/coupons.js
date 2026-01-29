/**
 * Helper: Đổi màu/chữ cho label trạng thái (Switch toggle)
 */
function toggleStatusLabel(checkbox, labelId) {
    const label = document.getElementById(labelId);
    if (label) {
        if (checkbox.checked) {
            label.textContent = 'Hoạt động';
            label.className = 'form-check-label small fw-bold text-success';
        } else {
            label.textContent = 'Đã tắt';
            label.className = 'form-check-label small fw-bold text-danger';
        }
    }
}

/**
 * Helper: Ẩn/Hiện vùng nhập "Giảm tối đa" dựa trên loại giảm giá
 */
function toggleMaxDiscount(selectId, areaId) {
    const select = document.getElementById(selectId);
    const area = document.getElementById(areaId);
    if (select && area) {
        if (select.value === 'percent') {
            area.style.display = 'block';
        } else {
            area.style.display = 'none';
            // Tùy chọn: Reset giá trị input bên trong khi ẩn đi
            const input = area.querySelector('input');
            if (input) input.value = '';
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // 1. Khởi tạo trạng thái ban đầu
    toggleMaxDiscount('create_type_select', 'create_max_discount_area');

    // Bắt sự kiện change cho select loại giảm giá (Thêm mới)
    const createSelect = document.getElementById('create_type_select');
    if (createSelect) {
        createSelect.addEventListener('change', function () {
            toggleMaxDiscount('create_type_select', 'create_max_discount_area');
        });
    }

    // Bắt sự kiện change cho select loại giảm giá (Chỉnh sửa)
    const editSelect = document.getElementById('edit_type_select');
    if (editSelect) {
        editSelect.addEventListener('change', function () {
            toggleMaxDiscount('edit_type_select', 'edit_max_discount_area');
        });
    }

    // 2. Xử lý Modal Xóa
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-coupon-id');
            const code = button.getAttribute('data-coupon-code');
            
            document.getElementById('modalCouponCode').textContent = code;
            document.getElementById('deleteForm').action = `/admin/coupons/${id}`;
        });
    }

    // 3. Xử lý Modal Chỉnh sửa (Đổ dữ liệu)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-edit-coupon');
        if (btn) {
            e.preventDefault();
            const id = btn.dataset.id;
            
            document.getElementById('edit_code').value = btn.dataset.code;
            document.getElementById('edit_description').value = btn.dataset.description;
            document.getElementById('edit_value').value = btn.dataset.value;
            document.getElementById('edit_min_order').value = btn.dataset.minOrder;
            document.getElementById('edit_max_discount').value = btn.dataset.maxDiscount;
            document.getElementById('edit_start_date').value = btn.dataset.startDate;
            document.getElementById('edit_end_date').value = btn.dataset.endDate;
            document.getElementById('edit_usage_limit').value = btn.dataset.usageLimit;

            const typeSelect = document.getElementById('edit_type_select');
            typeSelect.value = btn.dataset.type;
            toggleMaxDiscount('edit_type_select', 'edit_max_discount_area');

            const switchActive = document.getElementById('edit_is_active');
            switchActive.checked = (btn.dataset.isActive === 'active');
            toggleStatusLabel(switchActive, 'edit_status_label');

            document.getElementById('editCouponForm').action = `/admin/coupons/${id}`;

            new bootstrap.Modal(document.getElementById('editCouponModal')).show();
        }
    });

    // 4. Mở lại Modal khi có lỗi Validate (Server trả về)
    if (window.couponConfig && window.couponConfig.hasErrors) {
        if (window.couponConfig.oldMethod === 'PUT') {
            const editModalEl = document.getElementById('editCouponModal');
            if (editModalEl) new bootstrap.Modal(editModalEl).show();
        } else {
            const createModalEl = document.getElementById('createCouponModal');
            if (createModalEl) new bootstrap.Modal(createModalEl).show();
        }
    }

    // ---------------------------------------------------------
    // 5. [MỚI] XÓA TRẮNG FORM KHI ĐÓNG MODAL
    // ---------------------------------------------------------
    
    // Xử lý cho Modal Thêm mới: Reset toàn bộ form về mặc định
    const createModalEl = document.getElementById('createCouponModal');
    if (createModalEl) {
        createModalEl.addEventListener('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            if (form) {
                form.reset(); // Reset input, select về mặc định
                
                // Xóa các class lỗi (nếu có)
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                form.querySelectorAll('.invalid-feedback').forEach(el => el.remove()); // (Tùy chọn) Xóa text lỗi
                
                // Reset lại giao diện "Giảm tối đa" theo select mặc định
                toggleMaxDiscount('create_type_select', 'create_max_discount_area');
            }
        });
    }

    // Xử lý cho Modal Chỉnh sửa: Chỉ xóa class lỗi (Không cần reset value vì nút Edit sẽ đổ lại dữ liệu)
    const editModalEl = document.getElementById('editCouponModal');
    if (editModalEl) {
        editModalEl.addEventListener('hidden.bs.modal', function () {
            const form = this.querySelector('form');
            if (form) {
                // Chỉ cần xóa thông báo lỗi đỏ
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            }
        });
    }
});