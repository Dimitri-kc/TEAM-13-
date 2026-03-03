document.addEventListener("DOMContentLoaded", function () {

    const zoomIcon = document.getElementById("zoomIcon");
    const productImage = document.getElementById("zoom-image");
    const modal = document.getElementById("imageModal");
    const modalImage = document.getElementById("modalZoomImage");
    const imageWrapper = document.querySelector(".modal-image-wrapper");
    const closeBtn = document.querySelector(".close-modal-image");

    if (!productImage) return;

    let scale = 1;
    let isDragging = false;
    let startX = 0;
    let startY = 0;
    let translateX = 0;
    let translateY = 0;

    const MAX_SCALE = 2.5;

    function updateTransform() {
        modalImage.style.transform =
            `translate(${translateX}px, ${translateY}px) scale(${scale})`;
    }

    function resetZoom() {
        scale = 1;
        translateX = 0;
        translateY = 0;
        updateTransform();

        zoomIcon.src = "../images/zoom_in.png";
        imageWrapper.style.cursor = "url('../images/zoom_in.png') 16 16, zoom-in";
    }

    // OPEN MODAL
    productImage.addEventListener("click", function () {
        modal.classList.add("active");
        modalImage.src = this.src;
        resetZoom();
    });

    // CLOSE MODAL (close button)
    closeBtn.addEventListener("click", () => modal.classList.remove("active"));

    // CLOSE MODAL (click outside)
    modal.addEventListener("click", (e) => {
        if (e.target === modal) modal.classList.remove("active");
    });

    // CLOSE MODAL (ESC key)
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") modal.classList.remove("active");
    });

    // DOUBLE CLICK ZOOM
    imageWrapper.addEventListener("dblclick", function () {
        if (scale === 1) {
            scale = MAX_SCALE;
            zoomIcon.src = "../images/zoom_out.png";
            imageWrapper.style.cursor = "url('../images/zoom_out.png') 16 16, zoom-out";
        } else {
            resetZoom();
        }
        updateTransform();
    });

    // DRAG START
    imageWrapper.addEventListener("mousedown", function (e) {
        if (scale === 1) return;

        isDragging = true;
        imageWrapper.style.cursor = "grabbing";

        startX = e.clientX - translateX;
        startY = e.clientY - translateY;
    });

    // DRAG END
    imageWrapper.addEventListener("mouseup", () => {
        isDragging = false;
        if (scale > 1)
            imageWrapper.style.cursor = "url('../images/zoom_out.png') 16 16, zoom-out";
    });

    imageWrapper.addEventListener("mouseleave", () => {
        isDragging = false;
    });

    // DRAG MOVE
    imageWrapper.addEventListener("mousemove", function (e) {
        if (!isDragging || scale === 1) return;

        translateX = e.clientX - startX;
        translateY = e.clientY - startY;

        limitBoundaries();
        updateTransform();
    });

    function limitBoundaries() {
        const rect = imageWrapper.getBoundingClientRect();

        const maxX = (rect.width * (scale - 1)) / 2;
        const maxY = (rect.height * (scale - 1)) / 2;

        translateX = Math.min(maxX, Math.max(-maxX, translateX));
        translateY = Math.min(maxY, Math.max(-maxY, translateY));
    }

    // SAFETY CLOSE HANDLER (fallback)
    document.addEventListener("click", (e) => {
        if (modal.classList.contains("active") && e.target.id === "imageModal") {
            modal.classList.remove("active");
        }
    });

    if (!closeBtn) console.warn("Close button not found!");

    const wishlistIcon = document.querySelector(".wishlist-icon");

if (wishlistIcon) {
    wishlistIcon.addEventListener("click", function (e) {
        e.stopPropagation(); // 🔥 THIS FIXES IT
        console.log("Heart clicked");
    });
}
});
