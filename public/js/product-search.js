const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
        }
    });
}, observerOptions);

document.addEventListener("DOMContentLoaded", () => {
    const productCards = document.querySelectorAll(".product-card");
    productCards.forEach((card, index) => {
        card.style.opacity = "0";
        card.style.transform = "translateY(20px)";
        card.style.transition = `opacity 0.6s ease ${
            index * 0.1
        }s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    const searchResults = document.querySelector(".products-grid");
    if (searchResults) {
        const resultsCount = document.querySelector(
            ".search-results-count"
        ).textContent;
        searchResults.setAttribute("aria-label", resultsCount);
    }

    const productLinks = document.querySelectorAll(".product-link");
    productLinks.forEach((link) => {
        link.addEventListener("keydown", function (e) {
            if (e.key === "Enter" || e.key === " ") {
                e.preventDefault();
                this.click();
            }
        });
    });

    const query = "{{ $query }}";
    if (query.length > 2) {
        highlightSearchTerms(query);
    }
});

function highlightSearchTerms(query) {
    const searchTerms = query
        .toLowerCase()
        .split(" ")
        .filter((term) => term.length > 2);
    const productTitles = document.querySelectorAll(".product-title");
    const productDescriptions = document.querySelectorAll(
        ".product-description"
    );

    [...productTitles, ...productDescriptions].forEach((element) => {
        let content = element.textContent;
        searchTerms.forEach((term) => {
            const regex = new RegExp(`(${term})`, "gi");
            content = content.replace(
                regex,
                '<mark class="highlight">$1</mark>'
            );
        });
        if (content !== element.textContent) {
            element.innerHTML = content;
        }
    });
}

if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const img = entry.target;
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                    img.removeAttribute("data-src");
                    imageObserver.unobserve(img);
                }
            }
        });
    });

    // Apply lazy loading to images (if needed)
    document.querySelectorAll("img[data-src]").forEach((img) => {
        imageObserver.observe(img);
    });
}

// Add smooth scroll to top when clicking product links
document.querySelectorAll(".product-link").forEach((link) => {
    link.addEventListener("click", function () {
        // Add loading state
        this.style.opacity = "0.7";
    });
});
