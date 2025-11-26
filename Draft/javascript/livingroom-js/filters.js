const keywordCheckboxes = document.querySelectorAll(".tags input[type='checkbox']");
const categoryCheckboxes = document.querySelectorAll(".category-filter");
const colourCheckboxes = document.querySelectorAll(".colour-filter");
const products = document.querySelectorAll(".item");
const productGrid = document.querySelector(".product-grid");

function applyFilters() {
  const activeKeywords = [...keywordCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
  const activeCategories = [...categoryCheckboxes].filter(cb => cb.checked).map(cb => cb.value);
  const activeColours = [...colourCheckboxes].filter(cb => cb.checked).map(cb => cb.value);

  products.forEach(product => {
    const productKeywords = (product.dataset.keywords || "").split(" ");
    const productCategory = product.dataset.category || "";
    const productColours = (product.dataset.colour || "").split(" ");
    const categoryMatch = activeCategories.length === 0 || activeCategories.includes(productCategory);
    const keywordMatch = activeKeywords.length === 0 || activeKeywords.some(k => productKeywords.includes(k));
    const colourMatch = activeColours.length === 0 || activeColours.some(c => productColours.includes(c));
    product.style.display = categoryMatch && keywordMatch && colourMatch ? "block" : "none";
  });
}

keywordCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
categoryCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
colourCheckboxes.forEach(cb => cb.addEventListener("change", applyFilters));
