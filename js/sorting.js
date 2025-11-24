import { getProductsArray, render, setActiveButton } from "./utils.js";

const btnNew       = document.querySelector(".btn-New");
const btnPriceAsc  = document.querySelector(".btn-PriceAsc");
const btnPriceDesc = document.querySelector(".btn-PriceDesc");
const btnRating    = document.querySelector(".btn-Rating");

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