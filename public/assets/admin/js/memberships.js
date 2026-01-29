document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Xử lý Modal Xóa
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const tierId = button.getAttribute('data-id');
            const tierName = button.getAttribute('data-name');
            document.getElementById('modalTierName').textContent = tierName;
            document.getElementById('deleteForm').action = `/admin/membership-tiers/${tierId}`;
        });
    }

    function syncColor(pickerId, inputId) {
        const picker = document.getElementById(pickerId);
        const input = document.getElementById(inputId);
        if (picker && input) {
            picker.addEventListener('input', () => input.value = picker.value);
            input.addEventListener('input', () => picker.value = input.value);
        }
    }
    syncColor('edit_colorPicker', 'edit_colorInput');
    syncColor('create_colorPicker', 'create_colorInput');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-tier');
        if (btn) {
            e.preventDefault();

            const id = btn.dataset.id;
            const form = document.getElementById('editTierForm');
            const editModalEl = document.getElementById('editTierModal');

            if(form && editModalEl) {
                document.getElementById('edit_name').value = btn.dataset.name;
                document.getElementById('edit_rank_priority').value = btn.dataset.rankPriority;
                
                const colorHex = btn.dataset.colorHex || '#000000';
                document.getElementById('edit_colorInput').value = colorHex;
                document.getElementById('edit_colorPicker').value = colorHex;
                
                document.getElementById('edit_discount').value = btn.dataset.discount;
                document.getElementById('edit_usage_limit').value = btn.dataset.usageLimit;
                document.getElementById('edit_usage_period').value = btn.dataset.usagePeriod;
                document.getElementById('edit_min_spent').value = btn.dataset.minSpent;
                document.getElementById('edit_min_orders').value = btn.dataset.minOrders;
                document.getElementById('edit_validity_days').value = btn.dataset.validityDays;

                form.action = `/admin/membership-tiers/${id}`;

                new bootstrap.Modal(editModalEl).show();
            }
        }
    });
});