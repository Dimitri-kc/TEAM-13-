<?php include '../backend/config/db_connect.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Product Inventory</title>
    <link rel="stylesheet" href="../css/admin_product_inventory.css">
</head>
<body>

    <h1 class="title">Product Inventory</h1>

    <div id="product-container" class="product-grid">
        <!-- Products will load here automatically -->
    </div>

<script>
fetch('admin_get_products.php')
.then(response => response.json())
.then(data => {
    const container = document.getElementById('product-container');

    data.forEach(product => {
        const productCard = document.createElement('div');
        productCard.classList.add('product-card');

        productCard.innerHTML = `
            <img src="../images/${product.image}" alt="${product.name}">
            <div class="product-info">
                <p><strong>Product #:</strong> ${product.product_ID}</p>
                <p><strong>Price:</strong> £${product.price}</p>
                <p><strong>Stock Available:</strong> ${product.stock}</p>
            </div>
        `;

        container.appendChild(productCard);
    });
})
.catch(error => console.error('Error loading products:', error));
</script>

</body>
</html>

