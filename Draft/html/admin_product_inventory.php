<?php include '../backend/config/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Product Inventory</title>
    <link rel="stylesheet" href="../css/admin_product_inventory.css">
</head>
<body>

    <div class="admin-wrapper">
    <h1 class="title">Product Inventory</h1>
    <p class="subtitle">View current product inventory, edit product information and add more products </p>

    <label>Category:</label>
<select id="category-filter" name="category_id" required>
    <!-- <option value="">-- Select Category --</option> -->
     <option value="">All Items</option>
    <option value="1">Living Room</option>
    <option value="2">Kitchen</option>
    <option value="3">Office</option>
    <option value="4">Bathroom</option>
    <option value="5">Bedroom</option>
</select>
    
<!-- <label for="category-filter">Filter by Category:</label>
<select id="category-filter">
    <option value="all">All Categories</option>
</select> -->


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

        // Add "Add New Product" card first
        const addCard = document.createElement('div');
        addCard.classList.add('product-card', 'add-product-card');
        addCard.innerHTML = `
            <div class="add-product-inner">
                <p>+ Add New Product</p>
            </div>
        `;
        addCard.addEventListener('click', () => {
            window.location.href = 'admin_add_product.php';
        });
        container.appendChild(addCard);

        // Add product cards
        filteredData.forEach(product => {
            const productCard = document.createElement('div');
            productCard.classList.add('product-card');
            productCard.innerHTML = `
                <img src="../images/${product.image}" alt="${product.name}">
                <div class="product-info">
                    <p><strong>Product #:</strong> ${product.product_ID}</p>
                    <p><strong>Price:</strong> £${product.price}</p>
                    <p><strong>Stock Available:</strong> ${product.stock}</p>

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
</script>

</body>
</html>

