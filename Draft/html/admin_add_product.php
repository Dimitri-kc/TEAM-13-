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
    <title>Add Product | Admin | LOFT & LIVING</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <script src="../javascript/dark-mode.js"></script>
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
        html.dark-mode .admin-wrapper { background-color: #242424; color: #e0e0e0; }
        html.dark-mode .admin-form input, html.dark-mode .admin-form select { background-color: #1a1a1a; border-color: #444; color: #e0e0e0; }
        html.dark-mode .site-footer { background-color: #1a1a1a; border-top: 1px solid #333; }
        #basket-count { display: none !important; }

/* Center wrapper like a card, smaller than before */
.admin-wrapper {
    max-width: 500px;
    margin: 50px auto;
    padding: 30px 25px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* Titles */
.title {
    font-size: 22px;
    margin-bottom: 8px;
    color: #333;
    text-align: center;
}

.subtitle {
    font-size: 14px;
    color: #777;
    margin-bottom: 25px;
    text-align: center;
}

/* Form styling */
.admin-form {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.admin-form label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #555;
}

/* Inputs and select */
.admin-form input[type="text"],
.admin-form input[type="number"],
.admin-form select,
.admin-form input[type="file"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 6px;
    width: 100%;
    box-sizing: border-box;
    transition: border 0.2s ease, box-shadow 0.2s ease;
}

.admin-form input[type="text"]:focus,
.admin-form input[type="number"]:focus,
.admin-form select:focus,
.admin-form input[type="file"]:focus {
    border-color: #259131;
    box-shadow: 0 0 5px rgba(37, 145, 49, 0.3);
    outline: none;
}

.admin-form select {
    background: #fff;
    appearance: none;
    cursor: pointer;
}

/* File input smaller padding */
.admin-form input[type="file"] {
    padding: 6px;
    cursor: pointer;
}

/* Submit Button */
.submit-btn {
    background: #2c2c2c;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 6px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
    margin-top: 10px;
}

.submit-btn:hover {
    background: #259131;
}
.cancel-btn {
    color: #333;
    border: none;
    padding: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: 0.2s ease;
}

.cancel-btn:hover {
    background: #d0d0d0;
}
    </style>
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <div class="header-left-tools">
            <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu"><img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img"></button>
            <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
            <a class="mini-search" href="search.php" aria-label="Search"><img src="../images/header_footer_images/icon-search.png" alt="Search" class="ui-icon" id="search-icon" style="vertical-align: middle;"></a>
        </div>
        <div class="logo-wrapper"><a href="homepage.php"><img src="../images/header_footer_images/logo1.png" alt="LOFT &amp; LIVING" class="main-logo"></a></div>
        <div class="header-actions">
            <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon"></a>
            <div class="profile-wrapper" id="profile-wrapper">
                <button class="profile-btn" id="profile-toggle-btn" type="button" aria-haspopup="true" aria-expanded="false"><img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon"></button>
                <div class="profile-dropdown" id="profile-dropdown">
                    <?php if ($isLoggedIn): ?><div class="profile-welcome">Welcome, <?php echo htmlspecialchars($headerName); ?></div><?php else: ?><div class="profile-welcome">Welcome to Loft & Living</div><?php endif; ?>
                    <?php if (!$isLoggedIn): ?><a class="profile-link" href="signin.php">Sign in</a><a class="profile-link" href="signup.php">Sign Up</a><?php endif; ?>
                    <a class="profile-link" href="user_dash.php">My Account</a>
                    <?php if ($isLoggedIn): ?><a class="profile-link" href="user_order_history.php">My Orders</a><a class="profile-link" href="signout.php">Sign out</a><?php endif; ?>
                </div>
            </div>
            <a href="basket.php" class="basket-icon"><img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon"><span id="basket-count">0</span></a>
        </div>
    </div>
    <nav class="dropdown-panel" id="dropdown-nav"><ul class="nav-links"><li><a href="livingroom.php">Living Room</a></li><li><a href="bathroom.php">Bathroom</a></li><li><a href="bedroom.php">Bedroom</a></li><li><a href="office.php">Office</a></li><li><a href="kitchen.php">Kitchen</a></li></ul></nav>
</header>

<div class="admin-wrapper">
    <h1 class="title">Add New Product</h1>
    <p class="subtitle">Fill in the details below to add a new product</p>

    <form class="admin-form" method="POST" action="admin_insert_product.php" enctype="multipart/form-data">
        
        <label>Name:</label>
        <input type="text" name="name" placeholder="Enter product name" required>

        <label>Price (£):</label>
        <input type="number" step="0.01" name="price" placeholder="0.00" required>

        <label>Stock:</label>
        <input type="number" name="stock" placeholder="Quantity in stock" required>

        <label>Category:</label>
        <select name="category_id" required>
            <option value="">-- Select Category --</option>
            <option value="1">Living Room</option>
            <option value="2">Kitchen</option>
            <option value="3">Office</option>
            <option value="4">Bathroom</option>
            <option value="5">Bedroom</option>
        </select>

        <label>Image:</label>
        <input type="file" name="image" accept="image/*" required>

        <button type="submit" class="submit-btn">Add Product</button>
        <button type="button" class="cancel-btn" onclick="window.location.href='admin_product_inventory.php'">
    Cancel</button>
    </form>
</div>
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-section social-links"><a href="#"><img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon"></a><a href="#"><img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon"></a></div>
    <div class="footer-section"><h4>Navigation</h4><ul><li><a href="homepage.php">Homepage</a></li><li><a href="user_dash.php">My Account</a></li><li><a href="favourites.php">Favourites</a></li><li><a href="basket.php">Basket</a></li></ul></div>
    <div class="footer-section"><h4>Categories</h4><ul><li><a href="livingroom.php">Living Room</a></li><li><a href="office.php">Offices</a></li><li><a href="kitchen.php">Kitchen</a></li><li><a href="bathroom.php">Bathrooms</a></li><li><a href="bedroom.php">Bedrooms</a></li></ul></div>
    <div class="footer-section"><h4>More...</h4><ul><li><a href="contact.php">Contact Us</a></li><li><a href="about.php">About Us</a></li></ul></div>
  </div>
</footer>

<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>
</body>
</html>