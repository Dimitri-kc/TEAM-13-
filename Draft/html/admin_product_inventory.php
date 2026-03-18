<?php include '../backend/config/db_connect.php';

require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Product Inventory</title>
    <link rel="stylesheet" href="../css/admin_product_inventory.css">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=14">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
    
</head>
<body>
    <?php $headerPartialOnly = true; include "header.php"; ?>

    <div class="admin-wrapper"style="margin-top: 125px;">
    <h1 class="title">Product Inventory </h1>
    <p class="subtitle">View current product inventory, edit product information and add more products </p>

<div class="filter-wrapper" >
        <input 
        type="text" 
        id="search-bar" 
        placeholder="Search products…" 
        autocomplete="off">
        
 <label>Category:</label>

    <div class="select-wrapper">
        <select id="category-filter" name="category_id">
            <option value="">All Items</option>
            <option value="1">Living Room</option>
            <option value="2">Kitchen</option>
            <option value="3">Office</option>
            <option value="4">Bathroom</option>
            <option value="5">Bedroom</option>
        </select>
    </div>

      <!-- <button id="add-product-btn">Add New Product</button> -->
    <button id="add-product-btn" onclick="window.location.href='admin_add_product.php'">
        + Add New Product
    </button>
</div>

    <div id="product-container" class="product-grid">
        <!-- Products will load here automatically -->
    </div>
</div>

<script>
fetch('admin_get_products.php')
.then(response => response.json())
.then(data => {
    const container = document.getElementById('product-container');
    const categoryFilter = document.getElementById('category-filter');

    // Function to render products
    function renderProducts(filteredData) {
        container.innerHTML = ''; // clear previous cards
        // Add product cards
        filteredData.forEach(product => {

        // Determine stock level
    let stockClass = "";
    let stockText = "";

    if (product.stock <= 4) {
        stockClass = "stock-critical";
        stockText = "Critical";
    } 
    else if (product.stock <= 9) {
        stockClass = "stock-low";
        stockText = "Low Stock";
    } 
    else {
        stockClass = "stock-good";
        stockText = "In Stock";
    }
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');
        
productCard.innerHTML = `
    <img src="../images/${product.image}" alt="${product.name}">
    <div class="product-info">
        <p class="product-name">${product.name}</p>
        <p>Product #: ${product.product_ID}</p>
        <p>Price: £${product.price}</p>

        <p class="stock ${stockClass}">
            Stock Available: ${product.stock} (${stockText})
        </p>

        <div class="admin-buttons">
            <button class="view-btn" onclick="viewEditProduct(${product.product_ID})">
                View & Edit
            </button>
            <button class="remove-btn" onclick="removeProduct(${product.product_ID})">
                Remove
            </button>
        </div>
    </div>
`;
            container.appendChild(productCard);
        });
    }

    // Initial render with all products
    renderProducts(data);

    // Filter on dropdown change
categoryFilter.addEventListener('change', () => {
    const selected = categoryFilter.value;

    if (selected === '') {
        renderProducts(data); // Show all products
    } else {
        const filteredData = data.filter(product => Number(product.category_id) === Number(selected));
        renderProducts(filteredData); // Show only selected category
    }
});
document.getElementById("search-bar").addEventListener("input", function () {
    const query = this.value.toLowerCase();

    const filtered = data.filter(product =>
        product.name.toLowerCase().includes(query)
    );

    renderProducts(filtered);
});
})
.catch(error => console.error('Error loading products:', error));
// Functions for buttons
function viewEditProduct(productID) {
    window.location.href = `admin_edit_product.php?id=${productID}`;
}

function removeProduct(productID) {
    if (!confirm("Are you sure you want to remove this product?")) return;

    fetch('admin_delete_product.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `product_ID=${productID}`
    })
    .then(res => res.text())
    .then(response => {
        if (response.trim() === "success") {
            location.reload();
        } else {
            alert("Error deleting product");
        }
    });
}
document.getElementById("search-bar").addEventListener("input", function () {
    const query = this.value.toLowerCase(); // Get the search query, converting it to lowercase
    const products = document.querySelectorAll(".product-card"); // Get all product cards

    products.forEach(card => {
        const productName = card.querySelector(".product-info p strong").nextElementSibling.textContent.toLowerCase(); // Get the product name from the product card

        // Check if the product name includes the query text
        if (productName.includes(query)) {
            card.style.display = "flex"; // Show the product card
        } else {
            card.style.display = "none"; // Hide the product card
        }
    });
});
document.getElementById('add-product-btn').addEventListener('click', () => {
    window.location.href = 'admin_add_product.php';
});
</script>

    <?php $footerPartialOnly = true; include 'footer.php'; ?>
</body>
</html>
