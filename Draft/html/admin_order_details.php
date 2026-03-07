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
<title>Admin Order Details | LOFT & LIVING</title>

<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../css/header_footer_style.css?v=12">
<link rel="stylesheet" href="../css/dark-mode.css?v=9">


<style>
body {
  font-family: "Ibarra Real Nova", serif;
  margin: 40px;
  padding-top: 120px;
  color: #2B2B2B;
  background: #F4F1EC;
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
html.dark-mode .left-section, html.dark-mode .right-section { background-color: #242424; border-color: #444; }
html.dark-mode .form-row label { color: #e0e0e0; }
html.dark-mode .form-row input { background-color: #1a1a1a; border-color: #444; color: #e0e0e0; }
html.dark-mode .site-footer { background-color: #1a1a1a; border-top: 1px solid #333; }

#basket-count { display: none !important; }

/* Page layout */
.container {
  display: flex;
  gap: 40px;
  max-width: 1000px;
  margin: 40px auto;
  align-items: flex-start;
}

/* Left card */
.left-section {
  flex: 1;
  
  padding: 28px;
  border-radius: 14px;
  border: 1px solid #e6e6e6;
}

/* Right card */
.right-section {
  flex: 1;
  display: flex;
  flex-direction: column;
  background: #f5f5f5;
  padding: 32px;
  border-radius: 14px;
  border: 1px solid #e0e0e0;
}

/* Order image */
.order-image {
  width: 300px;
  height: 300px;
  background-color: #d3d3d3;
  border-radius: 10px;
  margin-top: 20px;
}

/* Form layout: label + field */
.form-row {
  display: grid;
  grid-template-columns: 160px 1fr;
  align-items: center;
  margin-bottom: 22px;
  gap: 16px;
}

.form-row label {
  font-weight: 600;
  font-size: 15px;
  color: #333;
  text-align: right;
}

.form-row input {
  width: 100%;
  padding: 12px 14px;
  border-radius: 8px;
  border: 1px solid #cfcfcf;
  background: #fff;
  font-size: 15px;
  transition: border-color 0.2s, box-shadow 0.2s;
}

.form-row input:focus {
  border-color: #2C2C2C;
  box-shadow: 0 0 0 3px rgba(44,44,44,0.15);
  outline: none;
}

/* Buttons */
button {
  width: 100%;
  background-color: #2C2C2C;
  color: white;
  border: none;
  padding: 14px 0;
  margin-bottom: 14px;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  font-size: 15px;
  transition: background-color 0.25s, transform 0.15s;
}

button:hover {
  background-color: #000;
  transform: translateY(-1px);
}

.shipping-address {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 10px;
}
/* Responsive layout */
@media (max-width: 900px) {
  .container {
    flex-direction: column;
    gap: 30px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .form-row label {
    text-align: left;
  }

  .right-section,
  .left-section {
    padding: 22px;
  }

  .order-image {
    width: 100%;
    height: auto;
    aspect-ratio: 1 / 1;
  }
}
</style>
<script src="../javascript/dark-mode.js"></script>


</head>
<body>



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

<div class="container">
  <div class="left-section">
    <div class="order-info">
      <h2>Order #UK12345</h2>
      <p>Customer: FirstName LastName</p>
    </div>
    <div class="order-image" aria-label="Order product image placeholder"></div>
  </div>

  <div class="right-section">
    <div class="shipping-address">Shipping Address:</div>
<form id="shipping-form" class="shipping-details" method="POST" action="update-order.php">

  <div class="form-row">
    <label for="full_name">Full Name:</label>
    <input type="text" id="full_name" name="full_name" value="FirstName LastName" readonly>
  </div>

  <div class="form-row">
    <label for="address1">Address Line 1:</label>
    <input type="text" id="address1" name="address1" value="Address Line 1" readonly>
  </div>

  <div class="form-row">
    <label for="address2">Address Line 2:</label>
    <input type="text" id="address2" name="address2" value="Address Line 2" readonly>
  </div>

  <div class="form-row">
    <label for="postcode">Postcode:</label>
    <input type="text" id="postcode" name="postcode" value="Postcode" readonly>
  </div>

  <div class="form-row">
    <label for="county">County:</label>
    <input type="text" id="county" name="county" value="County" readonly>
  </div>

  <div class="form-row">
    <label for="country">Country:</label>
    <input type="text" id="country" name="country" value="Country" readonly>
  </div>

  <button type="submit" id="save-btn" style="display:none;">Save Changes</button>
</form>

    <!-- <label for="cancel-lines">Cancel Select Lines</label>
    <select id="cancel-lines" name="cancel-lines">
      <option value="" disabled selected>Value</option>
         Populate options dynamically here -->
      <!-- <option value="line1">Line 1</option>
      <option value="line2">Line 2</option>
      <option value="line3">Line 3</option>
    </select> --> 

    <button type="button" id="edit-btn">Edit Details</button>
    <button type="button">Confirm Shipment Processing</button>
    <button type="button">Cancel Order</button>
  </div>
</div>
<script>
const editBtn = document.getElementById('edit-btn');
const saveBtn = document.getElementById('save-btn');
const form = document.getElementById('shipping-form');

editBtn.addEventListener('click', () => {
  // Make all input fields editable
  const inputs = form.querySelectorAll('input');
  inputs.forEach(input => input.removeAttribute('readonly'));

  // Show the Save button
  saveBtn.style.display = 'block';

  // Optionally hide the edit button to prevent multiple clicks
  editBtn.style.display = 'none';
});
</script>

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