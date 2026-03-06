// Standalone test - add this temporarily to verify JavaScript execution
console.log("TEST SCRIPT LOADED - JavaScript is working!");

setTimeout(() => {
    console.log("=== FILTER DEBUG TEST ===");
    console.log("Search input:", document.querySelector(".search"));
    console.log("Category filters:", document.querySelectorAll(".category-filter").length);
    console.log("Keyword checkboxes:", document.querySelectorAll(".tags input[type='checkbox']").length);
    console.log("Products:", document.querySelectorAll(".item").length);
    
    // Test if we can actually click a filter
    const firstFilter = document.querySelector(".category-filter");
    if (firstFilter) {
        console.log("First filter found:", firstFilter);
        
        // Check CSS that might block interaction
        const computedStyle = window.getComputedStyle(firstFilter);
        console.log("Filter CSS:", {
            pointerEvents: computedStyle.pointerEvents,
            display: computedStyle.display,
            visibility: computedStyle.visibility,
            zIndex: computedStyle.zIndex
        });
        
        firstFilter.addEventListener("change", () => {
            console.log("✓ FILTER CLICKED! Checkbox is now:", firstFilter.checked);
        });
        
        // Try programmatically clicking it
        console.log("Attempting programmatic click...");
        firstFilter.click();
    }
}, 1000);
