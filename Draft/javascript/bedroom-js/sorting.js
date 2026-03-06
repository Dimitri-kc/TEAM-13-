document.addEventListener("DOMContentLoaded", function() {
    function getProductsArray() {
        return [...document.querySelectorAll(".item")];
    }
    
    function render(sortedProducts, productGrid) {
        sortedProducts.forEach(p => productGrid.appendChild(p));
    }
    
    function setActiveButton(btn) {
        document.querySelectorAll(".button-sort").forEach(b => b.classList.remove("active"));
        btn.classList.add("active");
    }

    const productGrid   = document.querySelector(".product-grid");
    const btnNew        = document.querySelector(".btn-New");
    const btnPriceAsc   = document.querySelector(".btn-PriceAsc");
    const btnPriceDesc  = document.querySelector(".btn-PriceDesc");
    const btnRating     = document.querySelector(".btn-Rating");

    let originalProducts = [...document.querySelectorAll(".item")];

    // Reset grid to original order
    function resetGrid() {
        productGrid.innerHTML = "";
        originalProducts.forEach(p => {
            p.classList.remove("filter-hidden");
            productGrid.appendChild(p);
        });
        document.querySelectorAll(".button-sort").forEach(b => b.classList.remove("active"));
    }

    // NEW filter
    btnNew.addEventListener("click", () => {
        if (btnNew.classList.contains("active")) {
            resetGrid();
        } else {
            setActiveButton(btnNew);
            getProductsArray().forEach(p => {
                if (p.dataset.new === "true") {
                    p.classList.remove("filter-hidden");
                } else {
                    p.classList.add("filter-hidden");
                }
            });
        }
    });

    // PRICE ASC
    btnPriceAsc.addEventListener("click", () => {
        if (btnPriceAsc.classList.contains("active")) {
            resetGrid();
        } else {
            setActiveButton(btnPriceAsc);
            const visibleProducts = getProductsArray().filter(p => !p.classList.contains("filter-hidden"));
            const sorted = visibleProducts.sort((a, b) => Number(a.dataset.price) - Number(b.dataset.price));
            render(sorted, productGrid);
        }
    });

    // PRICE DESC
    btnPriceDesc.addEventListener("click", () => {
        if (btnPriceDesc.classList.contains("active")) {
            resetGrid();
        } else {
            setActiveButton(btnPriceDesc);
            const visibleProducts = getProductsArray().filter(p => !p.classList.contains("filter-hidden"));
            const sorted = visibleProducts.sort((a, b) => Number(b.dataset.price) - Number(a.dataset.price));
            render(sorted, productGrid);
        }
    });

    // RATING
    btnRating.addEventListener("click", () => {
        if (btnRating.classList.contains("active")) {
            resetGrid();
        } else {
            setActiveButton(btnRating);
            const sorted = getProductsArray().sort((a, b) => Number(a.dataset.rating) - Number(b.dataset.rating));
            render(sorted, productGrid);
        }
    });
});
