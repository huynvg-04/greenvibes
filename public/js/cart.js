$(document).ready(function () {
    const config = window.CartConfig;

    $.ajaxSetup({
        headers: { "X-CSRF-TOKEN": config.csrfToken },
    });

    function calcTotal() {
        let total = 0;
        let selectedCount = 0;

        $(".cart-item").each(function () {
            if ($(this).find(".item-check").is(":checked")) {
                let $row = $(this);
                let itemTotal = 0;

                // Logic lấy giá (Mobile/Desktop)
                if ($row.find(".item-total").css("display") === "none") {
                    let unitText =
                        $row.find(".price-current").text() ||
                        $row.find(".unit-price").text();
                    let unit = parseInt(unitText.replace(/\D/g, "")) || 0;
                    let qty = parseInt($row.find(".quantity").val());
                    itemTotal = unit * qty;
                } else {
                    let itemTotalText = $row.find(".item-total").text();
                    itemTotal = parseInt(itemTotalText.replace(/\D/g, "")) || 0;
                }

                total += itemTotal;
                selectedCount++;
            }
        });

        let discount = config.couponDiscount;
        let finalTotal = total - discount;
        if (finalTotal < 0) finalTotal = 0;

        let formattedTotal = finalTotal.toLocaleString("vi-VN") + "₫";

        $("#grandTotal").text(formattedTotal);
        $("#selected-count-text").text(selectedCount);

        // Toggle nút Mua Hàng
        if (selectedCount > 0) {
            $("#checkoutBtn").prop("disabled", false).css("opacity", "1");
        } else {
            $("#checkoutBtn").prop("disabled", true).css("opacity", "0.7");
        }
    }

    // 3. CHECKBOX LOGIC
    $("#checkAll").change(function () {
        let isChecked = $(this).prop("checked");
        $(".item-check").prop("checked", isChecked);
        calcTotal();

        // Gọi API từ Config Routes
        $.post(config.routes.select, {
            select_all: true,
            is_selected: isChecked ? 1 : 0,
        });
    });

    $(".item-check").change(function () {
        let totalItems = $(".item-check").length;
        let checkedItems = $(".item-check:checked").length;
        $("#checkAll").prop(
            "checked",
            totalItems > 0 && totalItems === checkedItems
        );

        calcTotal();

        let $card = $(this).closest(".cart-item");
        $.post(config.routes.select, {
            product_id: $card.data("product-id"),
            product_variant_id: $card.data("variant-id"),
            is_selected: $(this).is(":checked") ? 1 : 0,
        });
    });

    // 4. LOGIC SỐ LƯỢNG & AJAX UPDATE
    function updateCartAPI($row, quantity) {
        let productId = $row.data("product-id");
        let variantId = $row.data("variant-id");
        let $input = $row.find(".quantity");
        let maxStock = parseInt($input.data("stock"));

        // Validate
        if (quantity > maxStock) {
            alert("Số lượng vượt quá tồn kho (" + maxStock + ")!");
            $input.val(maxStock);
            quantity = maxStock;
        }
        if (quantity < 1) quantity = 1;

        // UI Loading
        $row.css("opacity", "0.6");

        $.ajax({
            url: config.routes.update,
            method: "POST",
            data: {
                product_id: productId,
                product_variant_id: variantId,
                quantity: quantity,
            },
            success: function (res) {
                $row.css("opacity", "1");
                if (res.success) {
                    $row.find(".item-total").text(res.item_subtotal_text);
                    $input.val(res.quantity); // Cập nhật lại số chuẩn từ server
                    calcTotal();
                } else {
                    alert(res.message);
                    // Reset lại số cũ nếu lỗi
                    // location.reload();
                }
            },
            error: function () {
                $row.css("opacity", "1");
                alert("Lỗi kết nối server!");
            },
        });
    }

    $(".qty-btn-plus")
        .off("click")
        .click(function (e) {
            e.preventDefault(); // Chặn hành vi mặc định
            let $input = $(this).siblings(".quantity");
            let val = parseInt($input.val());
            let max = parseInt($input.data("stock"));

            if (val < max) {
                let newVal = val + 1;
                $input.val(newVal); // Cập nhật giao diện
                updateCartAPI($(this).closest(".cart-item"), newVal); // Gọi API
            } else {
                alert("Đã đạt giới hạn tồn kho!");
            }
        });

    $(".qty-btn-minus")
        .off("click")
        .click(function (e) {
            e.preventDefault(); // Chặn hành vi mặc định
            let $input = $(this).siblings(".quantity");
            let val = parseInt($input.val());

            if (val > 1) {
                let newVal = val - 1;
                $input.val(newVal); // Cập nhật giao diện
                updateCartAPI($(this).closest(".cart-item"), newVal); // Gọi API
            }
        });

    $(".update-cart")
        .off("change")
        .change(function () {
            let val = parseInt($(this).val());
            if (isNaN(val) || val < 1) val = 1;
            updateCartAPI($(this).closest(".cart-item"), val);
        });

    let itemToDelete = null;
    $(".remove-cart").click(function () {
        itemToDelete = $(this).closest(".cart-item");
        $("#confirmRemoveModal").modal("show");
    });

    $("#confirmRemoveBtn").click(function () {
        if (!itemToDelete) return;

        let productId = itemToDelete.data("product-id");
        let variantId = itemToDelete.data("variant-id");

        $.ajax({
            url: config.routes.remove,
            method: "POST",
            data: { product_id: productId, product_variant_id: variantId },
            success: function (res) {
                if (res.success) {
                    itemToDelete.fadeOut(300, function () {
                        $(this).remove();
                        calcTotal();
                        if ($(".cart-item").length === 0) location.reload();
                    });
                    $("#confirmRemoveModal").modal("hide");
                }
            },
        });
    });

    $("#checkoutBtn").click(function () {
        window.location.href = config.routes.checkout;
    });

    let totalItems = $(".item-check").length;
    let checkedItems = $(".item-check:checked").length;
    if (totalItems > 0 && totalItems === checkedItems) {
        $("#checkAll").prop("checked", true);
    }

    calcTotal();
});
