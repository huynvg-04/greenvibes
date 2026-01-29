window.generateSlug = function(sourceId, targetId) {
    const titleInput = document.getElementById(sourceId);
    const slugInput = document.getElementById(targetId);

    if (!titleInput || !slugInput) return;

    let title = titleInput.value;
    let slug = title.toLowerCase();

    slug = slug.replace(/á|à|ả|ạ|ã|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ/g, "a");
    slug = slug.replace(/é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ/g, "e");
    slug = slug.replace(/i|í|ì|ỉ|ĩ|ị/g, "i");
    slug = slug.replace(/ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ/g, "o");
    slug = slug.replace(/ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự/g, "u");
    slug = slug.replace(/ý|ỳ|ỷ|ỹ|ỵ/g, "y");
    slug = slug.replace(/đ/g, "d");
    slug = slug.replace(/\`|\~|\!|\@|\#|\||\$|\%|\^|\&|\*|\(|\)|\+|\=|\,|\.|\/|\?|\>|\<|\'|\"|\:|\;|_/gi, "");
    slug = slug.replace(/ /g, "-");
    slug = slug.replace(/\-\-\-\-\-/g, "-");
    slug = slug.replace(/\-\-\-\-/g, "-");
    slug = slug.replace(/\-\-\-/g, "-");
    slug = slug.replace(/\-\-/g, "-");
    slug = "@" + slug + "@";
    slug = slug.replace(/\@\-|\-\@|\@/g, "");

    slugInput.value = slug;
};

// [MỚI] Hàm đổi màu và chữ cho nút Switch Trạng thái
window.toggleStatusLabel = function(checkbox, labelId) {
    const label = document.getElementById(labelId);
    if (label) {
        if (checkbox.checked) {
            label.textContent = 'Kích hoạt';
            label.className = 'form-check-label fw-bold ms-3 text-success';
        } else {
            label.textContent = 'Bản nháp';
            label.className = 'form-check-label fw-bold ms-3 text-secondary';
        }
    }
};

document.addEventListener("DOMContentLoaded", function () {
    const createModalEl = document.getElementById('createProductModal');
    if (createModalEl) {
        createModalEl.addEventListener('hidden.bs.modal', function () {
            const form = document.getElementById('createProductForm');
            if (form) {
                form.reset();
                
                const invalidInputs = form.querySelectorAll('.is-invalid');
                invalidInputs.forEach(el => el.classList.remove('is-invalid'));
            }

            if (tagifyCreate) {
                tagifyCreate.removeAllTags();
            }

            const statusSwitch = document.getElementById('create_status_switch');
            if (statusSwitch) {
                statusSwitch.checked = true;
                window.toggleStatusLabel(statusSwitch, 'create_status_label');
            }
        });
    }


    const config = window.laravelConfig || { tagsWhitelist: [], hasErrors: false };

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    const deleteModal = document.getElementById("deleteModal");
    if (deleteModal) {
        deleteModal.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const productName = button.getAttribute("data-name");
            const productId = button.getAttribute("data-id");
            document.getElementById("modalProductName").textContent = productName;
            document.getElementById("deleteForm").action = `/admin/products/${productId}`;
        });
    }

    function initTagify(selector) {
        const input = document.querySelector(selector);
        if (input) {
            return new Tagify(input, {
                whitelist: config.tagsWhitelist,
                maxTags: 10,
                dropdown: { maxItems: 20, classname: "tags-look", enabled: 0, closeOnSelect: false },
                originalInputValueFormat: valuesArr => JSON.stringify(valuesArr)
            });
        }
        return null;
    }

    const tagifyEdit = initTagify('#edit_tags');
    const tagifyCreate = initTagify('#create_tags');

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-product');
        if (btn) {
            const d = btn.dataset;
            const editForm = document.getElementById('editProductForm');
            
            if(editForm) editForm.action = `/admin/products/${d.id}`;
            
            const setVal = (id, val) => { 
                const el = document.getElementById(id); 
                if(el) el.value = val; 
            };

            setVal('edit_name', d.name);
            setVal('edit_sku', d.sku);
            setVal('edit_slug', d.slug);
            setVal('edit_discount', d.discount);
            setVal('edit_category_id', d.category);
            setVal('hidden_edit_id', d.id);
            
            const statusSwitch = document.getElementById('edit_status_switch');
            if (statusSwitch) {
                statusSwitch.checked = (d.status == 1);
                window.toggleStatusLabel(statusSwitch, 'edit_status_label');
            }

            if (window.editor) {
                window.editor.setData(d.description || '');
            } else {
                setVal('edit_description', d.description);
            }

            if (tagifyEdit) {
                tagifyEdit.removeAllTags();
                let tagsRaw = d.tags;
                if (tagsRaw) {
                    try {
                        let tagsArray = typeof tagsRaw === 'string' ? JSON.parse(tagsRaw) : tagsRaw;
                        if (Array.isArray(tagsArray)) tagifyEdit.addTags(tagsArray);
                    } catch (err) {
                        console.error("Lỗi tags:", err);
                    }
                }
            }
        }
    });

    if (config.hasErrors) {
        if (config.oldMode === 'create') {
            const createModalEl = document.getElementById('createProductModal');
            if(createModalEl) {
                const modal = new bootstrap.Modal(createModalEl);
                modal.show();
                
                if (tagifyCreate && config.oldTags) {
                    try { tagifyCreate.addTags(JSON.parse(config.oldTags)); } catch(e){}
                }
            }
        } 
        else if (config.oldMode === 'edit') {
            const editModalEl = document.getElementById('editProductModal');
            const editForm = document.getElementById('editProductForm');

            if (editModalEl) {
                if (config.oldId && editForm) {
                    editForm.action = `/admin/products/${config.oldId}`;
                }

                const statusSwitch = document.getElementById('edit_status_switch');
                if (statusSwitch) {
                    statusSwitch.checked = (config.oldStatus == '1');
                    window.toggleStatusLabel(statusSwitch, 'edit_status_label');
                }
                
                if (tagifyEdit && config.oldTags) {
                    tagifyEdit.removeAllTags();
                    try { tagifyEdit.addTags(JSON.parse(config.oldTags)); } catch(e){}
                }

                const modal = new bootstrap.Modal(editModalEl);
                modal.show();
            }
        }
    }
});