window.toggleStatusLabel = function(checkbox, labelId) {
    const label = document.getElementById(labelId);
    const hiddenInput = document.getElementById('real_edit_status');

    if (label && hiddenInput) {
        if (checkbox.checked) {
            label.textContent = 'Đang làm việc';
            label.className = 'form-check-label fw-bold text-success ms-2';
            hiddenInput.value = 'active';
        } else {
            label.textContent = 'Đã nghỉ / Khóa';
            label.className = 'form-check-label fw-bold text-danger ms-2';
            hiddenInput.value = 'blocked';
        }
    }
};

document.addEventListener('DOMContentLoaded', function() {
    
    function initSelectAll(selectAllClass, itemClass, groupDataAttr) {
        const selectAllBoxes = document.querySelectorAll(selectAllClass);
        selectAllBoxes.forEach(selectAll => {
            selectAll.addEventListener('change', function() {
                const groupId = this.getAttribute('data-group-id');
                const childSelector = itemClass + groupId;
                document.querySelectorAll(childSelector).forEach(child => child.checked = this.checked);
            });
        });
    }

    initSelectAll('.create-select-all', '.create-permission-item.create-group-');
    initSelectAll('.edit-select-all', '.edit-permission-item.edit-group-');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-staff');
        if (btn) {
            e.preventDefault();

            const id = btn.dataset.id;
            let permissions = [];
            try {
                permissions = JSON.parse(btn.dataset.permissions);
            } catch (error) {
                console.error("Lỗi parse permissions:", error);
            }

            document.getElementById('edit_full_name').value = btn.dataset.fullname || '';
            document.getElementById('edit_email').value = btn.dataset.email || '';
            document.getElementById('edit_phone').value = btn.dataset.phone || '';
            document.getElementById('edit_position').value = btn.dataset.position || '';
            document.getElementById('edit_salary').value = btn.dataset.salary || '';
            document.getElementById('edit_start_date').value = btn.dataset.startDate || '';

            const statusSwitch = document.getElementById('edit_status_switch');
            const isActive = (btn.dataset.status === 'active');
            
            if(statusSwitch) {
                statusSwitch.checked = isActive;
                toggleStatusLabel(statusSwitch, 'edit_status_label');
            }

            document.querySelectorAll('.edit-permission-item').forEach(el => el.checked = false);
            document.querySelectorAll('.edit-select-all').forEach(el => el.checked = false);
            
            permissions.forEach(permName => {
                const checkbox = document.querySelector(`.edit-permission-item[value="${permName}"]`);
                if (checkbox) checkbox.checked = true;
            });

            const form = document.getElementById('editStaffForm');
            if(form) form.action = `/admin/staffs/${id}`;

            const firstTabBtn = document.querySelector('#editStaffTab button[data-bs-target="#edit-info-pane"]');
            if(firstTabBtn) new bootstrap.Tab(firstTabBtn).show();

            const modalEl = document.getElementById('editStaffModal');
            if(modalEl) new bootstrap.Modal(modalEl).show();
        }
    });

    const createModalEl = document.getElementById('createStaffModal');
    if (createModalEl) {
        createModalEl.addEventListener('show.bs.modal', function() {
            const firstTabBtn = document.querySelector('#createStaffTab button[data-bs-target="#create-info-pane"]');
            if(firstTabBtn) new bootstrap.Tab(firstTabBtn).show();
        });
    }

    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const staffId = button.getAttribute('data-staff-id');
            const staffName = button.getAttribute('data-staff-name');
            
            const nameEl = document.getElementById('staffName');
            const formEl = document.getElementById('deleteForm');

            if(nameEl) nameEl.textContent = staffName;
            if(formEl) formEl.action = `/admin/staffs/${staffId}`;
        });
    }
});