document.addEventListener("DOMContentLoaded", function () {
    const config = window.productConfig;
    if (!config) return;

    const productVariants = config.variants;
    const totalAttrTypes = config.totalAttrTypes;

    const variantInputs = document.querySelectorAll(".variant-input");

    variantInputs.forEach((input) => {
        input.addEventListener("click", function (e) {
            const currentName = this.name;

            if (this.getAttribute("data-was-checked") === "true") {
                this.checked = false;
                this.setAttribute("data-was-checked", "false");
                document
                    .querySelectorAll(`input[name="${currentName}"]`)
                    .forEach((el) =>
                        el.setAttribute("data-was-checked", "false")
                    );
            } else {
                document
                    .querySelectorAll(`input[name="${currentName}"]`)
                    .forEach((el) =>
                        el.setAttribute("data-was-checked", "false")
                    );
                this.setAttribute("data-was-checked", "true");
            }

            handleSelectAttribute();
        });
    });

    function handleSelectAttribute() {
        const checkedInputs = document.querySelectorAll(
            ".variant-input:checked"
        );
        const selectedAttrs = {};

        checkedInputs.forEach((input) => {
            selectedAttrs[input.dataset.attrName] = parseInt(input.value);
        });

        if (Object.keys(selectedAttrs).length < totalAttrTypes) {
            resetUI();
            return;
        }

        const matchedVariant = productVariants.find((variant) => {
            const variantAttrIds = variant.attribute_values.map((v) =>
                parseInt(v.id)
            );
            return Object.values(selectedAttrs).every((id) =>
                variantAttrIds.includes(id)
            );
        });

        updateUI(matchedVariant);
    }

    function resetUI() {
        const priceDisplay = document.getElementById("price-display");
        const stockDisplay = document.getElementById("stock-display");
        const skuDisplay = document.getElementById("sku-display");
        const hiddenInput = document.getElementById("selected_variant_id");
        const btnAdd = document.getElementById("btn-add-to-cart");
        const qtyInput = document.getElementById("quantity");

        if (priceDisplay) priceDisplay.innerHTML = config.defaultPriceHTML;
        window.currentStock = config.defaultStock;

        if (stockDisplay) {
            if (config.defaultStock > 0) {
                stockDisplay.innerHTML = `<span class="text-muted">Chọn phân loại sản phẩm</span>`;
            } else {
                stockDisplay.innerHTML = `<span class="text" style="color: var(--color-dark)">Hết hàng</span>`;
            }
        }

        if (skuDisplay) skuDisplay.innerText = "---";
        if (hiddenInput) hiddenInput.value = "";

        if (btnAdd) {
            btnAdd.disabled = true;
            btnAdd.innerHTML = "Chọn phân loại sản phẩm";
        }

        if (qtyInput) {
            qtyInput.value = 1;
            qtyInput.max = window.currentStock;
        }
    }

    function updateUI(variant) {
        const priceDisplay = document.getElementById("price-display");
        const stockDisplay = document.getElementById("stock-display");
        const skuDisplay = document.getElementById("sku-display");
        const hiddenInput = document.getElementById("selected_variant_id");
        const btnAdd = document.getElementById("btn-add-to-cart");
        const qtyInput = document.getElementById("quantity");

        if (variant) {
            const price = new Intl.NumberFormat("vi-VN").format(
                variant.list_price
            );
            if (priceDisplay) priceDisplay.innerHTML = `${price}₫`;

            if (skuDisplay) skuDisplay.innerText = variant.sku;
            if (hiddenInput) hiddenInput.value = variant.id;

            window.currentStock = variant.stock;

            if (qtyInput) {
                qtyInput.value = 1;
                qtyInput.max = window.currentStock;
            }

            if (variant.stock > 0) {
                if (stockDisplay)
                    stockDisplay.innerHTML = `<span class="text" style="color: var(--color-dark)"> Còn hàng: ${variant.stock} sản phẩm</span>`;
                if (btnAdd) {
                    btnAdd.disabled = false;
                    btnAdd.innerHTML =
                        '<i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng';
                }
            } else {
                if (stockDisplay)
                    stockDisplay.innerHTML = `<span class="text" style="color: var(--color-dark)">Hết hàng</span>`;
                if (btnAdd) {
                    btnAdd.disabled = true;
                    btnAdd.innerHTML = "Tạm hết hàng";
                }
            }

            if (variant.image && typeof changeMainImage === "function") {
                changeMainImage(`${config.storageUrl}/${variant.image}`);
            }
        } else {
            if (priceDisplay)
                priceDisplay.innerHTML = '<span class="text-muted">---</span>';
            if (stockDisplay)
                stockDisplay.innerHTML =
                    '<span class="text-danger">Phân loại này không khả dụng</span>';
            if (btnAdd) btnAdd.disabled = true;
            if (hiddenInput) hiddenInput.value = "";
        }
    }

    const cartForm = document.querySelector(".cart-form");

    if (cartForm) {
        cartForm.addEventListener("submit", function (e) {
            const btn = document.getElementById("btn-add-to-cart");

            if (btn.disabled) {
                e.preventDefault();
                return;
            }

            btn.disabled = true;

            btn.setAttribute("data-original-text", btn.innerHTML);
            btn.innerHTML =
                '<span class="btn-spinner"></span> Đang xử lý...';
        });
    }
});

window.addEventListener("pageshow", function (event) {
    const btn = document.getElementById("btn-add-to-cart");

    if (btn) {
        const originalText = btn.getAttribute("data-original-text");
        if (originalText) {
            btn.innerHTML = originalText;
        } else if (btn.innerHTML.includes("Đang xử lý")) {
            btn.innerHTML =
                '<i class="fas fa-shopping-cart me-2"></i> Thêm vào giỏ hàng';
        }

        const config = window.productConfig;
        const hiddenInput = document.getElementById("selected_variant_id");

        if (config) {
            const totalAttrTypes = config.totalAttrTypes;
            const selectedVariantId = hiddenInput ? hiddenInput.value : "";

            if (totalAttrTypes > 0 && !selectedVariantId) {
                btn.disabled = true;
            } else {
                if (window.currentStock > 0) {
                    btn.disabled = false;
                }
            }
        } else {
            btn.disabled = false;
        }
    }
});


function checkMaxQuantity(input) {
    let max = parseInt(input.max);
    if (isNaN(max) || max < 1) max = window.currentStock || 999;

    let val = parseInt(input.value);
    if (isNaN(val) || val < 1) val = 1;

    if (val > max) input.value = max;
    else input.value = val;
}

function increaseQuantity() {
    const input = document.getElementById("quantity");
    if (!input) return;
    let val = parseInt(input.value);
    let max = parseInt(input.max);
    if (isNaN(max) || max < 1) max = window.currentStock || 999;
    if (val < max) input.value = val + 1;
}

function decreaseQuantity() {
    const input = document.getElementById("quantity");
    if (!input) return;
    let val = parseInt(input.value);
    if (val > 1) input.value = val - 1;
}

function changeMainImage(src) {
    const img = document.getElementById("mainImage");
    if (img) img.src = src;
}

const hoverImage = document.querySelector(".zoom-hover-image");
if (hoverImage) {
    hoverImage.addEventListener("mousemove", function (e) {
        const rect = this.getBoundingClientRect();
        const x = ((e.clientX - rect.left) / rect.width) * 100;
        const y = ((e.clientY - rect.top) / rect.height) * 100;
        this.style.transformOrigin = `${x}% ${y}%`;
        this.style.transform = "scale(1.7)";
    });

    hoverImage.addEventListener("mouseleave", function () {
        this.style.transform = "scale(1)";
        this.style.transformOrigin = "center center";
    });
}

function openProductTab(evt, tabName) {
    var i, tabcontent, tablinks;

    tabcontent = document.getElementsByClassName("tab-content-panel");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].classList.remove("active");
    }

    tablinks = document.getElementsByClassName("tab-link");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].classList.remove("active");
    }

    document.getElementById(tabName).classList.add("active");
    evt.currentTarget.classList.add("active");
}

document.addEventListener("DOMContentLoaded", function () {
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute("content");

    document.querySelectorAll(".btn-like-review").forEach((button) => {
        button.addEventListener("click", function (e) {
            e.preventDefault();

            let btn = this;
            let tooltip = btn.parentElement.querySelector(".login-tooltip");

            // --- [MỚI] KIỂM TRA ĐĂNG NHẬP NGAY TẠI ĐÂY ---
            if (!window.isLoggedIn) {
                // Nếu chưa đăng nhập: Hiện tooltip ngay và DỪNG LẠI (return)
                if (tooltip) {
                    tooltip.classList.add("show");
                    setTimeout(() => tooltip.classList.remove("show"), 3000);
                } else {
                    // Nếu không có tooltip thì chuyển trang login
                    window.location.href = "{{ route('login') }}";
                }
                return; // <--- Dừng code tại đây, không cho đổi màu, không gọi server
            }
            // ------------------------------------------------

            // Nếu đã đăng nhập thì chạy tiếp logic Optimistic UI như cũ
            let reviewId = btn.getAttribute("data-id");
            let icon = btn.querySelector("i");
            let countSpan = btn.querySelector(".like-count");

            let isLiked = btn.classList.contains("active-like");

            // 1. ĐỔI GIAO DIỆN NGAY LẬP TỨC
            if (isLiked) {
                // BỎ LIKE
                btn.classList.remove("active-like");
                btn.classList.add("btn-light", "text-secondary");
                icon.className = "bx bx-heart me-1 icon-heart";
                countSpan.innerText = Math.max(
                    0,
                    parseInt(countSpan.innerText) - 1
                );
            } else {
                // LIKE
                btn.classList.remove("btn-light", "text-secondary");
                btn.classList.add("active-like");
                icon.className = "bx bxs-heart me-1 icon-heart";
                countSpan.innerText = parseInt(countSpan.innerText) + 1;
            }

            // 2. GỬI REQUEST
            let url = "/reviews/" + reviewId + "/like";

            fetch(url, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
            }).catch((error) => console.error("Lỗi:", error));
        });
    });
});
