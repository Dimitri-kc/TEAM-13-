import "./filters.js";
import "./sorting.js";
import "./price-range.js";
import "./search.js";

// Get the product grid container
const productGrid = document.getElementById('product-grid');

const categoryMap = {
  livingroom: 1,
  kitchen: 2,
  office: 3,
  bathroom: 4,
  bedroom: 5
};

const categoryName = document.body.dataset.category; // "livingroom"
const categoryId = categoryMap[categoryName?.toLowerCase()] || 1;
// const productGrid = document.querySelector(".product-grid");

// Fetch products from PHP API
async function fetchProducts() {
    try {
        const response = await fetch(`../backend/routes/productRoute.php?action=byCategory&category=${categoryId}`);
        const products = await response.json();

        renderProducts(products);
        //added to expose hook
        document.dispatchEvent(new Event("productsRendered"));
    } catch (error) {
        console.error("Error fetching products:", error);
        productGrid.innerHTML = '<p>Failed to load products.</p>';
    }
}


// fetch(`../php/productRoute.php?action=byCategory&category=${categoryId}`)

  // Render products into the grid
function renderProducts(products) {
  productGrid.innerHTML = '';
  productGrid.style.display = 'grid';
  productGrid.style.gridTemplateColumns = 'repeat(auto-fill, minmax(260px, 1fr))';
  productGrid.style.gap = '24px';
  productGrid.style.alignItems = 'start';

    if (!products.length) {
        productGrid.innerHTML = '<p id="no-results">Uh oh! No products available.</p>';
        return;
    }
 
    const fragment = document.createDocumentFragment();
    
    products.forEach(product => {
    const div = document.createElement('div');
    div.classList.add('item');
    div.style.display = 'grid';

    const imgSrc = product.image && !product.image.startsWith('http') && !product.image.startsWith('../')
      ? `../images/${product.image}`
      : (product.image || '../images/placeholder.png');

    div.dataset.category = product.category_id;
    div.dataset.id = product.product_ID;
    div.dataset.price    = product.price;
    div.dataset.new      = product.is_new;
    div.dataset.rating   = product.rating;
    div.dataset.keywords = product.keywords;
    div.dataset.colour   = product.colour;

    div.innerHTML = `
      <img class="product-img" src="${imgSrc}" alt="${product.name}">
      <h2>${product.name}</h2>
      <p>£${product.price}</p>
      <button class="add-to-basket">
        <img src="../images/add-button-icon.png">
      </button>
    `;

    fragment.appendChild(div);

      // Added basket event listener after button is created
        const button = div.querySelector('.add-to-basket');
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            addToBasket(product.product_ID, 1, button);
        });
      });
      
      productGrid.appendChild(fragment);
      }
      
// Ensure products load on page ready using batched DOM insertion
document.addEventListener("DOMContentLoaded", () => {
  if (productGrid) {
    productGrid.style.display = "grid";
  }
  fetchProducts();
});
// Initial fetch
document.addEventListener("click", function (e) {
  const favBtn = e.target.closest(".fav-icon-btn");
  if (!favBtn) return;

  e.preventDefault();
  e.stopPropagation();
});



