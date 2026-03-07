<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../backend/config/db_connect.php';

$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Product Inventory | LOFT & LIVING</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/admin_product_inventory.css">
    <style>
        body { padding-top: 120px; }
        .site-header {
            position: fixed;
            top: 20px;
            left: 40px;
            right: 40px;
            z-index: 1000;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border-radius: 50px;
            height: 80px;
        }
        .header-left-tools { display: flex; align-items: center; gap: 25px; }
        .logo-wrapper { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
        .main-logo { height: 50px !important; width: auto !important; max-width: 280px; object-fit: contain; display: block; filter: invert(1); opacity: 0.95; }
        .header-actions { display: flex; align-items: center; gap: 25px; }
        html.dark-mode .site-header { background-color: #1a1a1a; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); }
        html.dark-mode .ui-icon { filter: invert(1); }
        html.dark-mode .main-logo { filter: invert(0); }
        html.dark-mode body { background-color: #1a1a1a; color: #e0e0e0; }
        html.dark-mode .site-footer { background-color: #1a1a1a; border-top: 1px solid #333; }
        #basket-count { display: none !important; }
    </style>
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>

    <header class="site-header">
        <div class="header-inner">
            <div class="header-left-tools">
                <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
                    <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
                </button>
                <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
                <a class="mini-search" href="search.php" aria-label="Search">
                    <img src="../images/header_footer_images/icon-search.png" alt="Search" class="ui-icon" id="search-icon" style="vertical-align: middle;">
                </a>
            </div>

            <div class="logo-wrapper">
                <a href="homepage.php">
                    <img src="../images/header_footer_images/logo1.png" alt="LOFT &amp; LIVING" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon"></a>
                <div class="profile-wrapper" id="profile-wrapper">
                    <button class="profile-btn" id="profile-toggle-btn" type="button" aria-haspopup="true" aria-expanded="false">
                        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                    </button>
                    <div class="profile-dropdown" id="profile-dropdown">
                        <?php if ($isLoggedIn): ?>
                            <div class="profile-welcome">Welcome, <?php echo htmlspecialchars($headerName); ?></div>
                        <?php else: ?>
                            <div class="profile-welcome">Welcome to Loft & Living</div>
                        <?php endif; ?>
                        <?php if (!$isLoggedIn): ?>
                            <a class="profile-link" href="signin.php">Sign in</a>
                            <a class="profile-link" href="signup.php">Sign Up</a>
                        <?php endif; ?>
                        <a class="profile-link" href="user_dash.php">My Account</a>
                        <?php if ($isLoggedIn): ?>
                            <a class="profile-link" href="user_order_history.php">My Orders</a>
                            <a class="profile-link" href="signout.php">Sign out</a>
                        <?php endif; ?>
                    </div>
                </div>
                <a href="basket.php" class="basket-icon"><img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon"><span id="basket-count">0</span></a>
            </div>
        </div>

        <nav class="dropdown-panel" id="dropdown-nav">
            <ul class="nav-links">
                <li><a href="livingroom.php">Living Room</a></li>
                <li><a href="bathroom.php">Bathroom</a></li>
                <li><a href="bedroom.php">Bedroom</a></li>
                <li><a href="office.php">Office</a></li>
                <li><a href="kitchen.php">Kitchen</a></li>

            </ul>
        </nav>
    </header>

    <div class="admin-wrapper">
    <h1 class="title">Product Inventory</h1>
    <p class="subtitle">View current product inventory, edit product information and add more products </p>

<div class="filter-wrapper">
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

        // Add "Add New Product" card first
        // const addCard = document.createElement('div');
        // addCard.classList.add('product-card', 'add-product-card');
        // addCard.innerHTML = `
        //     <div class="add-product-inner">
        //         <p>+ Add New Product</p>
        //     </div>
        // `;
        // addCard.addEventListener('click', () => {
        //     window.location.href = 'admin_add_product.php';
        // });
        // container.appendChild(addCard);

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

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-section social-links">
            <a href="#"><img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon"></a>
            <a href="#"><img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon"></a>
        </div>
        <div class="footer-section"><h4>Navigation</h4><ul><li><a href="homepage.php">Homepage</a></li><li><a href="user_dash.php">My Account</a></li><li><a href="favourites.php">Favourites</a></li><li><a href="basket.php">Basket</a></li></ul></div>
        <div class="footer-section"><h4>Categories</h4><ul><li><a href="livingroom.php">Living Room</a></li><li><a href="office.php">Offices</a></li><li><a href="kitchen.php">Kitchen</a></li><li><a href="bathroom.php">Bathrooms</a></li><li><a href="bedroom.php">Bedrooms</a></li></ul></div>
        <div class="footer-section"><h4>More...</h4><ul><li><a href="contact.php">Contact Us</a></li><li><a href="about.php">About Us</a></li></ul></div>
    </div>
</footer>

<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>

</body>
</html>

