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

    if (!products.length) {
        productGrid.innerHTML = '<p id="no-results">Uh oh! No products available.</p>';
        return;
    }
 
    products.forEach(product => {
    const div = document.createElement('div');
    div.classList.add('item');

   div.dataset.category = product.category_id;
   div.dataset.id = product.product_ID;
    div.dataset.price    = product.price;
    div.dataset.new      = product.is_new;
    div.dataset.rating   = product.rating;
    div.dataset.keywords = product.keywords;
    div.dataset.colour   = product.colour;

    div.innerHTML = `
    <img class="product-img" src="${product.image}" alt="${product.name}">
      <div class="product-text">
        <h2>${product.name}</h2>
        <p>£${product.price}</p>
      </div>
      <button class="add-to-basket">
        <img src="../images/add-button-icon.png">
      </button>
    `;

    productGrid.appendChild(div);

      // Added basket event listener after button is created
        const button = div.querySelector('.add-to-basket');
        button.addEventListener('click', (event) => {
            event.stopPropagation();
            addToBasket(product.product_ID, 1, button);
        });
      });
      }
// Initial fetch
// fetchProducts();

// fetch(`https://cs2team13.cs2410-web01pvm.aston.ac.uk/TEAM-13-/Draft/backend/routes/productRoute.php?action=byCategory&category=${categoryName}`)
// fetch(`../php/productRoute.php?action=byCategory&category=${filters.category}`)

/* const basketCountEl = document.getElementById('basket-count');

let basketCount = parseInt(localStorage.getItem('basketCount')) || 0;
if (basketCountEl) basketCountEl.textContent = basketCount;

// Basket modals
const basketModal = document.getElementById('basket-modal');
const continueBtn = document.getElementById('continue-shopping');
const goToBasketBtn = document.getElementById('go-to-basket');


function addToBasket(button) {
    basketCount++;
    basketCountEl.textContent = basketCount;
    button.classList.add('added'); 

    basketModal.style.display ='flex';
    localStorage.setItem('basketCount', basketCount);

}

if (continueBtn) {
    continueBtn.addEventListener('click', () => {
        basketModal.style.display = 'none';
    });
}

if (goToBasketBtn) {
    goToBasketBtn.addEventListener('click', () => {
        window.location.href = 'basket.php';
    });
} */

document.addEventListener("click", function (e) {
  const favBtn = e.target.closest(".fav-icon-btn");
  if (!favBtn) return;

  e.preventDefault();
  e.stopPropagation();
});



