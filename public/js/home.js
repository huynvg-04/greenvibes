const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
            // Tùy chọn: Ngừng quan sát sau khi hiện
            // observer.unobserve(entry.target); 
        }
    });
}, observerOptions);

document.addEventListener("DOMContentLoaded", () => {
    const productCards = document.querySelectorAll(".product-card");
    productCards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        // Stagger effect: Mỗi card hiện trễ hơn card trước 0.1s
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });
});

document.addEventListener("DOMContentLoaded", function() {
    setTimeout(function() {
        document.querySelectorAll(".alert-auto").forEach(function(el) {
            el.style.transition = "opacity 0.5s ease";
            el.style.opacity = "0";
            setTimeout(() => el.remove(), 500);
        });
    }, 4000);
});

// 3. Hiệu ứng 'show' class cho product-card (Observer thứ 2)
// Lưu ý: Đoạn này hơi trùng với đoạn 1, nhưng nếu CSS dùng class .show thì vẫn cần.
document.addEventListener("DOMContentLoaded", () => {
    const products = document.querySelectorAll(".product-card");
    const showObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("show");
                    showObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
        }
    );

    products.forEach((product) => showObserver.observe(product));
});

// 4. Hiệu ứng fade-in cho các phần tử chung (.fade-in)
document.addEventListener("DOMContentLoaded", function() {
    const items = document.querySelectorAll(".fade-in");

    const fadeObserver = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("visible");
                    fadeObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2,
        }
    );

    items.forEach((item) => fadeObserver.observe(item));
});

// 5. Phân trang cho Danh mục (Categories Grid Pagination)
document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById("categoriesGrid");
    const pagination = document.getElementById("categoriesPagination");
    const prevBtn = document.getElementById("catPrev");
    const nextBtn = document.getElementById("catNext");
    const pageInfo = document.getElementById("catPageInfo");

    if (!grid) return;

    const cards = Array.from(grid.children);
    let currentPage = 1;
    let itemsPerPage = 1; 
    let totalPages = 1;

    function computeItemsPerPage() {
        const gridStyles = window.getComputedStyle(grid);
        let columns = 1;
        const colTemplate = gridStyles.getPropertyValue("grid-template-columns");
        
        if (colTemplate && colTemplate !== 'none') {
            columns = colTemplate.split(" ").length;
        } else {
            const gridWidth = grid.clientWidth;
            const card = grid.querySelector(".category-card");
            if (card)
                columns = Math.max(1, Math.floor(gridWidth / card.clientWidth));
        }

        // Logic: Hiển thị 2 hàng (số cột * 2)
        itemsPerPage = Math.max(1, columns * 2);
    }

    function renderPage() {
        const start = (currentPage - 1) * itemsPerPage;
        const end = start + itemsPerPage;

        cards.forEach((card, idx) => {
            if (idx >= start && idx < end) {
                card.style.display = "";
            } else {
                card.style.display = "none";
            }
        });

        totalPages = Math.max(1, Math.ceil(cards.length / itemsPerPage));
        if (totalPages > 1 && pagination) {
            pagination.hidden = false;
            if(pageInfo) pageInfo.textContent = `Trang ${currentPage} / ${totalPages}`;
        } else if (pagination) {
            pagination.hidden = true;
        }

        if(prevBtn) prevBtn.disabled = currentPage <= 1;
        if(nextBtn) nextBtn.disabled = currentPage >= totalPages;
    }

    function recalcAndRender() {
        // Show all để đo đạc chính xác
        cards.forEach((c) => (c.style.display = ""));
        computeItemsPerPage();
        currentPage = Math.min(
            currentPage,
            Math.ceil(cards.length / itemsPerPage) || 1
        );
        renderPage();
    }

    if(prevBtn) {
        prevBtn.addEventListener("click", () => {
            if (currentPage > 1) {
                currentPage--;
                renderPage();
                scrollToElementWithOffset(grid, 20);
            }
        });
    }

    if(nextBtn) {
        nextBtn.addEventListener("click", () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderPage();
                scrollToElementWithOffset(grid, 20);
            }
        });
    }

    function scrollToElementWithOffset(el, extraOffset = 0) {
        if (!el) return;
        const headerSelectors = [
            "header.site-header", "header", ".navbar", ".main-nav", "#navbar", ".top-navbar"
        ];
        let offset = extraOffset;
        for (const sel of headerSelectors) {
            const hdr = document.querySelector(sel);
            if (hdr) {
                const style = window.getComputedStyle(hdr);
                if (style.position === "fixed" || style.position === "sticky") {
                    offset += hdr.getBoundingClientRect().height;
                    break; 
                }
            }
        }

        const rect = el.getBoundingClientRect();
        const targetY = window.scrollY + rect.top - offset - 8;
        window.scrollTo({
            top: Math.max(0, targetY),
            behavior: "smooth",
        });
    }

    let resizeTimer = null;
    window.addEventListener("resize", () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(recalcAndRender, 150);
    });

    recalcAndRender();
});

document.addEventListener("DOMContentLoaded", () => {
    const grid = document.getElementById("mainProductsGrid");
    const loadMoreWrapper = document.getElementById("productsLoadMoreWrapper");
    const loadMoreBtn = document.getElementById("productsLoadMoreBtn");
    
    if (!grid || !loadMoreBtn) return;

    const items = Array.from(grid.querySelectorAll(".product-card"));
    const batchSize = 15; // Số lượng hiện thêm mỗi lần
    let visibleCount = Math.min(batchSize, items.length);

    function updateVisibility() {
        items.forEach((it, idx) => {
            if (idx < visibleCount) {
                it.style.display = "";
                // Thêm class show để kích hoạt animation nếu có CSS tương ứng
                requestAnimationFrame(() => it.classList.add("show"));
            } else {
                it.style.display = "none";
                it.classList.remove("show");
            }
        });

        if (visibleCount >= items.length) {
            if(loadMoreWrapper) loadMoreWrapper.style.display = "none";
        } else {
            if(loadMoreWrapper) loadMoreWrapper.style.display = "";
        }
    }

    loadMoreBtn.addEventListener("click", () => {
        visibleCount = Math.min(items.length, visibleCount + batchSize);
        updateVisibility();
    });

    // Initial setup
    updateVisibility();
});