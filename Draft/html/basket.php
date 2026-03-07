<?php
include '../backend/config/db_connect.php';
session_start();

require_once __DIR__ . '/../backend/models/basketModel.php';
require_once __DIR__ . '/../backend/services/basketFunctions.php'; //guest_basket

/* FIX 1: make sure both carts are always initialised as arrays */
if (!isset($_SESSION["cart"]) || !is_array($_SESSION["cart"])) {
  $_SESSION["cart"] = [];
}
if (!isset($_SESSION["guest_basket"]) || !is_array($_SESSION["guest_basket"])) {
  $_SESSION["guest_basket"] = [];
}

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, "UTF-8"); }
function money($n){ return "£" . number_format((float)$n, 2); }

// Actions
/* FIX 2: allow action to come from POST too (so add works even without ?action=add) */
$action = $_GET["action"] ?? ($_POST["action"] ?? "");

$user_ID = isset($_SESSION["user_ID"]) ? (int)$_SESSION["user_ID"] : null; //get user_ID from session if logged in, otherwise null
$basketModel = new Basket(); //

//use guest_basket for guests (not saved to DB), cart for logged in users (syncs with DB)
//For logged in users, always sync from database to ensure session is up to date
if ($user_ID > 0) {
    $basket = $basketModel->fetchUserBasket($user_ID);
    if ($basket && !empty($basket['basket_ID'])) {
        $items = $basketModel->fetchBasketItems((int)$basket['basket_ID']);
        $_SESSION["cart"] = []; //reset session cart
        foreach ($items as $it) {
            $pid = (int)($it['product_ID'] ?? 0);
            $qty = (int)($it['quantity'] ?? 0);
            if ($pid > 0 && $qty > 0) {
                $_SESSION["cart"][$pid] = $qty;
            }
        }
    } else {
        $_SESSION["cart"] = []; //ensure empty array if no basket
    }
}

$cart = ($user_ID > 0) ? ($_SESSION["cart"] ?? []) : ($_SESSION["guest_basket"] ?? []); //logged in users have cart that syncs with DB

if ($_SERVER["REQUEST_METHOD"] === "POST") {

  // Add item from product pages
  if ($action === "add") {
    $id = (int)($_POST["product_id"] ?? 0);
    $qty = max(1, (int)($_POST["qty"] ?? 1));
    $redirect = $_POST["redirect"] ?? "basket.php";
    if (!is_string($redirect) || preg_match('/^https?:\/\//i', $redirect)) {
      $redirect = "basket.php";
    }

    if ($id > 0) {
      if ($user_ID > 0) {
        $_SESSION["cart"][$id] = ($_SESSION["cart"][$id] ?? 0) + $qty; //update session cart for logged in user
        $basket = $basketModel->fetchUserBasket($user_ID);
        if ($basket && !empty($basket['basket_ID'])) {
          $basketModel->addItemToBasket((int)$basket['basket_ID'], $id, $qty);
        }
      } else {
        $_SESSION["guest_basket"][$id] = ($_SESSION["guest_basket"][$id] ?? 0) + $qty;
      }
    }
    header("Location: $redirect");
    exit;
  }

  // Update quantities OR remove item 
  if ($action === "update") {

    // if clicking remove button 
    if (isset($_POST["remove_id"])) {
      $removeId = (int)$_POST["remove_id"];
      if ($removeId > 0) {
        if ($user_ID > 0) { //if user logged in, also sync to DB and remove item
          unset($_SESSION["cart"][$removeId]);
          $basket = $basketModel->fetchUserBasket($user_ID);
          if ($basket && !empty($basket['basket_ID'])) {
            $basketModel->removeItemFromBasket((int)$basket['basket_ID'], $removeId); //remove item from DB basket
          }
        } else {
          unset($_SESSION["guest_basket"][$removeId]);
        }
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

        if ($user_ID > 0) { //update session cart for logged in user
          if ($q <= 0) {
            unset($_SESSION["cart"][$id]);  // qty 0 = remove
            //if user logged in, also sync to DB and remove item
            $basket = $basketModel->fetchUserBasket($user_ID);
            if ($basket && !empty($basket['basket_ID'])) {
              $basketModel->removeItemFromBasket((int)$basket['basket_ID'], $id); //remove item from DB basket
            }
          } else {
            $_SESSION["cart"][$id] = $q; //if user logged in, sync new quantity to DB
            $basket = $basketModel->fetchUserBasket($user_ID);
            if ($basket && !empty($basket['basket_ID'])) {
              $basketModel->updateItemQuantity((int)$basket['basket_ID'], $id, $q); //update quantity in DB
            }
          }
        } else {
          if ($q <= 0) { //guest user qty 0 = remove
            unset($_SESSION["guest_basket"][$id]);
          } else {
            $_SESSION["guest_basket"][$id] = $q;
          }
        }
      }
    }
    header("Location: basket.php");
    exit;
  }

  // Clear basket
  if ($action === "clear") {
    if ($user_ID > 0) {
      $_SESSION["cart"] = [];
      //sync clear to DB for logged in users
      $basket = $basketModel->fetchUserBasket($user_ID);
      if ($basket && !empty($basket['basket_ID'])) {
        $basket_ID = (int)$basket['basket_ID'];
        $stmt = $conn->prepare("DELETE FROM basket_items WHERE basket_ID = ?"); //clear from DB basket
        if ($stmt) {
          $stmt->bind_param("i", $basket_ID); //changed to variable not direct value
          $stmt->execute();
          $stmt->close();
        }
      }
    } else {
      $_SESSION["guest_basket"] = [];
    }
    header("Location: basket.php");
    exit;
  }
} //POST ENDS

//debugging below
$basketMode = ($user_ID > 0) ? "Account Basket (DB-backed)" : "Guest Basket (Session-only)";
error_log("TESTING: $basketMode | user_ID=$user_ID | cart items=" . count($cart));

// Load products to cart for display
$ids = array_keys($cart);
$cartProducts = [];
$subtotal = 0.0;

if (count($ids) > 0) {
  $ids = array_keys($cart);
  $placeholders = implode(",", array_fill(0, count($ids), "?"));

  $sql = "SELECT product_ID, name, price, image
          FROM products
          WHERE product_ID IN ($placeholders)";

  $stmt = $conn->prepare($sql);
  $stmt->bind_param(str_repeat("i", count($ids)), ...$ids);
  $stmt->execute();
  $res = $stmt->get_result();

  while ($row = $res->fetch_assoc()) {
    $id = (int)$row["product_ID"];
    $qty = (int)($cart[$id] ?? 1);
    $price = (float)$row["price"];
    $line = $price * $qty;
    $subtotal += $line;

    $cartProducts[] = [
      "id" => $id,
      "name" => $row["name"],
      "price" => $price,
      "qty" => $qty,
      "line" => $line,
      "image" => $row["image"]
    ];
  }
  $stmt->close();
}
$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Basket | LOFT & LIVING</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/basket.css">

    <style>
        body {
            padding-top: 120px;
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

        .header-inner {
            max-width: 1400px;
            margin: 0 auto;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 40px;
        }

        .header-left-tools {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .logo-wrapper {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }

        .main-logo {
            height: 50px !important;
            width: auto !important;
            max-width: 280px;
            object-fit: contain;
            display: block;
            filter: invert(1);
            opacity: 0.95;
            transition: opacity 0.2s ease;
        }

        .ui-icon {
            width: 20px;
            height: 20px;
            object-fit: contain;
            display: block;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        html.dark-mode .site-header {
            background-color: #1a1a1a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        html.dark-mode .ui-icon {
            filter: invert(1);
        }

        html.dark-mode .main-logo {
            filter: invert(0);
        }

        html.dark-mode body {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        html.dark-mode .site-footer {
            background-color: #1a1a1a;
            border-top: 1px solid #333;
        }
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
            <a href="favourites.php">
                <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
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
<script src="../javascript/global/basketIcon.js"></script>

</body>
</html>