function toggleOrderDetails(orderId, btn) {
    const details = document.getElementById(`details-${orderId}`);
    const icon = btn.querySelector(".toggle-icon");

    if (details.style.display === "none" || details.style.display === "") {
        details.style.display = "block";
        icon.classList.add("rotate");
    } else {
        details.style.display = "none";
        icon.classList.remove("rotate");
    }
}

document.addEventListener("DOMContentLoaded", function () {
    var cancelModal = document.getElementById("cancelOrderModal");
    if (cancelModal) {
        cancelModal.addEventListener("show.bs.modal", function (event) {
            var button = event.relatedTarget;
            var actionUrl = button.getAttribute("data-action");
            document.getElementById("cancelOrderForm").action = actionUrl;
        });
    }

    var receiveModal = document.getElementById("confirmReceiveModal");
    if (receiveModal) {
        receiveModal.addEventListener("show.bs.modal", function (event) {
            var button = event.relatedTarget;
            var actionUrl = button.getAttribute("data-action");
            var paymentMethod = button.getAttribute("data-payment-method");

            document.getElementById("confirmReceiveForm").action = actionUrl;
            var textElement = document.getElementById("receive-confirm-text");

            if (paymentMethod === "cod") {
                textElement.innerHTML = `
                        <div class="alert alert-warning border-0 mb-0 d-flex align-items-center">
                            <i class='bx bx-money fa-2x me-3'></i> 
                            <div>Xác nhận <strong>đã nhận hàng</strong> và <strong>đã thanh toán</strong>?</div>
                        </div>`;
            } else {
                textElement.innerHTML = `
                        <div class="alert alert-success border-0 mb-0 d-flex align-items-center">
                            <i class='bx bx-check-circle fa-2x me-3'></i>
                            <div>Xác nhận <strong>đã nhận đủ hàng</strong> và hài lòng?</div>
                        </div>`;
            }
        });
    }

    $(".upload-box").click(function (e) {
        if ($(e.target).hasClass("remove-btn")) return;
        if ($(this).hasClass("has-image")) return;
        $($(this).data("target")).click();
    });

    $(".file-input-custom").change(function () {
        var input = this;
        var box = $(`.upload-box[data-target="#${input.id}"]`);

        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                box.find(".plus-icon").addClass("d-none");
                box.find(".img-preview")
                    .attr("src", e.target.result)
                    .removeClass("d-none");
                box.find(".remove-btn").removeClass("d-none");
                box.addClass("has-image border-primary").css(
                    "border-style",
                    "solid",
                );
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    $(".remove-btn").click(function (e) {
        e.stopPropagation();
        var box = $(this).closest(".upload-box");
        $($(box).data("target")).val("");

        box.find(".img-preview").attr("src", "").addClass("d-none");
        box.find(".plus-icon").removeClass("d-none");
        $(this).addClass("d-none");
        box.removeClass("has-image border-primary").css(
            "border-style",
            "dashed",
        );
    });

    const reviewModal = document.getElementById("reviewModal");
    const stars = document.querySelectorAll(".rating-stars .star");
    const ratingInput = document.getElementById("review-rating");

    reviewModal.addEventListener("hidden.bs.modal", function () {
        document.body.focus();
    });

    function renderStars(value) {
        stars.forEach((s) => {
            s.classList.remove("bx-star", "bxs-star");

            if (s.getAttribute("data-value") <= value) {
                s.classList.add("bxs-star");
            } else {
                s.classList.add("bx-star");
            }
        });
    }

    stars.forEach((star) => {
        star.addEventListener("click", function () {
            const val = this.getAttribute("data-value");
            ratingInput.value = val;
            renderStars(val);
        });

        star.addEventListener("mouseover", function () {
            renderStars(this.getAttribute("data-value"));
        });
    });

    document
        .querySelector(".rating-stars")
        .addEventListener("mouseleave", function () {
            renderStars(ratingInput.value);
        });

    // KHI MỞ MODAL
    if (reviewModal) {
        reviewModal.addEventListener("show.bs.modal", function (event) {
            var button = event.relatedTarget;
            document.getElementById("review-item-id").value =
                button.getAttribute("data-item-id");
            document.getElementById("review-product-name").innerText =
                button.getAttribute("data-product-name");

            document.getElementById("reviewForm").reset();

            // Reset Sao về 5
            ratingInput.value = 5;
            renderStars(5);

            // Reset Ảnh
            $(".file-input-custom").val("");
            $(".upload-box").each(function () {
                $(this)
                    .removeClass("has-image border-primary")
                    .css("border-style", "dashed");
                $(this).find(".img-preview").attr("src", "").addClass("d-none");
                $(this).find(".plus-icon").removeClass("d-none");
                $(this).find(".remove-btn").addClass("d-none");
            });
        });
    }

    // SUBMIT FORM (ĐÃ XÓA ALERT)
    const reviewForm = document.getElementById("reviewForm");
    if (reviewForm) {
        reviewForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML =
                '<span class="spinner-border spinner-border-sm"></span> Đang gửi...';

            fetch(
                `/reviews/${document.getElementById("review-item-id").value}`,
                {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: new FormData(this),
                },
            )
                .then((res) => res.json())
                .then((data) => {
                    if (data.success) {
                        // Đã xóa alert('Đánh giá thành công!');
                        location.reload(); // Chỉ reload trang
                    } else {
                        alert(data.error || "Lỗi xảy ra!");
                    }
                })
                .catch(() => alert("Lỗi kết nối!"))
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Gửi đánh giá";
                });
        });
    }
});
