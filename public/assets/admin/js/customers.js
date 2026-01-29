function updateStatusLabel(isChecked) {
    const label = document.getElementById('edit_status_label');
    if (label) {
        if (isChecked) {
            label.textContent = 'Hoạt động';
            label.className = 'form-check-label fw-bold small text-success';
        } else {
            label.textContent = 'Bị khóa';
            label.className = 'form-check-label fw-bold small text-danger';
        }
    }
}

window.toggleCreateStatus = function(checkbox) {
    const label = document.getElementById('create_status_label');
    if (label) {
        if (checkbox.checked) {
            label.textContent = 'Hoạt động';
            label.className = 'form-check-label small fw-bold text-success';
        } else {
            label.textContent = 'Bị khóa';
            label.className = 'form-check-label small fw-bold text-danger';
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // 1. Khởi tạo Tooltip Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const customerId = button.getAttribute('data-customer-id');
            const customerName = button.getAttribute('data-customer-name');

            document.getElementById('modalCustomerName').textContent = customerName;
            document.getElementById('deleteForm').action = `/admin/customers/${customerId}`;
        });
    }

    const editModalEl = document.getElementById('editCustomerModal');
    if (editModalEl) {
        const editModal = new bootstrap.Modal(editModalEl);
        const editForm = document.getElementById('editCustomerForm');

        const inputEmail = document.getElementById('edit_email');
        const inputName = document.getElementById('edit_fullname');
        const inputPhone = document.getElementById('edit_phone');
        const selectGender = document.getElementById('edit_gender');
        const inputAddress = document.getElementById('edit_address');
        const switchStatus = document.getElementById('edit_status');
        const selectTier = document.getElementById('edit_tier_id');
        const passInput = editForm.querySelector('input[name="password"]');
        const passConfirm = editForm.querySelector('input[name="password_confirmation"]');

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-edit-customer');

            if (btn) {
                e.preventDefault();

                const id = btn.getAttribute('data-id');
                const tierId = btn.getAttribute('data-tier-id');
                const statusData = btn.getAttribute('data-status');
   
                if (inputEmail) inputEmail.value = btn.getAttribute('data-email');
                if (inputName) inputName.value = btn.getAttribute('data-fullname');
                if (inputPhone) inputPhone.value = btn.getAttribute('data-phone');
                if (selectGender) selectGender.value = btn.getAttribute('data-gender');
                if (inputAddress) inputAddress.value = btn.getAttribute('data-address');

                if (selectTier) {
                    selectTier.value = tierId;
                }

                if (switchStatus) {
                    switchStatus.checked = (statusData === 'active');
                    updateStatusLabel(switchStatus.checked);

                    switchStatus.onchange = function() {
                        updateStatusLabel(this.checked);
                    };
                }

                if (passInput) passInput.value = '';
                if (passConfirm) passConfirm.value = '';
                editForm.action = `/admin/customers/${id}`;

                editModal.show();
            }
        });
    }
});