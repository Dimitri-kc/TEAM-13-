import "./filters.js";
import "./sorting.js";
import "./price-range.js";
import "./search.js";

const categoryName = document.body.dataset.category; // "livingroom"
const productGrid = document.querySelector(".product-grid");

// fetch(`https://cs2team13.cs2410-web01pvm.aston.ac.uk/TEAM-13-/Draft/backend/routes/productRoute.php?action=byCategory&category=${categoryName}`)
fetch(`https://cs2team13.cs2410-web01pvm.aston.ac.uk/TEAM-13-/Draft/backend/routes/productRoute.php?action=byCategory&category=${categoryName}`)
  .then(res => res.json())
  .then(products => {
    console.log(products); // check if data is coming through
    if (products.length === 0) {
      document.getElementById("no-results").style.display = "block";
    } else {
      products.forEach(product => {
        const div = document.createElement("div");
        div.classList.add("item");
        div.setAttribute("data-category", product.category_id);
        div.innerHTML = `
          <img src="../images/livingroom-images/${product.image}" alt="${product.name}">
          <div class="product-text">
            <h2>${product.name}</h2>
            <p>£${product.price}</p>
          </div>
        `;
        productGrid.appendChild(div);
      });
    }
  })
  .catch(err => {
    console.error("Error fetching products:", err);
  });

//   const categoryId = 1; // Living Room

// fetch(`https://cs2team13.cs2410-web01pvm.aston.ac.uk/TEAM-13-/Draft/backend/routes/productRoute.php?action=byCategory&category=${categoryId}`)
//   .then(res => res.json())
//   .then(products => console.log(products))
//   .catch(err => console.error(err));


  //added for fetch API
// import { loadProducts } from "./products.js";

// document.addEventListener("DOMContentLoaded", () => {
//     const category = document.body.dataset.category;
//     loadProducts(category);
// });

// const category = document.body.dataset.category;

// fetchfetch(`../backend/routes/productRoute.php?action=byCategory&category=${category}`)
//   .then(res => res.json())
//   .then(products => {
//     console.log(products);
//   });

//Basket icon section
// const basketButtons = document.querySelectorAll('.add-to-basket');
// const basketCountEl = document.getElementById('basket-count');
// let basketCount = 0;

// // Add click to each button
// basketButtons.forEach(button => {
//     button.addEventListener('click', (event) => {
//         event.stopPropagation(); 
//         basketCount++;
//         basketCountEl.textContent = basketCount;
//         //changes button style to show it was added
//         button.classList.add('added');
//     });
// });
// // ---------- BASKET PERSISTENCE ----------
// const basketCountEl = document.getElementById('basket-count');

// // Load basket count from localStorage, default to 0
// let basketCount = parseInt(localStorage.getItem('basketCount')) || 0;
// if (basketCountEl) basketCountEl.textContent = basketCount;

// // Function to add a product to the basket
// function addToBasket(button) {
//     basketCount++;
//     basketCountEl.textContent = basketCount;

//     // Save count to localStorage
//     localStorage.setItem('basketCount', basketCount);

//     // optional: add visual feedback
//     button.classList.add('added');
// }

// // ---------- ADD EVENT LISTENERS ----------
// const basketButtons = document.querySelectorAll('.add-to-basket');

// basketButtons.forEach(button => {
//     button.addEventListener('click', (event) => {
//         event.stopPropagation(); // prevent going to product page
//         addToBasket(button);
//     });
// });

const basketCountEl = document.getElementById('basket-count');
//page loads at 0
let basketCount = 0;
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
}
continueBtn.addEventListener('click', () => {
    basketModal.style.display = 'none'; // closes modal
});

goToBasketBtn.addEventListener('click', () => {
    window.location.href = 'basket.html'; // redirects to the basket
});

const basketButtons = document.querySelectorAll('.add-to-basket');
basketButtons.forEach(button => {
    button.addEventListener('click', (event) => {
        event.stopPropagation(); // prevent navigating to product page
        addToBasket(button);
    });
});