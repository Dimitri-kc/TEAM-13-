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

  header("Location: orderconfirmation.php");
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
<link rel="stylesheet" href="../css/checkout.css">

<style>
#basket-count { display:none!important; }
.checkout-layout{ display:flex!important; }
.product-column{ order:1!important; }
.details-column{ order:2!important; }
.product-column img{ display:none!important; }
.submit-btn,.checkout-btn{ background:#ccc!important; }

/* ==== FIX for CARD DETAILS header appearance ==== */
/* make header match the input boxes in that card-fields box */
.card-fields .form-title{
    display: block;
    width: 100%;
    box-sizing: border-box; /* keep total width stable with border/padding; aligns with inputs */ 
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 12px 14px;       /* similar padding as inputs */
    margin: 0 0 10px 0;       /* space below header, like inputs have between them */
    font-size: 15px;          /* closer to input text size */
    font-weight: 600;
    line-height: 1.2;         /* compact lines */
    background: #fff;
}
</style>
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

<aside class="product-column">

<div class="basket-totals">

<div class="total-row">
<span>Subtotal:</span>
<span id="subtotal"><?= money($subtotal) ?></span>
</div>

<div class="total-row">
<span>Delivery:</span>
<span id="delivery"><?= money($delivery) ?></span>
</div>

<div class="total-row total">
<span><strong>Total:</strong></span>
<span id="total"><strong><?= money($total) ?></strong></span>
</div>

</div>

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

<label><strong>County/Region *</strong></label>
<input type="text" name="county_region" required>

      <label><strong>Postcode *</strong></label>
      <input type="text" name="postcode" required>
    </div>
  </section>


<div class="card-fields">

<h1 class="form-title">CARD DETAILS</h1>

        <input type="text" name="card_number" placeholder="Card Number (16 Digits)" maxlength="19" inputmode="numeric" required />
        <input type="text" name="expiry" placeholder="Expiry Date (MM/YY)" pattern="(0[1-9]|1[0-2])/[0-9]{2}" required />
        <input type="text" name="cvv" placeholder="CVV (3 Digits)" maxlength="3" inputmode="numeric" required />
        <button type="submit" name="place_order" class="submit-btn">Submit</button>

<div class="pay-buttons">
<img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
<img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
</div>

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

</body>
</html>