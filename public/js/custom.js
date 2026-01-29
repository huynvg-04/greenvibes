let lastScrollTop = 0;
let scrollThreshold = 5;
let navbar = document.querySelector(".modern-navbar");

window.addEventListener(
    "scroll",
    function () {
        let scrollTop =
            window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > 50) {
            navbar.classList.add("scrolled");
        } else {
            navbar.classList.remove("scrolled");
        }

        // Ẩn/hiện navbar
        if (Math.abs(scrollTop - lastScrollTop) > scrollThreshold) {
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                // Cuộn xuống - ẩn navbar
                navbar.classList.add("navbar-hidden");
                navbar.classList.remove("navbar-visible");
            } else {
                // Cuộn lên - hiện navbar
                navbar.classList.remove("navbar-hidden");
                navbar.classList.add("navbar-visible");
            }
            lastScrollTop = scrollTop;
        }
    },
    {
        passive: true,
    }
);

class TypingEffect {
    constructor(element) {
        this.element = element;
        this.originalHTML = element.innerHTML;
        this.plainText = element.textContent || element.innerText;
        this.isTyping = false;
        this.autoLoop = false;
        this.loopTimeout = null;

        // Cố định kích thước và tạo structure
        this.setupStructure();
    }

    setupStructure() {
        const rect = this.element.getBoundingClientRect();

        this.element.style.position = "relative";
        this.element.style.display = "inline-block";
        this.element.style.width = Math.ceil(rect.width) + "px";
        this.element.style.height = Math.ceil(rect.height) + "px";

        this.ghostElement = document.createElement("span");
        this.ghostElement.innerHTML = this.originalHTML;
        this.ghostElement.style.position = "absolute";
        this.ghostElement.style.top = "4px";
        this.ghostElement.style.left = "0";
        this.ghostElement.style.opacity = "0.25";
        this.ghostElement.style.pointerEvents = "none";
        this.ghostElement.style.width = "100%";

        this.contentElement = document.createElement("span");
        this.contentElement.style.position = "relative";
        this.contentElement.style.zIndex = "2";

        this.element.innerHTML = "";
        this.element.appendChild(this.ghostElement);
        this.element.appendChild(this.contentElement);

        this.contentElement.innerHTML = "";
    }

    showGhost() {
        this.ghostElement.style.opacity = "0.25";
    }

    hideGhost() {
        this.ghostElement.style.opacity = "0";
    }

    async typeText(originalHTML, speed = 100) {
        this.isTyping = true;
        this.contentElement.innerHTML = "";
        this.showGhost();
        this.element.classList.add("typing-cursor");

        const parts = this.parseHTML(originalHTML);
        let currentHTML = "";

        for (let part of parts) {
            if (!this.isTyping) break;

            if (part.type === "text") {
                for (let i = 0; i < part.content.length; i++) {
                    if (!this.isTyping) break;
                    currentHTML += part.content[i];
                    this.contentElement.innerHTML = currentHTML;
                    await this.delay(speed);
                }
            } else if (part.type === "strong") {
                currentHTML += "<strong>";
                this.contentElement.innerHTML = currentHTML;

                for (let i = 0; i < part.content.length; i++) {
                    if (!this.isTyping) break;
                    const strongContent = part.content.substring(0, i + 1);
                    this.contentElement.innerHTML = currentHTML.replace(
                        "<strong>",
                        "<strong>" + strongContent
                    );
                    await this.delay(speed);
                }

                currentHTML =
                    currentHTML.replace("<strong>", "<strong>" + part.content) +
                    "</strong>";
            }
        }

        this.hideGhost();
        this.element.classList.remove("typing-cursor");
        this.isTyping = false;

        if (this.autoLoop) {
            this.loopTimeout = setTimeout(() => {
                this.startEffect();
            }, 2000);
        }
    }

    async eraseText(speed = 50) {
        this.isTyping = true;
        this.element.classList.add("typing-cursor");
        this.showGhost();

        const parts = this.parseHTML(this.originalHTML);
        const totalLength = this.plainText.length;

        for (
            let currentLength = totalLength;
            currentLength >= 0;
            currentLength--
        ) {
            if (!this.isTyping) break;

            let builtHTML = "";
            let charCount = 0;

            for (let part of parts) {
                if (charCount >= currentLength) break;

                if (part.type === "text") {
                    const remainingChars = currentLength - charCount;
                    const textToShow = part.content.substring(
                        0,
                        remainingChars
                    );
                    builtHTML += textToShow;
                    charCount += textToShow.length;
                } else if (part.type === "strong") {
                    const remainingChars = currentLength - charCount;
                    if (remainingChars > 0) {
                        const textToShow = part.content.substring(
                            0,
                            remainingChars
                        );
                        if (textToShow) {
                            builtHTML += "<strong>" + textToShow + "</strong>";
                        }
                        charCount += textToShow.length;
                    }
                }
            }

            this.contentElement.innerHTML = builtHTML;
            await this.delay(speed);
        }
    }

    parseHTML(html) {
        const parts = [];
        const tempDiv = document.createElement("div");
        tempDiv.innerHTML = html;

        for (let node of tempDiv.childNodes) {
            if (node.nodeType === Node.TEXT_NODE) {
                if (node.textContent.trim()) {
                    parts.push({
                        type: "text",
                        content: node.textContent,
                    });
                }
            } else if (node.nodeType === Node.ELEMENT_NODE) {
                if (node.tagName === "STRONG") {
                    parts.push({
                        type: "strong",
                        content: node.textContent,
                    });
                }
            }
        }

        return parts;
    }

    async startEffect() {
        if (this.isTyping) return;

        if (this.contentElement.innerHTML.trim() !== "") {
            await this.eraseText(75);
            await this.delay(300);
        }

        this.showGhost();
        await this.delay(500);
        await this.typeText(this.originalHTML, 100);
    }

    stop() {
        this.isTyping = false;
        this.element.classList.remove("typing-cursor");
        if (this.loopTimeout) {
            clearTimeout(this.loopTimeout);
            this.loopTimeout = null;
        }
    }

    reset() {
        this.stop();
        this.contentElement.innerHTML = this.originalHTML;
        this.hideGhost();
    }

    toggleAutoLoop() {
        this.autoLoop = !this.autoLoop;

        if (!this.autoLoop && this.loopTimeout) {
            clearTimeout(this.loopTimeout);
            this.loopTimeout = null;
        }
    }

    delay(ms) {
        return new Promise((resolve) => setTimeout(resolve, ms));
    }
}

// CSS
const style = document.createElement("style");
style.textContent = `
            .typing-cursor::after {
                content: '|';
                color: var(--color-accent);
                animation: blink 1s infinite;
                margin-left: 2px;
            }

            @keyframes blink {
                0%, 50% { opacity: 1; }
                51%, 100% { opacity: 0; }
            }
            `;
document.head.appendChild(style);

const logo = document.getElementById("GreenVibesLogo");
const typingEffect = new TypingEffect(logo);
typingEffect.startEffect();
typingEffect.toggleAutoLoop();

// $(document).ready(function() {
//     $(document).bind('contextmenu', function(e) {
//         return false;
//     })
// })

$(document).ready(function () {
    $("#search-input").on("keyup", function () {
        let query = $(this).val();
        if (query.length > 0) {
            $.ajax({
                url: "{{ route('products.suggestions') }}",
                type: "GET",
                data: {
                    query: query,
                },
                success: function (data) {
                    let suggestions = "";

                    data.forEach((item) => {
                        let shortName =
                            item.name.length > 25
                                ? item.name.substring(0, 25) + "…"
                                : item.name;

                        let imgUrl = null;

                        if (
                            item.primary_image &&
                            item.primary_image.image_url
                        ) {
                            imgUrl = item.primary_image.image_url;
                        } else if (item.images && item.images.length > 0) {
                            let primary = item.images.find(
                                (img) =>
                                    img.is_primary == 1 ||
                                    img.is_primary == true
                            );

                            imgUrl = primary
                                ? primary.image_url
                                : item.images[0].image_url;
                        }

                        let imageSrc = imgUrl
                            ? `/storage/${imgUrl}`
                            : "/images/no-image.png";

                        suggestions += `
            <a href="/san-pham/${item.slug}" class="list-group-item list-group-item-action d-flex align-items-center" role="option">
                <img src="${imageSrc}" alt="${item.name}" 
                     style="width: 40px; height: 40px; object-fit: cover; margin-right: 10px;">
                <span class="suggest-name" style="flex:1;">${shortName}</span>
            </a>`;
                    });

                    $("#suggestions").html(suggestions).show();
                },
            });
        } else {
            $("#suggestions").hide();
        }
    });

    $(document).on("click", "#suggestions a", function () {
        $("#search-input").val($(this).text());
        $("#suggestions").hide();
    });

    $(document).click(function (e) {
        if (!$(e.target).closest("#search-form").length) {
            $("#suggestions").hide();
        }
    });

    setTimeout(function () {
        $(".page-loader").fadeOut();
    }, 200);

    $(window).scroll(function () {
        if ($(this).scrollTop() > 50) {
            $(".modern-navbar").addClass("scrolled");
        } else {
            $(".modern-navbar").removeClass("scrolled");
        }
    });

    $("#search-input").on("keydown", function (e) {
        const suggestions = $("#suggestions a");
        const current = suggestions.filter(".active");

        if (e.key === "ArrowDown") {
            e.preventDefault();
            if (current.length === 0) {
                suggestions.first().addClass("active").focus();
            } else {
                const next = current.removeClass("active").next();
                if (next.length) {
                    next.addClass("active").focus();
                } else {
                    suggestions.first().addClass("active").focus();
                }
            }
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            if (current.length === 0) {
                suggestions.last().addClass("active").focus();
            } else {
                const prev = current.removeClass("active").prev();
                if (prev.length) {
                    prev.addClass("active").focus();
                } else {
                    suggestions.last().addClass("active").focus();
                }
            }
        } else if (e.key === "Escape") {
            $("#suggestions").hide();
            $(this).blur();
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const btn = document.getElementById("scrollToTopBtn");
    let currentScrollContainer = window;

    if (!btn) return;

    document.addEventListener(
        "scroll",
        function (event) {
            let scrollY = 0;
            const target = event.target;

            if (target === document) {
                scrollY = window.scrollY;
                currentScrollContainer = window;
            } else if (target.scrollTop !== undefined) {
                scrollY = target.scrollTop;
                currentScrollContainer = target;
            }

            if (scrollY > 300) {
                btn.classList.add("show");
            } else {
                btn.classList.remove("show");
            }
        },
        true
    );

    btn.addEventListener("click", function () {
        currentScrollContainer.scrollTo({ top: 0, behavior: "smooth" });
    });
});
