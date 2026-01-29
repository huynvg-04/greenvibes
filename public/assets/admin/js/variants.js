document.addEventListener('DOMContentLoaded', function () {
    
    const createModalEl = document.getElementById('createVariantModal');
    if (createModalEl) {
        const createVariantModal = new bootstrap.Modal(createModalEl);
        const addButtons = document.querySelectorAll('.btn-add-variant');
        const createForm = document.getElementById('createVariantForm');

        const modalTitle = document.getElementById('modalProductName');
        const modalSkuPrefix = document.getElementById('modalSkuPrefix');

        addButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const productId = this.getAttribute('data-product-id');
                const productName = this.getAttribute('data-product-name');
                const productSku = this.getAttribute('data-product-sku');
                
                if (createForm) {
                    createForm.action = `/admin/products/${productId}/variants`;
                }

                if (modalTitle) modalTitle.textContent = productName;
                if (modalSkuPrefix) modalSkuPrefix.textContent = productSku;

                const refInput = document.getElementById('create_product_ref_id');
                if (refInput) refInput.value = productId;

                const nameInput = document.getElementById('modal_product_name_input');
                if (nameInput) nameInput.value = productName;

                const prefixInput = document.getElementById('modal_sku_prefix_input');
                if (prefixInput) prefixInput.value = productSku;

                createVariantModal.show();
            });
        });
    }


    const editVariantModalEl = document.getElementById('editVariantModal');
    if (editVariantModalEl) {
        const editVariantModal = new bootstrap.Modal(editVariantModalEl);
        const editForm = document.getElementById('editVariantForm');

        const editSkuInput = document.getElementById('edit_sku');
        const editCostInput = document.getElementById('edit_standard_cost');
        const editPriceInput = document.getElementById('edit_list_price');
        const editSkuDisplay = document.getElementById('editModalSku');
        const attributeSelects = document.querySelectorAll('.attribute-select');

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.btn-edit-variant');
            if (!btn) return;

            e.preventDefault();

            const id = btn.dataset.id;
            const sku = btn.dataset.sku;
            const cost = btn.dataset.standardCost;
            const price = btn.dataset.listPrice;
            
            let selectedAttributes = [];
            try {
                selectedAttributes = JSON.parse(btn.dataset.attributes || '[]');
            } catch (err) {
                console.error('Lỗi parse attributes:', err);
            }

            if (editForm) {
                editForm.action = `/admin/product_variants/${id}`;
            }

            if (editSkuInput) editSkuInput.value = sku;
            if (editCostInput) editCostInput.value = cost;
            if (editPriceInput) editPriceInput.value = price;
            if (editSkuDisplay) editSkuDisplay.textContent = sku;

            const editIdInput = document.getElementById('edit_variant_id');
            if (editIdInput) editIdInput.value = id;

            const skuDisplayInput = document.getElementById('modal_edit_sku_display');
            if (skuDisplayInput) skuDisplayInput.value = sku;

            if (attributeSelects) {
                attributeSelects.forEach(select => {
                    select.value = ""; 
                    Array.from(select.options).forEach(option => {
                        if (selectedAttributes.includes(parseInt(option.value))) {
                            select.value = option.value;
                        }
                    });
                });
            }

            editVariantModal.show();
        });
    }

    const deleteModalEl = document.getElementById("deleteModal");
    if (deleteModalEl) {
        deleteModalEl.addEventListener("show.bs.modal", function (event) {
            const button = event.relatedTarget;
            const variantId = button.getAttribute("data-id");
            const variantName = button.getAttribute("data-name");

            const modalNameEl = document.getElementById("modalVariantName");
            const deleteForm = document.getElementById("deleteForm");

            if (modalNameEl) modalNameEl.textContent = variantName;

            if (deleteForm) {
                // URL: /admin/product_variants/{id}
                deleteForm.action = `/admin/product_variants/${variantId}`;
            }
        });
    }
});