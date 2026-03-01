<?php
include '../backend/config/db_connect.php';
session_start();
if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit();
}
//commented out while testing
/* if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["place_order"])) {

  // Collects customer info 
  $customer = [
    "name"     => $_POST["name"] ?? "",
    "email"    => $_POST["email"] ?? "",
    "address1" => $_POST["address1"] ?? "",
    "address2" => $_POST["address2"] ?? "",
    "city"     => $_POST["city"] ?? "",
    "state"    => $_POST["state"] ?? "",
    "postcode" => $_POST["postcode"] ?? "",
    "phone"    => $_POST["phone"] ?? "",
  ];

  // Collect items from session cart 

  $items = $_SESSION["cart_items"] ?? [];

  // Store order for confirmation page
  $_SESSION["order"] = [
    "number" => (string)random_int(100000, 999999),
    "date" => date("F j, Y"),
    "currency" => "£",
    "payment_method" => "Card",
    "customer" => $customer,
    "items" => $items,
    "shipping" => 0,
    "tax" => 0,
    "discount" => 0,
    "continue_url" => "homepage.php"
  ];

  header("Location: order_confirmation.php");
  exit;
} */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout page</title>

    <link rel="stylesheet" href="../css/header_footer_style.css">

    <link rel="stylesheet" href="../css/checkout.css" />
</head>
<body>

    <header class="site-header">
        <div class="header-inner">
            <button class="menu-btn" id="menu-toggle-btn">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img"> 
            </button>

            <div class="logo-wrapper">
                <a href="homepage.php">
                    <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.php">
                    <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
                </a>
                <a href="signin.php">
                    <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                </a>
                <a href="basket.php" class="basket-icon">
                    <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
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
                <li class="nav-divider"><a href="signin.php">My Account</a></li>
            </ul>
        </nav>
    </header>
    <header class="checkout-header">
        <h1>CHECKOUT</h1>
    </header>

    <main class="checkout-layout">

<aside class="product-column" id="basket-items-container">
    <p>Loading basket...</p>

    <!--BASKET TOTALS-->
    <div class="basket-totals">
        <div class="total-row">
            <span>Subtotal:</span>
            <span id="subtotal">£0.00</span>
        </div>
        <div class="total-row">
             <span>Delivery:</span>
            <span id="delivery">£0.00</span>
        </div>
        <div class="total-row total">
            <span><strong>Total:</strong></span>
            <span id="total"><strong>£0.00</strong></span>
        </div>
    </div>
<!--commented out php while testing-->
<!--   <?php
    function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
    function money($n){ return "£" . number_format((float)$n, 2); }

    $items = $_SESSION["cart_items"] ?? [];
    if (!is_array($items) || count($items) === 0):
  ?>
      <p>Your basket is empty.</p>
  <?php else: ?>
      <?php foreach ($items as $item): ?>
        <div class="product-item">
          <img src="<?= e($item["image"] ?? "../images/basket-images/sofa.jpg") ?>" alt="<?= e($item["name"] ?? "Item") ?>"/>
          <div class="product-text">
            <p><?= e($item["name"] ?? "") ?></p>
            <p class="price"><?= money((float)($item["price"] ?? 0)) ?></p>
            <p class="quantity">Quantity: <?= (int)($item["qty"] ?? 1) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
  <?php endif; ?> -->

</aside>

        <section class="details-column">
            
    <form method="post" id="checkout-form">
  <section class="review-box">
    <h1 class="form-title">YOUR DETAILS</h1>

    <div class="review-details">
      <label><strong>Name *</strong></label>
      <input type="text" name="name" required>

      <label><strong>Email *</strong></label>
      <input type="email" name="email" required>

      <label><strong>Phone</strong></label>
      <input type="text" name="phone">

      <label><strong>Address line 1 *</strong></label>
      <input type="text" name="address1" required>

      <label><strong>Address line 2</strong></label>
      <input type="text" name="address2">

      <label><strong>City *</strong></label>
      <input type="text" name="city" required>

      <label><strong>State</strong></label>
      <input type="text" name="state">

      <label><strong>Postcode *</strong></label>
      <input type="text" name="postcode" required>
    </div>
  </section>


    <div class="card-fields">
        <h2>Card Details</h2>

        <input type="text" name="card_number" placeholder="Card Number (16 Digits)" maxlength="19" inputmode="numeric" required />
        <input type="text" name="expiry" placeholder="Expiry Date (MM/YY)" pattern="(0[1-9]|1[0-2])/[0-9]{2}" required />
        <input type="text" name="cvv" placeholder="CVV (3 Digits)" maxlength="3" inputmode="numeric" required />
        <button type="submit" name="place_order" class="submit-btn">Submit</button>

        <div class="pay-buttons">
            <img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
            <img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
        </div>
    </div>

            <div class="delivery-section">
                <p>Ready for Loft & Living in Your Home?</p>
                <p>Your Order will be dispatched using Standard Delivery </p>
                <p>Estimated Delivery: 9th March 2026</p>

                <button class="checkout-btn" type="submit" name="place_order" value="1">Checkout</button>
            </div>
    </form>

    </section>
        
    </main>
    <footer class="site-footer">
        <div class="footer-inner">
            <div class="footer-section social-links">
                <a href="#">
                    <img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon">
                </a>
                <a href="#">
                    <img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon">
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
    <script src="../javascript/checkout.js"></script>
        
</body>
</html>