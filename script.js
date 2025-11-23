
const keywordCheckboxes = document.querySelectorAll(".tags input[type='checkbox']");
const categoryCheckboxes = document.querySelectorAll(".category-filter");
const colourCheckboxes = document.querySelectorAll(".colour-filter");
const products = document.querySelectorAll(".item");
const productGrid = document.querySelector(".product-grid");

const btnNew       = document.querySelector(".btn-New");
const btnPriceAsc  = document.querySelector(".btn-PriceAsc");
const btnPriceDesc = document.querySelector(".btn-PriceDesc");
const btnRating    = document.querySelector(".btn-Rating");


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

function getProductsArray() {
  return [...document.querySelectorAll(".item")];
}
function render(sortedProducts) {
  sortedProducts.forEach(p => productGrid.appendChild(p));
}

function setActiveButton(btn) {
  document.querySelectorAll(".button-sort").forEach(b => b.classList.remove("active"));
  btn.classList.add("active");
}

btnNew.addEventListener("click", () => {
  setActiveButton(btnNew);
  getProductsArray().forEach(p => {
    p.style.display = p.dataset.new === "true" ? "block" : "none";
  });
});

btnPriceAsc.addEventListener("click", () => {
  setActiveButton(btnPriceAsc);
  render(getProductsArray().sort((a, b) => Number(a.dataset.price) - Number(b.dataset.price)));
});

btnPriceDesc.addEventListener("click", () => {
  setActiveButton(btnPriceDesc);
  render(getProductsArray().sort((a, b) => Number(b.dataset.price) - Number(a.dataset.price)));
});

btnRating.addEventListener("click", () => {
  setActiveButton(btnRating);
  render(getProductsArray().sort((a, b) => Number(b.dataset.rating) - Number(a.dataset.rating)));
});


const slider = document.getElementById("price");
const label = document.getElementById("price-num");
slider.addEventListener("input", () => label.textContent = `£0-${slider.value}`);
