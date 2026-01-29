document.addEventListener("DOMContentLoaded", function () {
    class TypingEffect {
        constructor(element) {
            this.element = element;
            this.originalHTML = element.innerHTML;
            this.plainText = element.textContent || element.innerText;
            this.isTyping = false;
            this.autoLoop = false;
            this.loopTimeout = null;
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
            this.ghostElement.style.top = "4";
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
                        currentHTML.replace(
                            "<strong>",
                            "<strong>" + part.content
                        ) + "</strong>";
                }
            }
            this.hideGhost();
            this.element.classList.remove("typing-cursor");
            this.isTyping = false;

            if (this.autoLoop) {
                this.loopTimeout = setTimeout(() => {
                    this.startEffect();
                }, 5000);
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
                        const remaining = currentLength - charCount;
                        const text = part.content.substring(0, remaining);
                        builtHTML += text;
                        charCount += text.length;
                    } else if (part.type === "strong") {
                        const remaining = currentLength - charCount;
                        if (remaining > 0) {
                            const text = part.content.substring(0, remaining);
                            if (text)
                                builtHTML += "<strong>" + text + "</strong>";
                            charCount += text.length;
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
                    if (node.textContent)
                        parts.push({ type: "text", content: node.textContent });
                } else if (
                    node.nodeType === Node.ELEMENT_NODE &&
                    node.tagName === "STRONG"
                ) {
                    parts.push({ type: "strong", content: node.textContent });
                }
            }
            return parts;
        }

        async startEffect() {
            if (this.isTyping) return;
            if (this.contentElement.innerHTML.trim() !== "") {
                await this.eraseText(50);
                await this.delay(300);
            }
            this.showGhost();
            await this.delay(500);
            await this.typeText(this.originalHTML, 150);
        }

        stop() {
            this.isTyping = false;
            this.element.classList.remove("typing-cursor");
            if (this.loopTimeout) {
                clearTimeout(this.loopTimeout);
                this.loopTimeout = null;
            }
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

    const style = document.createElement("style");
    style.textContent = `
        .typing-cursor::after {
            content: '︱';
            color: var(--color-accent);
            animation: blink 1s infinite;
            margin-left: -11px;
        }
        @keyframes blink { 0%, 50% { opacity: 1; } 51%, 100% { opacity: 0; } }
    `;
    document.head.appendChild(style);

   

    const logo = document.getElementById("GreenVibesLogo");
    if (logo) {
        const typingEffect = new TypingEffect(logo);
        typingEffect.startEffect();
        typingEffect.toggleAutoLoop();
    }

    let lastScrollTop = 0;
    let scrollThreshold = 5;
    let navbar = document.querySelector(".modern-navbar");

    if (navbar) {
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

                if (Math.abs(scrollTop - lastScrollTop) > scrollThreshold) {
                    if (scrollTop > lastScrollTop && scrollTop > 100) {
                        navbar.classList.add("navbar-hidden");
                        navbar.classList.remove("navbar-visible");
                    } else {
                        navbar.classList.remove("navbar-hidden");
                        navbar.classList.add("navbar-visible");
                    }
                    lastScrollTop = scrollTop;
                }
            },
            { passive: true }
        );
    }

    const btn = document.getElementById("myNewScrollBtn");

    let scrollContainer = window;

    if (!btn) {
        console.error("error");
        return;
    }

    document.addEventListener(
        "scroll",
        function (e) {
            const target = e.target;
            let currentScrollY = 0;

            if (target === document) {
                currentScrollY = window.scrollY;
                scrollContainer = window;
            } else if (target.scrollTop !== undefined) {
                currentScrollY = target.scrollTop;
                scrollContainer = target;
            }

            if (currentScrollY > 300) {
                btn.classList.add("show");
            } else {
                btn.classList.remove("show");
            }
        },
        true
    );

    btn.addEventListener("click", function () {
        scrollContainer.scrollTo({
            top: 0,
            behavior: "smooth",
        });
    });

    setTimeout(function () {
        const loader = document.querySelector(".page-loader");
        if (loader) $(loader).fadeOut();
    }, 200);
});

$(document).ready(function () {
    const config = window.AppConfig || {};

    $("#search-input").on("keyup input", function () {
        let query = $(this).val();

        if (query.length > 0) {
            $("#btn-clear-search").show();
        } else {
            $("#btn-clear-search").hide();
            $("#suggestions").hide();
            return;
        }

        if (config.routes && config.routes.suggestions) {
            $.ajax({
                url: config.routes.suggestions,
                type: "GET",
                data: { query: query },
                success: function (data) {
                    let suggestions = "";
                    if (data.length > 0) {
                        data.forEach((item) => {
                            let shortName =
                                item.name.length > 35
                                    ? item.name.substring(0, 35) + "…"
                                    : item.name;

                            let imgUrl = "/images/no-image.png";

                            if (
                                item.primary_image &&
                                item.primary_image.image_url
                            ) {
                                imgUrl = `/storage/${item.primary_image.image_url}`;
                            } else if (item.images && item.images.length > 0) {
                                let primary = item.images.find(
                                    (img) => img.is_primary == 1
                                );
                                let firstImg = primary
                                    ? primary
                                    : item.images[0];
                                if (firstImg)
                                    imgUrl = `/storage/${firstImg.image_url}`;
                            }

                            let productLink = `/san-pham/${item.slug}`;

                            suggestions += `
                                <a href="${productLink}" class="list-group-item list-group-item-action d-flex align-items-center" role="option">
                                    <img src="${imgUrl}" alt="${item.name}" 
                                         style="width: 40px; height: 40px; object-fit: cover; margin-right: 12px;">
                                    <span class="suggest-name" style="flex:1; font-size: 14px;">${shortName}</span>
                                </a>`;
                        });
                        $("#suggestions").html(suggestions).show();
                    } else {
                        $("#suggestions").hide();
                    }
                },
            });
        }
    });

    $("#btn-clear-search").click(function () {
        $("#search-input").val("").focus();
        $("#suggestions").hide();
        $(this).hide();
    });

    $(document).on("click", "#suggestions a", function (e) {
        // e.preventDefault(); // Nếu muốn click là điền text, còn không thì để nó chuyển trang luôn
        // $("#search-input").val($(this).find('.suggest-name').text());
        // $("#suggestions").hide();
    });

    $(document).click(function (e) {
        if (!$(e.target).closest("#search-form").length) {
            $("#suggestions").hide();
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
                if (next.length) next.addClass("active").focus();
                else suggestions.first().addClass("active").focus();
            }
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            if (current.length === 0) {
                suggestions.last().addClass("active").focus();
            } else {
                const prev = current.removeClass("active").prev();
                if (prev.length) prev.addClass("active").focus();
                else suggestions.last().addClass("active").focus();
            }
        } else if (e.key === "Escape") {
            $("#suggestions").hide();
            $(this).blur();
        } else if (e.key === "Enter") {
            const activeLink = suggestions.filter(".active").attr("href");
            if (activeLink) {
                e.preventDefault();
                window.location.href = activeLink;
            }
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    const body = document.body;
    let lastScrollY = window.scrollY;
    let ticking = false;

    const thresholdNav1 = 50;

    function updateNav() {
        const currentScrollY = window.scrollY;

        
        if (Math.abs(currentScrollY - lastScrollY) < 5) {
            ticking = false;
            return;
        }

        if (currentScrollY > lastScrollY) {
            if (currentScrollY > thresholdNav1) {
                body.classList.add("sticky-nav-2");
            }
            if (currentScrollY > 150) {
                body.classList.add("nav-hidden");
            }
        } else {
            body.classList.remove("nav-hidden");

            if (currentScrollY < thresholdNav1) {
                body.classList.remove("sticky-nav-2");
            }
        }

        lastScrollY = currentScrollY;
        ticking = false;
    }

    window.addEventListener("scroll", () => {
        if (!ticking) {
            window.requestAnimationFrame(updateNav);
            ticking = true;
        }
    });
});
document.addEventListener("DOMContentLoaded", function () {
    let isScrolling;

    window.addEventListener('scroll', function() {
        // Xóa timeout cũ nếu đang cuộn liên tục
        window.clearTimeout(isScrolling);

        // Đặt timeout mới để tránh spam lệnh click quá nhiều
        isScrolling = setTimeout(function() {
            
            // 1. Tìm tất cả các nút Toggle đang có class 'show' (Tức là đang mở)
            const openToggles = document.querySelectorAll('.dropdown-toggle.show');

            // 2. Nếu tìm thấy
            if (openToggles.length > 0) {
                openToggles.forEach(toggle => {
                    // 3. Giả lập một cú click vào nút đó
                    // Bootstrap sẽ hiểu là người dùng bấm đóng menu
                    toggle.click();
                    
                    // (Phòng hờ) Xóa tiêu điểm khỏi nút để nó không bị sáng lên
                    toggle.blur(); 
                });
            }
            
        }, 50); // Độ trễ 50ms: Đủ nhanh để đóng ngay, nhưng không gây lag
    }, { passive: true });
});


$(document).ready(function() {
    if ($('#ai-chat-window').length === 0) return;

    const chatWindow = $('#ai-chat-window');
    const triggerBtn = $('#ai-widget-trigger');
    const closeBtn = $('#close-chat');
    const userInput = $('#user-input');
    const messagesContainer = $('#chat-messages');
    
    const isLoggedIn = $('meta[name="user-logged-in"]').attr('content') === '1';
    
    let tempHistory = []; 

    if (isLoggedIn) {
        $.ajax({
            url: "/chatbot/history",
            method: 'GET',
            success: function(data) {
                if (data.length > 0) messagesContainer.empty();
                
                data.forEach(function(msg) {
                    let sender = msg.role === 'model' ? 'ai' : 'user';
                    appendMessage(sender, msg.content);
                });
                scrollToBottom();
            }
        });
    }

    triggerBtn.click(function() {
        chatWindow.toggleClass('active');
        $(this).find('.notification-badge').remove();
        if (chatWindow.hasClass('active')) {
            userInput.focus();
            scrollToBottom();
        }
    });

    closeBtn.click(function() {
        chatWindow.removeClass('active');
    });

    $('#send-btn').click(sendMessage);
    userInput.keypress(function(e) {
        if (e.which == 13) sendMessage();
    });

    function sendMessage() {
        let text = userInput.val().trim();
        if (text === '') return;

        userInput.val('');

        appendMessage('user', text);

        let loadingId = 'loading-' + Date.now();
        let aiAvatarUrl = "https://cdn-icons-png.flaticon.com/512/4712/4712139.png"; 

        messagesContainer.append(`
            <div id="${loadingId}" class="message ai d-flex gap-2 mb-3">
                <div class="avatar flex-shrink-0">
                    <img src="${aiAvatarUrl}" width="30" height="30" class="rounded-circle bg-light p-1">
                </div>
                <div class="content bg-white p-3 rounded-3 shadow-sm">
                    <div class="typing-dots"><span></span><span></span><span></span></div>
                </div>
            </div>
        `);
        scrollToBottom();

        let payload = {
            message: text,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        if (!isLoggedIn) {
            payload.history = tempHistory;
        }

        $.ajax({
            url: "/chatbot/send", 
            method: 'POST',
            data: payload,
            success: function(response) {
                $('#' + loadingId).remove();
                appendMessage('ai', response.reply);

                if (!isLoggedIn) {
                    tempHistory.push({ role: 'user', content: text });
                    tempHistory.push({ role: 'bot', content: response.reply });
                    
                    if (tempHistory.length > 20) tempHistory = tempHistory.slice(-20);
                }
            },
            error: function(xhr) {
                $('#' + loadingId).remove();
                appendMessage('ai', 'Xin lỗi, kết nối bị gián đoạn. Vui lòng thử lại sau! 😓');
                console.error(xhr.responseText);
            }
        });
    }

    function appendMessage(sender, text) {
        let aiAvatarUrl = "https://cdn-icons-png.flaticon.com/512/4712/4712139.png";
        
        let avatar = sender === 'ai' ?
            `<div class="avatar flex-shrink-0"><img src="${aiAvatarUrl}" width="30" height="30" class="rounded-circle bg-light p-1"></div>` :
            '';

        let formattedText = text.replace(
            /(https?:\/\/[^\s]+)/g, 
            '<a href="$1" target="_blank" style="color: var(--color-accent); text-decoration: underline; word-break: break-all;">$1</a>'
        );

        formattedText = formattedText.replace(/\n/g, '<br>');
        
        let bgClass = sender === 'ai' ? 'bg-light' : 'bg-accent text-white';

        if (sender === 'user') {
            formattedText = formattedText.replace(/style="color: var(--color-accent);/g, 'style="color: white;');
        }

        let html = `
            <div class="message ${sender} d-flex gap-2 mb-3 ${sender === 'user' ? 'justify-content-end' : ''}">
                ${avatar}
                <div class="content p-2 px-3 rounded-3 shadow-sm ${bgClass}" style="max-width: 80%;">
                    ${formattedText}
                </div>
            </div>
        `;
        messagesContainer.append(html);
        scrollToBottom();
    }

    function scrollToBottom() {
        if(messagesContainer.length) {
            messagesContainer.animate({ scrollTop: messagesContainer[0].scrollHeight }, 300);
        }
    }
});