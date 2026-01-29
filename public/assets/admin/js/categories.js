document.addEventListener("DOMContentLoaded", function () {
    
    // --- 0. DỌN DẸP BACKDROP CŨ (Nếu bị kẹt) ---
    const backdrops = document.querySelectorAll('.modal-backdrop');
    if (backdrops.length > 0) {
        backdrops.forEach(el => el.remove());
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    }

    // Lấy config từ Blade
    const config = window.laravelConfig || { hasErrors: false, formMode: '', editId: '' };

    // --- 1. Khởi tạo Tooltip Bootstrap ---
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // --- 2. Logic Modal Edit (Sửa danh mục) ---
    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-edit"); 
        
        if (btn) {
            e.preventDefault();

            // Lấy dữ liệu
            const id = btn.getAttribute("data-id");
            const name = btn.getAttribute("data-name");
            const slug = btn.getAttribute("data-slug");
            const type = btn.getAttribute("data-type");
            const desc = btn.getAttribute("data-description");

            // Đổ dữ liệu vào form
            const editName = document.getElementById("edit_name");
            const editSlug = document.getElementById("edit_slug");
            const editType = document.getElementById("edit_type");
            const editDesc = document.getElementById("edit_description");
            const editForm = document.getElementById("editForm");
            
            // Tìm input hidden ID trong form edit để gán giá trị (quan trọng cho validate)
            const hiddenId = editForm ? editForm.querySelector('input[name="id"]') : null;

            if (editName) editName.value = name;
            if (editSlug) editSlug.value = slug;
            if (editType) editType.value = type;
            if (editDesc) editDesc.value = desc ? desc : '';
            if (hiddenId) hiddenId.value = id;

            if (editForm) editForm.action = `/admin/categories/${id}`;

            const editModalEl = document.getElementById("editModal");
            if (editModalEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
                modal.show();
            }
        }
    });

    const deleteModalEl = document.getElementById("deleteModal");
    if (deleteModalEl) {
        deleteModalEl.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute("data-id");
            const name = button.getAttribute("data-name");

            document.getElementById("modalDeleteName").textContent = name;
            document.getElementById("deleteForm").action = `/admin/categories/${id}`;
        });
    }

    function generateSlug(value) {
        let slug = value.toLowerCase();
        slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/g, "a");
        slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/g, "e");
        slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/g, "i");
        slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/g, "o");
        slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/g, "u");
        slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/g, "y");
        slug = slug.replace(/đ/g, "d");
        slug = slug.replace(/[^a-z0-9\s-]/g, "");
        slug = slug.replace(/\s+/g, "-");
        slug = slug.replace(/^-+|-+$/g, "");
        return slug;
    }

    const createNameInput = document.querySelector('#createModal input[name="name"]');
    const createSlugInput = document.querySelector('#createModal input[name="slug"]');
    if (createNameInput && createSlugInput) {
        const handler = function() { createSlugInput.value = generateSlug(this.value); };
        createNameInput.addEventListener("keyup", handler);
        createNameInput.addEventListener("change", handler);
    }

    const editNameInput = document.getElementById("edit_name");
    const editSlugInput = document.getElementById("edit_slug");
    if (editNameInput && editSlugInput) {
        editNameInput.addEventListener("keyup", function () {
            editSlugInput.value = generateSlug(this.value);
        });
    }

    if (config.hasErrors) {
        const safeShow = (id) => {
            const el = document.getElementById(id);
            if (el) {
                const modal = bootstrap.Modal.getOrCreateInstance(el);
                modal.show();
            }
        };

        if (config.formMode === 'create') {
            safeShow('createModal');
        } 
        else if (config.formMode === 'edit') {
            const editForm = document.getElementById('editForm');
            if (editForm && config.editId) {
                editForm.action = `/admin/categories/${config.editId}`;
            }
            safeShow('editModal');
        }
    }
    
    const createModalEl = document.getElementById('createModal');
    if (createModalEl) {
        createModalEl.addEventListener('hidden.bs.modal', function () {
             const form = createModalEl.querySelector('form');
             if(form) {
                 form.reset();
                 form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
             }
        });
    }
});