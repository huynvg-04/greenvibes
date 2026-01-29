function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("show");
}

function setTheme(theme) {
    const html = document.documentElement;
    const icon = document.getElementById("theme-icon-active");

    if (theme === "system") {
        const isDark = window.matchMedia(
            "(prefers-color-scheme: dark)"
        ).matches;
        theme = isDark ? "dark" : "light";
        localStorage.removeItem("theme");
    } else {
        localStorage.setItem("theme", theme);
    }

    html.setAttribute("data-theme", theme);
    if (icon) {
        icon.className = theme === "dark" ? "bx bx-moon" : "bx bx-sun";
    }
}

function setLayout(pos) {
    document.body.classList.remove(
        "sidebar-right",
        "layout-top",
        "layout-bottom"
    );

    if (pos === "right") document.body.classList.add("sidebar-right");
    else if (pos === "top") document.body.classList.add("layout-top");
    else if (pos === "bottom") document.body.classList.add("layout-bottom");

    localStorage.setItem("layoutPos", pos);
}

function toggleMenu(header) {
    const group = header.parentElement;
    const isActive = group.classList.contains("active");
    const isHorizontal =
        document.body.classList.contains("layout-top") ||
        document.body.classList.contains("layout-bottom");

    if (isHorizontal) {
        document.querySelectorAll(".nav-group.active").forEach((el) => {
            if (el !== group) el.classList.remove("active");
        });
    }

    group.classList.toggle("active");
}

// 5. Khởi tạo trạng thái ban đầu (IIFE)
(function () {
    // Theme Init
    const savedTheme = localStorage.getItem("theme");
    const systemDark = window.matchMedia(
        "(prefers-color-scheme: dark)"
    ).matches;

    // Logic ưu tiên: Đã lưu -> System
    let initialTheme = "light";
    if (savedTheme) {
        initialTheme = savedTheme;
    } else if (systemDark) {
        initialTheme = "dark";
    }

    document.documentElement.setAttribute("data-theme", initialTheme);
    // Lưu ý: Icon chưa được update ở đây vì DOM có thể chưa load xong icon
    // Việc update icon sẽ được thực hiện lại khi gọi setTheme() hoặc manual update sau khi load

    // Layout Init
    const savedLayout = localStorage.getItem("layoutPos") || "left";
    // Đợi DOM load xong để add class vào body an toàn hơn,
    // nhưng để tránh flicker layout, ta có thể check document.body ngay nếu script đặt cuối body
    if (document.body) {
        if (savedLayout === "right")
            document.body.classList.add("sidebar-right");
        else if (savedLayout === "top")
            document.body.classList.add("layout-top");
        else if (savedLayout === "bottom")
            document.body.classList.add("layout-bottom");
    }
})();

// 6. DOM Content Loaded Events
document.addEventListener("DOMContentLoaded", function () {
    // Re-apply theme icon cho chắc chắn sau khi DOM load
    const savedTheme =
        localStorage.getItem("theme") ||
        (window.matchMedia("(prefers-color-scheme: dark)").matches
            ? "dark"
            : "light");
    const icon = document.getElementById("theme-icon-active");
    if (icon)
        icon.className = savedTheme === "dark" ? "bx bx-moon" : "bx bx-sun";

    // Select2 Init
    if (typeof $ !== "undefined" && $(".tags-select").length > 0) {
        $(".tags-select").select2({
            tags: true,
            width: "100%",
        });
    }

    // Auto close alert
    setTimeout(function () {
        if (typeof $ !== "undefined" && $(".alert").length > 0) {
            $(".alert").alert("close");
        }
    }, 5000);

    // CKEditor Init
    if (document.getElementById("editor") && typeof CKEDITOR !== "undefined") {
        CKEDITOR.replace("editor", {
            height: 400,
            removeButtons: "",
            versionCheck: false,
        });
    }

    // Handle Layout Menu Click Outside (Cho layout ngang)
    const isHorizontal =
        document.body.classList.contains("layout-top") ||
        document.body.classList.contains("layout-bottom");
    if (isHorizontal) {
        document.querySelectorAll(".nav-group.active").forEach((group) => {
            group.classList.remove("active");
        });
    }

    document.addEventListener("click", function (event) {
        const currentLayoutHorizontal =
            document.body.classList.contains("layout-top") ||
            document.body.classList.contains("layout-bottom");

        if (currentLayoutHorizontal) {
            const isClickInside = event.target.closest(".nav-group");

            if (!isClickInside) {
                document.querySelectorAll(".nav-group.active").forEach((el) => {
                    el.classList.remove("active");
                });
            }
        }
    });

    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Global modal hidden event listener to clear forms
    document.body.addEventListener('hidden.bs.modal', function (event) {
        const modal = event.target;
        // Only target modals with forms inside
        const form = modal.querySelector('form');
        // Optional: check modal ID to avoid clearing specific modals if needed, or use data attribute
        if (form && !modal.hasAttribute('data-no-clear')) {
            form.reset();
            
            // Clear validation states
            form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            
            // Reset Select2 if present
            if (typeof $ !== 'undefined') {
                $(form).find('select.select2-hidden-accessible').val(null).trigger('change');
            }
            
            // Reset image previews if any
            form.querySelectorAll('img.preview-image').forEach(img => img.src = '');

            // Reset specific inputs that might need manual intervention (like tags input)
            // Example: Tagify reset logic could go here if needed
        }
    }, true);
});