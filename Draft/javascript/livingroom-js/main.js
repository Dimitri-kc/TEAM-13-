import "./filters.js";
import "./sorting.js";
import "./price-range.js";
import "./search.js";

//added for fetch API
import { loadProducts } from "./products.js";

document.addEventListener("DOMContentLoaded", () => {
    const category = document.body.dataset.category;
    loadProducts(category);
});