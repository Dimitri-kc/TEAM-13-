document.addEventListener("DOMContentLoaded", function() {
    console.log("Filters.js loaded!");
    
    function getProductsArray() {
        return document.querySelectorAll(".item");
    }

    const keywordCheckboxes = document.querySelectorAll(".tags input[type='checkbox']");
    const categoryCheckboxes = document.querySelectorAll(".category-filter");
    const colourCheckboxes = document.querySelectorAll(".colour-filter");
    const productGrid = document.querySelector(".product-grid");
    
    console.log("Found elements:", {
        keywords: keywordCheckboxes.length,
        categories: categoryCheckboxes.length,
        colours: colourCheckboxes.length,
        products: getProductsArray().length
    });

    function applyFilters() {
        console.log("applyFilters called!");
        const products = document.querySelectorAll(".item");
        
        const activeKeywords = [...keywordCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
        const activeCategories = [...categoryCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
        const activeColours = [...colourCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
        
        console.log("Active filters:", { activeKeywords, activeCategories, activeColours });

        products.forEach(product => {
            const productKeywords = (product.dataset.keywords || "").split(" ");
            const productCategory = product.dataset.category || "";
            const productColours = (product.dataset.colour || "").split(" ");
            const categoryMatch = activeCategories.length === 0 || activeCategories.includes(productCategory);
            const keywordMatch = activeKeywords.length === 0 || activeKeywords.some(k => productKeywords.includes(k));
            const colourMatch = activeColours.length === 0 || activeColours.some(c => productColours.includes(c));
            product.style.display = categoryMatch && keywordMatch && colourMatch ? "" : "none";
        });
    }

    keywordCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
    categoryCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
    colourCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
    
    console.log("Event listeners attached!");
});
