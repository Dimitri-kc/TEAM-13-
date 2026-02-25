
<?php
include '../backend/config/db_connect.php';
session_start();

if (!isset($_SESSION["cart"])) {
  // cart format: [ product_id => qty ]
  $_SESSION["cart"] = [];
}

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
function money($n){ return "£" . number_format((float)$n, 2); }

// Actions 
$action = $_GET["action"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // Add item from product pages
  if ($action === "add") {
    $id = (int)($_POST["product_id"] ?? 0);
    $qty = max(1, (int)($_POST["qty"] ?? 1));
    if ($id > 0) {
      $_SESSION["cart"][$id] = ($_SESSION["cart"][$id] ?? 0) + $qty;
    }
    header("Location: basket.php");
    exit;
  }

  // Update quantities OR remove item 
if ($action === "update") {

  // if clicking remove button 
  if (isset($_POST["remove_id"])) {
    $removeId = (int)$_POST["remove_id"];
    if ($removeId > 0) {
      unset($_SESSION["cart"][$removeId]);
    }
    header("Location: basket.php");
    exit;
  }

 
  $qtyArr = $_POST["qty"] ?? [];
  if (is_array($qtyArr)) {
    foreach ($qtyArr as $id => $q) {
      $id = (int)$id;
      $q  = (int)$q;
      if ($id <= 0) continue;

      if ($q <= 0) unset($_SESSION["cart"][$id]);  // qty 0 = remove
      else $_SESSION["cart"][$id] = $q;
    }
  }

  header("Location: basket.php");
  exit;
}

  // Remove one
//   if ($action === "remove") {
//     $id = (int)($_POST["product_id"] ?? 0);
//     if ($id > 0) unset($_SESSION["cart"][$id]);
//     header("Location: basket.php");
//     exit;
//   }

  // Clear basket
  if ($action === "clear") {
    $_SESSION["cart"] = [];
    header("Location: basket.php");
    exit;
  }
}

// Load products to cart 

$cart = $_SESSION["cart"];
$cartProducts = [];
$subtotal = 0.0;

if (count($cart) > 0) {
  $ids = array_keys($cart);
  $placeholders = implode(",", array_fill(0, count($ids), "?"));

  $sql = "SELECT product_id, product_name, price, image_path
          FROM products
          WHERE product_id IN ($placeholders)";

  $stmt = $conn->prepare($sql);

  if ($stmt) {
    $types = str_repeat("i", count($ids));
    $stmt->bind_param($types, ...$ids);
    $stmt->execute();
    $res = $stmt->get_result();

    while ($row = $res->fetch_assoc()) {
      $id = (int)$row["product_id"];
      $qty = (int)($cart[$id] ?? 1);
      $price = (float)$row["price"];
      $line = $price * $qty;

      $subtotal += $line;

      $cartProducts[] = [
        "id" => $id,
        "name" => $row["product_name"],
        "price" => $price,
        "qty" => $qty,
        "line" => $line,
        "image" => $row["image_path"] ?: "../images/basket-images/sofa.jpg"
      ];
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket | LOFT & LIVING</title>

    <link rel="stylesheet" href="../css/header_footer_style.css">

    <link rel="stylesheet" href="../css/basket.css">

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
                <a href="basket.php">
                    <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
                </a>
            </div>
        </div>

        <nav class="dropdown-panel" id="dropdown-nav">
            <ul class="nav-links">
                <li><a href="Categories.php">Living Room</a></li>
                <li><a href="bathroom.php">Bathroom</a></li>
                <li><a href="bedroom.php">Bedroom</a></li>
                <li><a href="office.php">Office</a></li>
                <li><a href="kitchen.php">Kitchen</a></li>
                <li class="nav-divider"><a href="signin.php">My Account</a></li>
            </ul>
        </nav>
    </header>
    <main class="basket-container">

                

        <h1 class="basket-title">YOUR BASKET</h1>

        <section class="basket-layout">

            <div class="basket-items">

  <?php if (count($cartProducts) === 0): ?>
   
    <div style="border:1px dashed #d8d8d8; border-radius:16px; padding:26px 22px; color:#555; background:#fff;">
      <div style="font-size:20px; margin-bottom:6px;">
        No items in your basket yet. Go to categories and add a product.
      </div>
    </div>

    <a href="homepage.php" style="display:inline-block; margin-top:14px; color:#000;">
      Back to Homepage
    </a>

  <?php else: ?>

    <form method="post" action="basket.php?action=update">
      <?php foreach ($cartProducts as $p): ?>
        <div class="basket-item">
          <img src="<?= e($p["image"]) ?>" alt="<?= e($p["name"]) ?>" class="item-image">

          <div class="item-details">
            <h3 class="item-name"><?= e($p["name"]) ?></h3>
            <p class="price"><?= money($p["price"]) ?></p>

            <div class="quant-controls">
              <input
                class="quant-number"
                style="width:70px; padding:8px; border:1px solid #ddd; border-radius:10px;"
                type="number"
                min="0"
                name="qty[<?= (int)$p["id"] ?>]"
                value="<?= (int)$p["qty"] ?>"
              />
              <span style="color:#555; margin-left:10px;">Line: <?= money($p["line"]) ?></span>
            </div>

            <div style="margin-top:10px;">
  <button 
    type="submit"
    name="remove_id"
    value="<?= (int)$p["id"] ?>"
    class="discount-btn"
    style="background:#fff; color:#000; border:1px solid #000;"
  >
    Remove
  </button>
</div>

          </div>
        </div>
      <?php endforeach; ?>

      <button type="submit" class="discount-btn" style="margin-top:12px; background:#000; color:#fff; border:1px solid #000;">
        Update Basket
      </button>
    </form>

    <form method="post" action="basket.php?action=clear" style="margin-top:10px;">
      <button type="submit" class="discount-btn" style="background:#fff; color:#000; border:1px solid #000;">
        Clear Basket
      </button>
    </form>

  <?php endif; ?>

</div>

            <aside class="basket-summary">

                <div class="discount-box">
                    <label>Any Discount or Promotional Codes?</label>
                    <input type="text" placeholder="Enter code">
                    <button class="discount-btn">Add Discount</button>
                </div>

               <div class="summary-box">
    <h3>Your Basket Total</h3>

    <?php if (count($cartProducts) === 0): ?>
        <p>Basket: £0.00</p>
        <p>Discount: £0.00</p>
        <p><strong>Total: £0.00</strong></p>
    <?php else: ?>
        <p>Basket: <?= money($subtotal) ?></p>
        <p>Discount: £0.00</p>
        <p><strong>Total: <?= money($subtotal) ?></strong></p>
    <?php endif; ?>
</div>

            <div class="checkout-info">
                <h3>Ready To Checkout?</h3>
                <p>On the next page you’ll be asked to log in or sign up if
                this is your first time, so you can confirm as a guest.</p>
                 <button class="checkout-btn" onclick="window.location.href='checkout.php'">Checkout</button>
                                    <div class="pay-buttons">

                    <img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
                    <img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
                </div>
            </div>


            </aside>

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
    

</body>
</html>

