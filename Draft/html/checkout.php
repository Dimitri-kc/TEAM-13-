<?php
include '../backend/config/db_connect.php';
session_start();

//fetch user details for pre-filling form
$user_ID = $_SESSION['user_ID'] ?? null;
$stmt = $conn->prepare("SELECT name, surname, email, phone, address FROM users WHERE user_ID = ?");
$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
//prepares variables for form pre-fill > null handles missing data
$userName = trim(($user['name'] ?? '') . ' ' . ($user['surname'] ?? ''));//combine name + surname stored in DB
$userEmail = $user['email'] ?? '';
$userPhone = $user['phone'] ?? '';
$userAddress1 = $user['address'] ?? ''; //DB has single address field
$userAddress2 = '';
$userCity = '';
$userCountyRegion = '';
$userPostcode = '';

function money($n){
    return "£" . number_format((float)$n, 2);
}

if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit();
}

$user_ID = isset($_SESSION["user_ID"]) ? (int)$_SESSION["user_ID"] : null;
$cart = ($user_ID > 0) ? ($_SESSION["cart"] ?? []) : ($_SESSION["guest_basket"] ?? []);

$subtotal = 0.0;
$delivery = 0.0;
$tax = 0.0;
$total = 0.0;

$ids = array_keys($cart);
if (count($ids) > 0) {
    $placeholders = implode(",", array_fill(0, count($ids), "?"));
    $sql = "SELECT product_ID, price FROM products WHERE product_ID IN ($placeholders)";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
        $stmt->execute();
        $res = $stmt->get_result();

        while ($row = $res->fetch_assoc()) {
            $pid = (int)$row["product_ID"];
            $qty = (int)($cart[$pid] ?? 0);
            $price = (float)$row["price"];
            $subtotal += ($price * $qty);
        }
        $stmt->close();
    }
}

$tax = $subtotal * 0.10;
$total = $subtotal + $tax + $delivery;
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

.card-fields .form-title{
    display: block;
    width: 100%;
    box-sizing: border-box;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 12px 14px;
    margin: 0 0 10px 0;
    font-size: 15px;
    font-weight: 600;
    line-height: 1.2;
    background: #fff;
}

input[name="postcode"]{
    margin-bottom: 28px !important;
}
.card-fields{
    margin-top: 12px !important;
    clear: both;
}

.payment-summary{
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 18px 16px;
    margin: 14px 0;
    background: #fff;
}
.payment-summary .row{
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 8px 0;
}
.payment-summary .row.total{
    font-weight: 700;
    font-size: 16px;
    margin-top: 10px;
}

.view-basket-btn{
    display: inline-block;
    padding: 10px 12px;
    border: 1px solid #111;
    border-radius: 6px;
    background: #fff;
    color: #111;
    text-decoration: none;
    font-size: 12px;
}
.payment-actions{
    display:flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    margin: 12px 0;
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
<span>Tax (10%):</span>
<span id="tax"><?= money($tax) ?></span>
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
<label><strong>Full Name *</strong></label>
<input type="text" name="name" value="<?php echo htmlspecialchars($userName); ?>" required>

<label><strong>Email *</strong></label>
<input type="email" name="email" value="<?php echo htmlspecialchars($userEmail); ?>" required>

<label><strong>Phone</strong></label>
<input type="text" name="phone" value="<?php echo htmlspecialchars($userPhone); ?>">

<label><strong>Address line 1 *</strong></label>
<input type="text" name="address1" value="<?php echo htmlspecialchars($userAddress1); ?>" required>

<label><strong>Address line 2</strong></label>
<input type="text" name="address2" value="<?php echo htmlspecialchars($userAddress2); ?>">

<label><strong>City *</strong></label>
<input type="text" name="city" value="<?php echo htmlspecialchars($userCity); ?>" required>

<label><strong>County/Region *</strong></label>
<input type="text" name="county_region" value="<?php echo htmlspecialchars($userCountyRegion); ?>" required>

<label><strong>Postcode *</strong></label>
<input type="text" name="postcode" value="<?php echo htmlspecialchars($userPostcode); ?>" required>
</div>
</section>

<div class="card-fields">

<h1 class="form-title">CARD DETAILS</h1>

<input type="text" id="card_number" name="card_number" placeholder="Card Number (1234 4567 8901 2345)" maxlength="19" inputmode="numeric" required />
<input type="text" id="expiry" name="expiry" placeholder="Expiry Date (MM/YY)" maxlength="5" pattern="(0[1-9]|1[0-2])/[0-9]{2}" required />
<input type="text" id="cvv" name="cvv" placeholder="CVV (3 Digits)" maxlength="3" inputmode="numeric" required />

<div class="payment-summary">
    <div class="row">
        <span>Subtotal</span>
        <span><?= money($subtotal) ?></span>
    </div>
    <div class="row">
        <span>Tax (10%)</span>
        <span><?= money($tax) ?></span>
    </div>
    <div class="row">
        <span>Delivery</span>
        <span><?= money($delivery) ?></span>
    </div>
    <div class="row total">
        <span>Total</span>
        <span><?= money($total) ?></span>
    </div>
</div>

<div class="payment-actions">
    <a href="basket.php" class="view-basket-btn">View Basket</a>
    <div style="font-weight:700;">
        Total: <?= money($total) ?>
    </div>
</div>

<button type="submit" name="place_order" class="submit-btn">Confirm Payment</button>

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
<script src="../javascript/checkout.js"></script>

</body>
</html>