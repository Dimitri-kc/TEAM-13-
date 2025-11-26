import { getProductsArray, render, setActiveButton } from "./utils.js";

const productGrid = document.querySelector(".product-grid");
const btnNew       = document.querySelector(".btn-New");
const btnPriceAsc  = document.querySelector(".btn-PriceAsc");
const btnPriceDesc = document.querySelector(".btn-PriceDesc");
const btnRating    = document.querySelector(".btn-Rating");
const originalProducts    = [...productGrid.querySelectorAll(".item")];

function resetGrid() {
  // render(getProductsArray(), productGrid);
  productGrid.innerHTML ="";
  originalProducts.forEach(p => productGrid.appendChild(p));
  document.querySelectorAll(".button-sort").forEach(b => b.classList.remove("active"));
}

btnNew.addEventListener("click", () => {
  if(btnNew.classList.contains("active")){
    resetGrid();
  } else {
  setActiveButton(btnNew);
  getProductsArray().forEach(p => {
    p.style.display = p.dataset.new === "true" ? "block" : "none";
  });
}
});

btnPriceAsc.addEventListener("click", () => {
    if(btnPriceAsc.classList.contains("active")){
    resetGrid();
  } else {
  setActiveButton(btnPriceAsc);
  render(getProductsArray().sort((a, b) => Number(a.dataset.price) - Number(b.dataset.price)), productGrid);
}
});


btnPriceDesc.addEventListener("click", () => {
      if(btnPriceDesc.classList.contains("active")){
    resetGrid();
  } else {
  setActiveButton(btnPriceDesc);
  render(getProductsArray().sort((a, b) => Number(b.dataset.price) - Number(a.dataset.price)), productGrid);
}
});

btnRating.addEventListener("click", () => {
      if(btnRating.classList.contains("active")){
    resetGrid();
  } else {
  setActiveButton(btnRating);
  render(getProductsArray().sort((a, b) => Number(b.dataset.rating) - Number(a.dataset.rating)), productGrid);
  }
});