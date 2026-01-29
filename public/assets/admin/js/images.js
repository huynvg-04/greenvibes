function previewSingleImage(input, imgId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            document.getElementById(imgId).src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function handleFileSelect(input) {
    const container = document.getElementById("preview_container");
    const btnUpload = document.getElementById("btnUpload");

    container.innerHTML = "";

    if (input.files && input.files.length > 0) {
        btnUpload.disabled = false;

        Array.from(input.files).forEach((file) => {
            var reader = new FileReader();
            reader.onload = function (e) {
                const col = document.createElement("div");
                col.className = "col-3 col-md-2";
                col.innerHTML = `
                        <div class="preview-item">
                            <img src="${e.target.result}">
                            <div class="preview-remove"><i class='bx bx-check'></i></div>
                        </div>
                    `;
                container.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    } else {
        btnUpload.disabled = true;
    }
}

function toggleImageSelection(card, productId, imageId) {
    const checkbox = document.getElementById("cb-" + imageId);
    checkbox.checked = !checkbox.checked;
    if (checkbox.checked) {
        card.classList.add("selected");
    } else {
        card.classList.remove("selected");
    }
    updateBulkToolbar(productId);
}

function updateBulkToolbar(productId) {
    const wrapper = document.getElementById("form-bulk-wrapper-" + productId);
    const checkboxes = wrapper.querySelectorAll(".image-checkbox:checked");

    const toolbar = document.getElementById("bulk-toolbar-" + productId);
    const countSpan = toolbar.querySelector(".selected-count");

    countSpan.textContent = checkboxes.length;

    if (checkboxes.length > 0) {
        toolbar.classList.remove("d-none");
        toolbar.classList.add("d-flex");
    } else {
        toolbar.classList.add("d-none");
        toolbar.classList.remove("d-flex");
    }
}

function prepareEditModal(event, btn) {
    event.stopPropagation();
    event.preventDefault();

    const id = btn.dataset.id;
    const productId = btn.dataset.productId;
    const url = btn.dataset.url;
    const isPrimary = btn.dataset.isPrimary === "true";
    const hiddenInput = document.getElementById("edit_modal_image_id");
    if(hiddenInput) hiddenInput.value = id;

    const imgPreview = document.getElementById("edit_preview_img");
    const checkboxPrimary = document.getElementById("edit_is_primary");
    const fileInput = document.getElementById("edit_image_input");
    const form = document.getElementById("editImageForm");

    if (imgPreview) imgPreview.src = url;
    if (checkboxPrimary) checkboxPrimary.checked = isPrimary;
    if (fileInput) fileInput.value = "";

    if (form) form.action = `/admin/product_images/update/${productId}/${id}`;

    const modalEl = document.getElementById("editImageModal");
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

function viewImage(event, imageUrl) {
    event.stopPropagation();
    event.preventDefault();

    const imgTarget = document.getElementById("view_image_target");
    if (imgTarget) {
        imgTarget.src = imageUrl;
    }

    const modalEl = document.getElementById("viewImageModal");
    if (modalEl) {
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    document.body.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-add-image");
        if (btn) {
            e.preventDefault();
            e.stopPropagation();

            const productId = btn.dataset.productId;
            const productName = btn.dataset.productName;
            const hiddenInput = document.getElementById(
                "create_modal_product_id",
            );
            if (hiddenInput) hiddenInput.value = productId;

            document.getElementById("create_product_name_display").textContent =
                productName;
            document.getElementById("createImageForm").action =
                `/admin/product_images/store/${productId}`;

            document.getElementById("upload_images").value = "";
            document.getElementById("preview_container").innerHTML = "";
            document.getElementById("btnUpload").disabled = true;

            new bootstrap.Modal(
                document.getElementById("createImageModal"),
            ).show();
        }
    });

    const deleteModal = document.getElementById("deleteModal");
    const deleteForm = document.getElementById("deleteForm");
    const modalTitle = document.getElementById("modalTitle");
    const modalDesc = document.getElementById("modalDesc");

    if (deleteModal) {
        deleteModal.addEventListener("show.bs.modal", function (event) {
            const trigger = event.relatedTarget;
            deleteForm.action = "";
            deleteForm.onsubmit = null;

            if (trigger.hasAttribute("data-url")) {
                deleteForm.action = trigger.getAttribute("data-url");
                modalTitle.textContent =
                    trigger.getAttribute("data-title") || "Xóa ảnh này?";
                modalDesc.textContent =
                    trigger.getAttribute("data-desc") ||
                    "Hành động này không thể hoàn tác.";
            } else if (trigger.classList.contains("bulk-delete-btn")) {
                const formId = trigger.getAttribute("data-form-id");
                const originalForm = document.getElementById(formId);
                modalTitle.textContent = "Xóa các ảnh đã chọn?";
                modalDesc.textContent = "Các ảnh này sẽ bị xóa vĩnh viễn.";
                deleteForm.onsubmit = function (e) {
                    e.preventDefault();
                    originalForm.submit();
                };
            }
        });
    }
});
