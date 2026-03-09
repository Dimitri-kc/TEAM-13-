document.addEventListener("DOMContentLoaded", function() {

    const minSlider = document.getElementById("price-min");
    const maxSlider = document.getElementById("price-max");
    const label  = document.getElementById("price-num");
    const rangeDisplay = document.getElementById("range-display");

    // Grab all products
    function getProductsArray() {
        return document.querySelectorAll(".item");
    }

    // Store original display type for each product
    const products = getProductsArray();
    products.forEach(product => {
        product.dataset.originalDisplay = window.getComputedStyle(product).display;
    });

    // Set slider max to highest product price
    function setMaxPriceFromProducts() {
        let highestPrice = 0;
        products.forEach(product => {
            const price = Number(product.dataset.price);
            if (price > highestPrice) highestPrice = price;
        });

        // Round max price nicely
        highestPrice = Math.ceil(highestPrice / 10) * 10;

        minSlider.min = 0;
        maxSlider.min = 0;
        minSlider.max = highestPrice;
        maxSlider.max = highestPrice;
        maxSlider.value = highestPrice;

        label.textContent = `£0 - £${highestPrice}`;
    }

    // Prevent slider thumbs from overlapping
    function fixThumbOverlap() {
        if (parseInt(minSlider.value) >= parseInt(maxSlider.value) - 5) {
            minSlider.style.zIndex = 4;
            maxSlider.style.zIndex = 3;
        } else {
            minSlider.style.zIndex = 2;
            maxSlider.style.zIndex = 3;
        }
    }

    // Filter products based on slider
    function updatePrice() {
        let min = Number(minSlider.value);
        let max = Number(maxSlider.value);

        if (min > max) [min, max] = [max, min];

        label.textContent = `£${min} - £${max}`;

        const percentMin = (min / minSlider.max) * 100;
        const percentMax = (max / maxSlider.max) * 100;
        rangeDisplay.style.left = percentMin + "%";
        rangeDisplay.style.width = (percentMax - percentMin) + "%";

products.forEach(product => {
    const price = Number(product.dataset.price);
    if (price >= min && price <= max) {
product.style.display = product.dataset.originalDisplay;
    } else {
        product.style.display = "none"; // hide
    }
});
        fixThumbOverlap();
    }

    // Initialize
    setMaxPriceFromProducts();
    minSlider.addEventListener("input", updatePrice);
    maxSlider.addEventListener("input", updatePrice);
    updatePrice();

});