document.addEventListener('DOMContentLoaded', function() {

    function toSlug(str) {
        str = str.toLowerCase();     
        str = str.normalize('NFD').replace(/[\u0300-\u036f]/g, "");
        str = str.replace(/[đĐ]/g, 'd');
        str = str.replace(/([^0-9a-z-\s])/g, '');
        str = str.replace(/(\s+)/g, '-');
        str = str.replace(/-+/g, '-');
        str = str.replace(/^-+|-+$/g, '');

        return str;
    }

    const nameInput = document.getElementById('name'); 
    const slugInput = document.getElementById('slug'); 

    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            slugInput.value = toSlug(this.value);
        });
    }

    const editNameInput = document.getElementById('edit_name');
    const editSlugInput = document.getElementById('edit_slug');

    if (editNameInput && editSlugInput) {
        editNameInput.addEventListener('input', function() {
            editSlugInput.value = toSlug(this.value);
        });
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit');

        if (btn) {
            e.preventDefault();

            const id = btn.getAttribute('data-id');
            const name = btn.getAttribute('data-name');
            const slug = btn.getAttribute('data-slug');
            
            let values = [];
            try {
                values = JSON.parse(btn.getAttribute('data-values'));
            } catch (error) {
                console.error("Lỗi parse JSON:", error);
                values = [];
            }

            const editName = document.getElementById('edit_name');
            const editSlug = document.getElementById('edit_slug');
            const editForm = document.getElementById('editForm');
            const addValueForm = document.getElementById('addValueForm');

            if (editName) editName.value = name;
            if (editSlug) editSlug.value = slug;
            
            if (editForm) editForm.action = `/admin/attributes/${id}`;
            if (addValueForm) addValueForm.action = `/admin/attributes/${id}/values`;

            const valuesTable = document.getElementById('modalValuesTable');
            let html = '';

            if (values && values.length > 0) {
                values.forEach(v => {
                    let codeDisplay = v.code ?
                        `<span class="d-inline-block border rounded-circle" style="width:12px;height:12px;background:${v.code}"></span> <span class="small font-monospace">${v.code}</span>` :
                        '<span class="text-muted small">---</span>';

                    html += `
                        <tr>
                            <td class="ps-3 fw-semibold text-body">${v.value}</td>
                            <td>${codeDisplay}</td>
                            <td class="text-end pe-3">
                                <form action="/admin/attributes/values/${v.id}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giá trị này?');" style="display:inline-block;">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="button" class="btn btn-icon btn-light-danger btn-sm rounded-circle" onclick="this.closest('form').submit()">
                                        <i class='bx bx-x fs-5'></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    `;
                });
            } else {
                html = `<tr><td colspan="3" class="text-center text-muted py-4 small">Chưa có giá trị nào. Hãy thêm mới bên trên.</td></tr>`;
            }

            if (valuesTable) valuesTable.innerHTML = html;

            const editModalEl = document.getElementById('editModal');
            if (editModalEl) {
                const editModal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                editModal.show();
            }
        }
    });

    const deleteModalEl = document.getElementById('deleteModal');
    if (deleteModalEl) {
        deleteModalEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const name = button.getAttribute('data-name');

            document.getElementById('modalDeleteName').textContent = name;
            document.getElementById('deleteForm').action = `/admin/attributes/${id}`;
        });
    }

    const createModalEl = document.getElementById('createModal');
    if (createModalEl && createModalEl.getAttribute('data-has-errors') === 'true') {
        const createModal = new bootstrap.Modal(createModalEl);
        createModal.show();
    }
});