// Combined filtering, search, sorting, and price range functionality
(function() {
    'use strict';
    
    console.log("Combined filters script loading...");
    
    // Wait for DOM
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
    
    function init() {
        console.log("Initializing filters...");
        
        // Get all elements
        const keywordCheckboxes = document.querySelectorAll(".tags input[type='checkbox']");
        const categoryCheckboxes = document.querySelectorAll(".category-filter");
        const colourCheckboxes = document.querySelectorAll(".colour-filter");
        const searchInput = document.querySelector(".search");
        const noResults = document.getElementById("no-results");
        const minSlider = document.getElementById("price-min");
        const maxSlider = document.getElementById("price-max");
        const priceLabel = document.getElementById("price-num");
        
        console.log("Found elements:", {
            keywords: keywordCheckboxes.length,
            categories: categoryCheckboxes.length,
            colours: colourCheckboxes.length,
            search: !!searchInput,
            priceSliders: !!(minSlider && maxSlider)
        });
        
        // Main filter function
        function applyAllFilters() {
            console.log("Applying filters...");
            const products = document.querySelectorAll(".item");
            
            const activeKeywords = Array.from(keywordCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const activeCategories = Array.from(categoryCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const activeColours = Array.from(colourCheckboxes).filter(cb => cb.checked).map(cb => cb.value);
            const searchQuery = searchInput ? searchInput.value.toLowerCase().trim() : '';
            
            const minPrice = minSlider ? Number(minSlider.value) : 0;
            const maxPrice = maxSlider ? Number(maxSlider.value) : 999999;
            
            console.log("Active filters:", { activeKeywords, activeCategories, activeColours, searchQuery, minPrice, maxPrice });
            
            let visibleCount = 0;
            
            products.forEach(product => {
                const productKeywords = (product.dataset.keywords || "").split(" ");
                const productCategory = product.dataset.category || "";
                const productColours = (product.dataset.colour || "").split(" ");
                const productPrice = Number(product.dataset.price || 0);
                const productName = product.querySelector("h2") ? product.querySelector("h2").textContent.toLowerCase() : "";
                
                // Check all filters
                const categoryMatch = activeCategories.length === 0 || activeCategories.includes(productCategory);
                const keywordMatch = activeKeywords.length === 0 || activeKeywords.some(k => productKeywords.includes(k));
                const colourMatch = activeColours.length === 0 || activeColours.some(c => productColours.includes(c));
                const priceMatch = productPrice >= minPrice && productPrice <= maxPrice;
                const searchMatch = !searchQuery || productName.includes(searchQuery) || productKeywords.join(' ').toLowerCase().includes(searchQuery);
                
                const shouldShow = categoryMatch && keywordMatch && colourMatch && priceMatch && searchMatch;
                
                product.style.display = shouldShow ? "" : "none";
                if (shouldShow) visibleCount++;
            });
            
            if (noResults) {
                noResults.style.display = visibleCount === 0 ? "block" : "none";
            }
            
            console.log(`Filtered: ${visibleCount} products visible`);
        }
        
        // Attach event listeners
        keywordCheckboxes.forEach(cb => {
            cb.addEventListener("change", function() {
                console.log("Keyword clicked:", cb.value, cb.checked);
                applyAllFilters();
            });
        });
        
        categoryCheckboxes.forEach(cb => {
            cb.addEventListener("change", function() {
                console.log("Category clicked:", cb.value, cb.checked);
                applyAllFilters();
            });
        });
        
        colourCheckboxes.forEach(cb => {
            cb.addEventListener("change", function() {
                console.log("Colour clicked:", cb.value, cb.checked);
                applyAllFilters();
            });
        });
        
        if (searchInput) {
            searchInput.addEventListener("input", function() {
                console.log("Search input:", searchInput.value);
                applyAllFilters();
            });
        }
        
        if (minSlider && maxSlider) {
            minSlider.addEventListener("input", applyAllFilters);
            maxSlider.addEventListener("input", applyAllFilters);
        }
        
        console.log("All event listeners attached!");
        
        // Test with a manual click after a delay
        setTimeout(() => {
            console.log("Testing automatic click on first category filter...");
            if (categoryCheckboxes[0]) {
                categoryCheckboxes[0].checked = true;
                applyAllFilters();
                setTimeout(() => {
                    categoryCheckboxes[0].checked = false;
                    applyAllFilters();
                }, 1000);
            }
        }, 2000);
    }
})();
