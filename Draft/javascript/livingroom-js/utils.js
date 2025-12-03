export function getProductsArray() {
  return [...document.querySelectorAll(".item")];
}
export function render(sortedProducts, productGrid) {
  sortedProducts.forEach(p => productGrid.appendChild(p));
}
export function setActiveButton(btn) {
  document.querySelectorAll(".button-sort").forEach(b => b.classList.remove("active"));
  btn.classList.add("active");
}
