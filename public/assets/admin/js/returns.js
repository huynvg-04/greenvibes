document.addEventListener("DOMContentLoaded", function () {
    var tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]'),
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    var viewImageModal = document.getElementById("viewImageModal");

    if (viewImageModal) {
        viewImageModal.addEventListener("show.bs.modal", function (event) {
            var button = event.relatedTarget;
            var imgSrc = button.getAttribute("data-src");
            var modalImg = viewImageModal.querySelector("#modal_image_target");
            modalImg.src = imgSrc;
        });

        viewImageModal.addEventListener("hidden.bs.modal", function () {
            var modalImg = viewImageModal.querySelector("#modal_image_target");
            modalImg.src = "";
        });
    }
});
