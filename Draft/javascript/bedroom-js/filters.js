function getProductsArray() {
    return document.querySelectorAll(".item");
}

function applyFilters() {
  const products = document.querySelectorAll(".item");
  
  // Re-query checkboxes each time to ensure we get any dynamically added ones
  const keywordCheckboxes = document.querySelectorAll(".tags input[type='checkbox']");
  const categoryCheckboxes = document.querySelectorAll(".category-filter");
  const colourCheckboxes = document.querySelectorAll(".colour-filter");
  const sizeCheckboxes = document.querySelectorAll(".size-filter");
  
  const activeKeywords = [...keywordCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
  const activeCategories = [...categoryCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
  const activeColours = [...colourCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
  const activeSizes = [...sizeCheckboxes].filter(cb => cb.checked).map(cb => cb.value);

  products.forEach(product => {
    const productKeywords = (product.dataset.keywords || "").split(" ").filter(k => k.length > 0);
    const productCategory = product.dataset.category || "";
    const productColours = (product.dataset.colour || "").split(" ").filter(c => c.length > 0);
    const productSize = product.dataset.size || "";
    
    const categoryMatch = activeCategories.length === 0 || activeCategories.includes(productCategory);
    const keywordMatch = activeKeywords.length === 0 || activeKeywords.some(k => productKeywords.includes(k));
    const colourMatch = activeColours.length === 0 || activeColours.some(c => productColours.includes(c));
    const sizeMatch = activeSizes.length === 0 || activeSizes.includes(productSize);
    
    product.style.display = categoryMatch && keywordMatch && colourMatch && sizeMatch ? "" : "none";
  });
}

// Attach event listeners - using event delegation to handle dynamic elements
document.addEventListener("change", (e) => {
  if (e.target.classList.contains("tags") || 
      e.target.classList.contains("category-filter") ||
      e.target.classList.contains("colour-filter") ||
      e.target.classList.contains("size-filter")) {
    applyFilters();
  }
});
