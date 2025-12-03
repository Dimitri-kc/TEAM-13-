import { getProductsArray, render, setActiveButton } from "./utils.js";

const productGrid = document.querySelector(".product-grid");
const btnNew       = document.querySelector(".btn-New");
const btnPriceAsc  = document.querySelector(".btn-PriceAsc");
const btnPriceDesc = document.querySelector(".btn-PriceDesc");
const btnRating    = document.querySelector(".btn-Rating");
const originalProducts    = [...productGrid.querySelectorAll(".item")];

function resetGrid() {
  productGrid.innerHTML ="";
  originalProducts.forEach(p => {

    p.style.display = "none";

    p.style.display ="block";

    productGrid.appendChild(p);
  }) 
  
  document.querySelectorAll(".button-sort").forEach(b => b.classList.remove("active"));
}

function showAllProducts() {
  getProductsArray().forEach(p => p.style.display = "block");
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
  showAllProducts();
  const sorted = getProductsArray().sort(
    (a, b) => Number(a.dataset.price) - Number(b.dataset.price));
  render(sorted, productGrid);
}
});


btnPriceDesc.addEventListener("click", () => {
      if(btnPriceDesc.classList.contains("active")){
    resetGrid();
  } else {
  setActiveButton(btnPriceDesc);
  showAllProducts();
  const sorted = getProductsArray().sort(
    (a, b) => Number(b.dataset.price) - Number(a.dataset.price));

  render(sorted, productGrid);

}
});

btnRating.addEventListener("click", () => {
      if(btnRating.classList.contains("active")){
    resetGrid();
  } else {
  setActiveButton(btnRating);
  showAllProducts();
    const sorted = getProductsArray().sort(
    (a, b) => Number(a.dataset.rating) - Number(b.dataset.rating));

  render(sorted, productGrid);

  }
});