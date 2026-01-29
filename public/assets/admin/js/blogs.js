window.toggleBlogStatus = function (checkbox, labelId) {
    const label = document.getElementById(labelId);
    if (!label) return;

    if (checkbox.checked) {
        label.textContent = "Công khai";
        label.classList.remove("text-muted");
        label.classList.add("text-success");
    } else {
        label.textContent = "Bản nháp";

        label.classList.remove("text-success");
        label.classList.add("text-muted");
    }
};

window.generateSlug = function (sourceId, targetId) {
    const sourceEl = document.getElementById(sourceId);
    const targetEl = document.getElementById(targetId);

    if (!sourceEl || !targetEl) return;

    let title = sourceEl.value;
    let slug = title.toLowerCase();
    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/gi, "a");
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/gi, "e");
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/gi, "i");
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/gi, "o");
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/gi, "u");
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/gi, "y");
    slug = slug.replace(/đ/gi, "d");
    slug = slug.replace(
        /\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi,
        "",
    );
    slug = slug.replace(/ /gi, "-");
    slug = slug.replace(/\-\-\-\-\-/gi, "-");
    slug = slug.replace(/\-\-\-\-/gi, "-");
    slug = slug.replace(/\-\-\-/gi, "-");
    slug = slug.replace(/\-\-/gi, "-");
    slug = "@" + slug + "@";
    slug = slug.replace(/\@\-|\-\@|\@/gi, "");

    targetEl.value = slug;
};

window.previewImage = function (input, imgId) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            const img = document.getElementById(imgId);
            if (img) {
                img.src = e.target.result;
                img.classList.remove("d-none");

                const placeholder = input
                    .closest(".card-body")
                    .querySelector(".placeholder-text");
                if (placeholder) placeholder.classList.add("d-none");
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
};

function initCKEditor(elementId) {
    if (typeof CKEDITOR === "undefined") {
        console.warn("CKEditor chưa được load.");
        return;
    }

    if (document.getElementById(elementId)) {
        if (CKEDITOR.instances[elementId]) {
            CKEDITOR.instances[elementId].destroy(true);
        }
        CKEDITOR.replace(elementId, {
            height: 300,
            removePlugins: "exportpdf",
            versionCheck: false,
        });
    }
}

document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    );
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const createModalEl = document.getElementById("createBlogModal");
    if (createModalEl) {
        createModalEl.addEventListener("shown.bs.modal", function () {
            initCKEditor("create_content_editor");
        });

        createModalEl.addEventListener("hidden.bs.modal", function () {
            const form = this.querySelector("form");
            if (form) form.reset();

            const img = document.getElementById("create_preview_img");
            const placeholder = this.querySelector(".placeholder-text");
            if (img) img.classList.add("d-none");
            if (placeholder) placeholder.classList.remove("d-none");

            if (CKEDITOR.instances["create_content_editor"]) {
                CKEDITOR.instances["create_content_editor"].setData("");
            }
        });
    }

    const deleteModalEl = document.getElementById("deleteModal");
    if (deleteModalEl) {
        deleteModalEl.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const blogId = button.getAttribute("data-id");
            const blogTitle = button.getAttribute("data-title");

            document.getElementById("modalBlogTitle").textContent = blogTitle;
            document.getElementById("deleteForm").action =
                `/admin/blogs/${blogId}`;
        });
    }

    document.addEventListener("click", function (e) {
        const btn = e.target.closest(".btn-edit-blog");
        if (btn) {
            e.preventDefault();

            const id = btn.dataset.id;
            const title = btn.dataset.title;
            const slug = btn.dataset.slug;
            const excerpt = btn.dataset.excerpt;
            const content = btn.dataset.content;
            const categoryId = btn.dataset.categoryId;
            const isPublished = btn.dataset.isPublished === "true";
            const thumbnail = btn.dataset.thumbnail;

            document.getElementById('edit_blog_id').value = id;
            document.getElementById("edit_title").value = title;
            document.getElementById("edit_slug").value = slug;
            document.getElementById("edit_excerpt").value = excerpt;
            document.getElementById("edit_category_id").value = categoryId;

            const switchPub = document.getElementById("edit_is_published");
            if (switchPub) switchPub.checked = isPublished;

            const imgPreview = document.getElementById("edit_preview_img");
            const placeholderText = document.getElementById(
                "edit_placeholder_text",
            );

            if (thumbnail && !thumbnail.endsWith("/storage/")) {
                imgPreview.src = thumbnail;
                imgPreview.classList.remove("d-none");
                if (placeholderText) placeholderText.classList.add("d-none");
            } else {
                imgPreview.classList.add("d-none");
                if (placeholderText) placeholderText.classList.remove("d-none");
            }

            document.getElementById("editBlogForm").action =
                `/admin/blogs/${id}`;

            const editModalInstance = new bootstrap.Modal(
                document.getElementById("editBlogModal"),
            );
            editModalInstance.show();

            const editModalEl = document.getElementById("editBlogModal");
            editModalEl.addEventListener(
                "shown.bs.modal",
                function () {
                    initCKEditor("edit_content_editor");
                    if (CKEDITOR.instances["edit_content_editor"]) {
                        CKEDITOR.instances["edit_content_editor"].setData(
                            content,
                        );
                    }
                },
                { once: true },
            );
        }
    });

  const createModalDom = document.getElementById('createBlogModal');
    const editModalDom = document.getElementById('editBlogModal');

    // 1. Kiểm tra Modal Chỉnh sửa (Ưu tiên)
    if (editModalDom && editModalDom.dataset.hasErrors === 'true' && editModalDom.dataset.oldMethod === 'PUT') {
        const editInstance = bootstrap.Modal.getOrCreateInstance(editModalDom);
        editInstance.show();

        // [QUAN TRỌNG] Khôi phục lại Action URL từ ID cũ
        const oldId = document.getElementById('edit_blog_id').value;
        if (oldId) {
            document.getElementById('editBlogForm').action = `/admin/blogs/${oldId}`;
        }

        // Init CKEditor (Không dùng setData để giữ lại old content)
        editModalDom.addEventListener('shown.bs.modal', function() {
             initCKEditor('edit_content_editor');
        }, { once: true });
    } 
    // 2. Kiểm tra Modal Thêm mới
    else if (createModalDom && createModalDom.dataset.hasErrors === 'true') {
        const createInstance = bootstrap.Modal.getOrCreateInstance(createModalDom);
        createInstance.show();

        createModalDom.addEventListener('shown.bs.modal', function() {
             initCKEditor('create_content_editor');
        }, { once: true });
    }
});
