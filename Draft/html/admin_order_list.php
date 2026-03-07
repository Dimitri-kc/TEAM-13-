<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin Orders & Shipments | LOFT & LIVING</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/header_footer_style.css?v=12">
<link rel="stylesheet" href="../css/dark-mode.css?v=9">

<style>
  body {
    font-family: "Ibarra Real Nova", serif;
    background: #fff;
    margin: 0;
    padding-top: 120px;
    padding-left: 20px;
    padding-right: 20px;
    padding-bottom: 40px;
    color: #1a1a1a;
  }

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
  html.dark-mode .order-card { background-color: #242424; border-color: #444; }
  html.dark-mode .order-status, html.dark-mode .order-number, html.dark-mode .customer-name { color: #e0e0e0; }
  html.dark-mode .site-footer { background-color: #1a1a1a; border-top: 1px solid #333; }

  #basket-count { display: none !important; }

  .admin-container {
    max-width: 900px;
    margin: 0 auto;
  }

  h1 {
    font-weight: 700;
    text-align: left;
    font-size: 30px;
    margin-bottom: 4px;
  }

  p.subheader {
    color: #6c6c6c;
    font-weight: 400;
    font-size: 14px;
    margin-top: 0;
    margin-bottom: 24px;
  }

  .orders-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 50px;
  }

  .order-card {
    border: 1px solid #e2e2e2;
    border-radius: 6px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .order-card img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    background: #eee;
    flex-shrink: 0;
  }

  .order-details {
    flex-grow: 1;
    font-size: 14px;
  }

  .order-status {
    font-weight: 600;
    font-size: 12px;
    color: #333;
    margin-bottom: 4px;
  }

  .order-number {
    font-weight: 700;
    font-size: 16px;
    margin: 0 0 4px 0;
  }

  .customer-name {
    margin: 0;
    font-weight: 500;
    color: #555;
  }

  .order-actions {
    display: flex;
    gap: 10px;
  }

  button {
    border-radius: 6px;
    border: none;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
    white-space: nowrap;
  }

  .btn-view-edit {
    background-color: #ddd;
    color: #333;
  }

  .btn-view-edit:hover {
    background-color: #ccc;
  }

  .btn-cancel {
    background-color: #2C2C2C;
    color: white;
  }

  .btn-cancel:hover {
    background-color: #1a1a1a;
  }

  /* Responsive: single column on small screens */
  @media (max-width: 600px) {
    .orders-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
<script src="../javascript/dark-mode.js"></script>
</head>
<body data-category="livingroom">

<!-- Header -->
<header class="site-header">
  <div class="header-inner">
    <div class="header-left-tools">
      <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
        <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img" />
      </button>
      <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
      <a class="mini-search" href="search.php" aria-label="Search">
        <img src="../images/header_footer_images/icon-search.png" alt="Search" class="ui-icon" id="search-icon" style="vertical-align: middle;">
      </a>
    </div>

    <div class="logo-wrapper">
      <a href="homepage.php">
        <img src="../images/header_footer_images/logo1.png" alt="LOFT & LIVING" class="main-logo" />
      </a>
    </div>

    <div class="header-actions">
      <a href="favourites.php">
        <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon" />
      </a>

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

      <a href="basket.php" class="basket-icon">
        <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon" />
        <span id="basket-count">0</span>
      </a>
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

<div class="admin-container">
  <h1>Order and Shipments</h1>
  <p class="subheader">View recent customer orders and make edits or cancel</p>

  <div class="orders-grid">

    <div class="order-card">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Pending</p>
        <p class="order-number">Order #UK12345</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit">View & Edit</button>
        <button class="btn-cancel">Cancel</button>
      </div>
    </div>

    <div class="order-card">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Shipped</p>
        <p class="order-number">Order #UK12345</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit">View & Edit</button>
        <button class="btn-cancel">Cancel</button>
      </div>
    </div>

    <div class="order-card">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Pending</p>
        <p class="order-number">Order #UK12345</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit">View & Edit</button>
        <button class="btn-cancel">Cancel</button>
      </div>
    </div>

    <div class="order-card">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Pending</p>
        <p class="order-number">Order #UK12345</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit">View & Edit</button>
        <button class="btn-cancel">Cancel</button>
      </div>
    </div>

  </div>
</div>

<!-- Footer -->
<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-section social-links">
      <a href="#">
        <img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon" />
      </a>
      <a href="#">
        <img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon" />
      </a>
    </div>

    <div class="footer-section">
      <h4>Navigation</h4>
      <ul>
        <li><a href="homepage.php">Homepage</a></li>
        <li><a href="signin.php">My Account</a></li>
        <li><a href="favourites.php">Favourites</a></li>
        <li><a href="basket.php">Basket</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h4>Categories</h4>
      <ul>
        <li><a href="livingroom.php">Living Room</a></li>
        <li><a href="office.php">Offices</a></li>
        <li><a href="kitchen.php">Kitchen</a></li>
        <li><a href="bathroom.php">Bathrooms</a></li>
        <li><a href="bedroom.php">Bedrooms</a></li>
      </ul>
    </div>

    <div class="footer-section">
      <h4>More...</h4>
      <ul>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
    </div>
  </div>
</footer>
<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>
</body>
</html>