function initCKEditor(elementId) {
    if (typeof CKEDITOR === 'undefined') {
        console.error('CKEditor chưa được tải!');
        return;
    }
    
    if (CKEDITOR.instances[elementId]) {
        CKEDITOR.instances[elementId].destroy(true);
    }
    CKEDITOR.replace(elementId, {
        height: 150,
        removePlugins: 'exportpdf',
        toolbar: [{
                name: 'basicstyles',
                items: ['Bold', 'Italic', 'Underline', 'RemoveFormat']
            },
            {
                name: 'paragraph',
                items: ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight']
            },
            {
                name: 'colors',
                items: ['TextColor', 'BGColor']
            },
            {
                name: 'styles',
                items: ['Format', 'Font', 'FontSize']
            }
        ],
        versionCheck: false,
    });
}

window.previewImage = function(input, imgId, placeholderId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById(imgId);
            if (img) {
                img.src = e.target.result;
                img.classList.remove('d-none');
            }
            if (placeholderId) {
                const placeholder = document.getElementById(placeholderId);
                if (placeholder) placeholder.classList.add('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
};

window.showImageModal = function(src) {
    const modalEl = document.getElementById('imagePreviewModal');
    const img = document.getElementById('previewImage');

    if (modalEl && img) {
        img.src = src;
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
};

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('create_title_editor')) {
        initCKEditor('create_title_editor');
    }
    if (document.getElementById('edit_title_editor')) {
        initCKEditor('edit_title_editor');
    }

    document.addEventListener('click', function(e) {

        const btnEdit = e.target.closest('.btn-edit-banner');
        if (btnEdit) {
            e.preventDefault();

            const id = btnEdit.getAttribute('data-id');
            const title = btnEdit.getAttribute('data-title');
            const link = btnEdit.getAttribute('data-link');
            const status = btnEdit.getAttribute('data-status');
            const imageUrl = btnEdit.getAttribute('data-image');

            if (CKEDITOR.instances['edit_title_editor']) {
                CKEDITOR.instances['edit_title_editor'].setData(title);
            }

            const linkInput = document.getElementById('edit_link');
            const statusInput = document.getElementById('edit_status');
            const form = document.getElementById('editBannerForm');

            if (linkInput) linkInput.value = link;
            if (statusInput) statusInput.value = status;
            if (form) form.action = `/admin/banners/${id}`;

            const imgPreview = document.getElementById('edit_preview_img');
            if (imgPreview) {
                imgPreview.src = imageUrl;
                imgPreview.classList.remove('d-none');
            }

            const fileInput = form.querySelector('input[type="file"]');
            if (fileInput) fileInput.value = '';

            const editModalEl = document.getElementById('editBannerModal');
            if (editModalEl) {
                const editModal = new bootstrap.Modal(editModalEl);
                editModal.show();
            }
        }

        const btnView = e.target.closest('.btn-view-image');
        if (btnView) {
            const src = btnView.getAttribute('data-image');
            showImageModal(src);
        }
    });

    const createModalEl = document.getElementById('createBannerModal');
    if (createModalEl && createModalEl.getAttribute('data-has-errors') === 'true') {
        const createModal = new bootstrap.Modal(createModalEl);
        createModal.show();
    }

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const bannerId = button.getAttribute('data-banner-id');
            const bannerName = button.getAttribute('data-banner-name');

            const div = document.createElement("div");
            div.innerHTML = bannerName;
            const textName = div.textContent || div.innerText || "";

            document.getElementById('modalBannerName').textContent = textName;
            document.getElementById('deleteForm').action = `/admin/banners/${bannerId}`;
        });
    }
});