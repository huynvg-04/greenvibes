function closeToast(button) {
    const toast = button.closest(".notification-toast");
    hideAndRemove(toast);
}

function hideAndRemove(toast) {
    toast.classList.add("hide");
    toast.addEventListener("animationend", (e) => {
        if (e.animationName === "slideOut") {
            toast.remove();
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const toasts = document.querySelectorAll(".notification-toast");

    toasts.forEach((toast) => {
        const progressBar = toast.querySelector(".progress-bar");

        progressBar.addEventListener("animationend", (e) => {
            if (e.animationName === "countdown") {
                hideAndRemove(toast);
            }
        });

        toast.addEventListener("mouseenter", () => {
            progressBar.style.animationPlayState = "paused";
        });

        toast.addEventListener("mouseleave", () => {
            progressBar.style.animationPlayState = "running";
        });
    });
});
