<?php 
include '../backend/config/db_connect.php'; 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require login
if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_ID'];

$favs = [];
$dbError = null;

// Load favourites from database
$sql = "SELECT p.product_ID, p.name, p.price, p.image
        FROM favourites f
        JOIN products p ON f.product_ID = p.product_ID
        WHERE f.user_ID = ?";

try {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Failed to prepare favourites query: ' . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res instanceof mysqli_result) {
        while ($row = $res->fetch_assoc()) {
            $favs[] = $row;
        }
    } else {
        $stmt->bind_result($product_ID, $name, $price, $image);
        while ($stmt->fetch()) {
            $favs[] = [
                'product_ID' => $product_ID,
                'name' => $name,
                'price' => $price,
                'image' => $image,
            ];
        }
    }

    $stmt->close();
} catch (Throwable $e) {
    // If the favourites table doesn't exist (or any SQL error), don't crash the page.
    $dbError = $e->getMessage();
    $favs = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favourites | LOFT & LIVING</title>
    <link rel="stylesheet" href="../css/header_footer_style.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background:#fff; color:#111; }
        .wrap { max-width: 980px; margin: 0 auto; padding: 26px 18px 40px; }
        h1 { font-size: 18px; margin: 0 0 4px; font-weight: 700; }
        .sub { font-size: 12px; color: #666; margin-bottom: 18px; }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        .fav-card {
          position: relative;
          border: 1px solid #eaeaea;
          border-radius: 6px;
          overflow: hidden;
          background: #ffffff;
          display: flex;
          flex-direction: column;
          text-decoration: none;
          color: inherit;
          transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .fav-card:hover {
          transform: translateY(-2px);
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .fav-card-link {
          display: flex;
          flex-direction: column;
          flex: 1;
          text-decoration: none;
          color: inherit;
        }

        .thumb {
          width: 100%; 
          height: 300px;
          border-radius: 0;
          background: #e9e9e9;
          overflow: hidden;
          display: flex; 
          align-items: center; 
          justify-content: center;
        }
        .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }

        .fav-card-content {
          padding: 16px;
          display: flex;
          flex-direction: column;
          justify-content: space-between;
          flex: 1;
        }

        .fav-card-title {
          font-size: 15px;
          font-weight: 500;
          color: #2B2B2B;
          margin: 0 0 8px 0;
          line-height: 1.4;
        }

        .fav-card-price {
          font-size: 18px;
          font-weight: 600;
          color: #2B2B2B;
          margin: 0;
        }

        .fav-card-actions {
          display: flex;
          gap: 8px;
          padding: 0 16px 16px 16px;
        }

        .btn {
          padding: 10px 14px;
          border: none;
          border-radius: 6px;
          background: #111;
          color: #fff;
          font-size: 12px;
          white-space: nowrap;
          margin: 0;
          flex: 1;
          cursor: pointer;
          font-weight: 500;
        }

        .btn:hover {
          background: #333;
        }

        .removeBtn {
          position: absolute;
          top: 12px;
          right: 12px;
          width: 32px;
          height: 32px;
          border-radius: 8px;
          border: 1px solid #ddd;
          background: #fff;
          cursor: pointer;
          font-size: 18px;
          line-height: 1;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        }

        .removeBtn:hover {
          background: #f5f5f5;
          border-color: #999;
        }

        .empty { padding: 18px; border: 1px dashed #ddd; border-radius: 8px; color:#666; }
        .actions { margin-top: 14px; font-size: 12px; }
        .actions a { color:#111; text-decoration: none; border-bottom: 1px solid #ddd; }

        @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <button class="menu-btn" id="menu-toggle-btn">
            <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon">
        </button>

        <div class="logo-wrapper">
            <a href="homepage.php">
                <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo">
            </a>
        </div>

        <div class="header-actions">
            <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon"></a>
            <a href="signin.php"><img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon"></a>
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

<main style="padding: 50px; min-height: 600px;">
    <?php $showBasketToast = isset($_GET['basket_added']) && $_GET['basket_added'] === '1'; ?>
    <?php if ($showBasketToast): ?>
        <div id="fav-basket-toast" style="position: fixed; top: 110px; right: 24px; background: rgba(33,33,33,0.95); color: #fff; padding: 10px 14px; border-radius: 10px; font-size: 13px; z-index: 5000; box-shadow: 0 6px 16px rgba(0,0,0,0.2);">
            Item added to basket
        </div>
    <?php endif; ?>

    <div class="wrap">
        <h1>My Favourites</h1>
        <div class="sub">See an item you like? Come back to it later at any time</div>

        <?php if (empty($favs)): ?>
            <div class="empty">No favourites yet. Go to categories and like a product.</div>
            <div class="actions"><a href="homepage.php">Back to Homepage</a></div>
        <?php else: ?>

            <div class="grid">
                <?php foreach ($favs as $p): ?>
                    <?php 
                        $pid = (int)$p["product_ID"]; 
                    ?>
                    
                    <div class="fav-card">
                        <a href="product.php?id=<?= $pid ?>" class="fav-card-link">
                            <div class="thumb">
                                <?php if (!empty($p["image"])): ?>
                                    <img src="../images/<?= htmlspecialchars($p["image"]) ?>" alt="<?= htmlspecialchars($p["name"]) ?>">
                                <?php else: ?>
                                    <span style="color:#cfcfcf; font-size:12px;">IMG</span>
                                <?php endif; ?>
                            </div>
                            <div class="fav-card-content">
                                <h3 class="fav-card-title"><?= htmlspecialchars($p["name"]) ?></h3>
                                <p class="fav-card-price">£<?= htmlspecialchars($p["price"]) ?></p>
                            </div>
                        </a>

                        <div class="fav-card-actions">
                            <form method="post" action="basket.php?action=add" style="margin:0; flex: 1;">
                                <input type="hidden" name="product_id" value="<?= $pid ?>">
                                <input type="hidden" name="qty" value="1">
                                <input type="hidden" name="redirect" value="favourites.php?basket_added=1">
                                <button class="btn" type="submit">Add to bag</button>
                            </form>
                            <form method="post" action="favourite_remove.php" style="margin:0;">
                                <input type="hidden" name="product_id" value="<?= $pid ?>">
                                <input type="hidden" name="redirect" value="favourites.php">
                                <button class="removeBtn" type="submit" title="Remove from Favourites">×</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="actions" style="margin-top:18px;">
                <a href="homepage.php">Back to homepage</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<footer class="site-footer">
    <div class="footer-inner">
        <div class="footer-section social-links">
            <a href="#"><img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon"></a>
            <a href="#"><img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon"></a>
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
                <li><a href="office.php">Office</a></li>
                <li><a href="kitchen.php">Kitchen</a></li>
                <li><a href="bathroom.php">Bathroom</a></li>
                <li><a href="bedroom.php">Bedroom</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h4>More...</h4>
            <ul>
<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const toast = document.getElementById('fav-basket-toast');
    if (toast) {
        setTimeout(function () {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-8px)';
            toast.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            setTimeout(function () { toast.remove(); }, 260);
        }, 1700);

        if (window.history && window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('basket_added');
            window.history.replaceState({}, document.title, url.pathname + (url.search ? url.search : ''));
        }
    }
});
</script>
</body>
</html> </div>
    </div>
</footer>

<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>
</body>
</html>