<?php include '../config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- '' This is the header and footer template for LOFT & LIVING '' -->
    <meta charset="UTF-8">
    <title>LOFT & LIVING BIRMINGHAM</title>
    <link rel="stylesheet" href="../css/header_footer_style.css">
    </head>
<body>

    <!-- '' Site Header: This section contains the logo, navigation menu, and user action icons '' -->
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
<!-- 
        '' Dropdown Navigation Menu: Here users can navigate to different product categories '' -->
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
    <main style="height: 600px; padding: 50px; text-align: center; background-color: #f9f9f9;">
        <h1></h1>
    </main>



<?php
session_start();
$target = '../config/db_connect.php';
$dir = __DIR__;
$found = null;


 ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?> 

        <!-- <nav class="dropdown-panel" id="dropdown-nav">
            <ul class="nav-links">
                <li><a href="livingroom.html">Living Room</a></li>
                <li><a href="bathroom.html">Bathroom</a></li>
                <li><a href="bedroom.html">Bedroom</a></li>
                <li><a href="office.html">Office</a></li>
                <li><a href="kitchen.html">Kitchen</a></li>
                <li class="nav-divider"><a href="signin.html">My Account</a></li>
            </ul>
        </nav>
    </header>
    <main style="height: 600px; padding: 50px; text-align: center; background-color: #f9f9f9;">
        <h1></h1>
    </main>
 -->


<!doctype html>
<html>
<head>
  <meta charset="utf-8" />
  <title>My Favourites</title>
  <style>
    body { font-family: Arial, sans-serif; margin: 0; background:#fff; color:#111; }
    .topline { height: 10px; border-bottom: 1px solid #d9d9d9; box-shadow: 0 1px 0 rgba(0,0,0,.04); }
    .wrap { max-width: 980px; margin: 0 auto; padding: 26px 18px 40px; }
    h1 { font-size: 18px; margin: 0 0 4px; font-weight: 700; }
    .sub { font-size: 12px; color: #666; margin-bottom: 18px; }

    .grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 22px; }

    .fav-card {
      position: relative;
      border: 1px solid #eaeaea;
      border-radius: 6px;
      padding: 16px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      background: #fff;
      min-height: 92px;
    }

    .thumb {
      width: 86px; height: 86px;
      border-radius: 4px;
      background: #e9e9e9;
      overflow: hidden;
      display:flex; align-items:center; justify-content:center;
      flex: 0 0 86px;
    }
    .thumb img { width:100%; height:100%; object-fit:cover; display:block; }

    .btn {
      padding: 7px 10px;
      border: none;
      border-radius: 6px;
      background: #111;
      color: #fff;
      font-size: 11px;
      cursor: pointer;
      white-space: nowrap;
    }

    .removeBtn {
      position:absolute;
      top: 8px; right: 8px;
      width: 26px; height: 26px;
      border-radius: 7px;
      border: 1px solid #e1e1e1;
      background:#fff;
      cursor:pointer;
      font-size: 16px;
      line-height: 1;
      display:flex; align-items:center; justify-content:center;
    }

    .empty { padding: 18px; border: 1px dashed #ddd; border-radius: 8px; color:#666; }
    .actions { margin-top: 14px; font-size: 12px; }
    .actions a { color:#111; text-decoration: none; border-bottom: 1px solid #ddd; }

    @media (max-width: 900px) { .grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 580px) { .grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="topline"></div>

  <div class="wrap">
    <h1>My Favourites</h1>
    <div class="sub">See an item you like? Come back to it later at any time</div>

    <?php if (empty($favs)): ?>
      <div class="empty">No favourites yet. Go to categories and like a product.</div>
      <div class="actions"><a href="homepage.php">Back to Homepage</a></div>
    <?php else: ?>
      <div class="grid">
        <?php foreach ($favs as $p): $pid = (int)$p["id"]; ?>
          <div class="fav-card">
            <form method="post" action="favourite_remove.php" style="margin:0;">
              <input type="hidden" name="product_id" value="<?= $pid ?>">
              <input type="hidden" name="redirect" value="favourites.php">
              <button class="removeBtn" type="submit" title="Remove">×</button>
            </form>

            <form method="post" action="favorite_add.php">
        <input type="hidden" name="product_id" value="<?= (int)$pid ?>">
       <input type="hidden" name="redirect" value="favourites.php">
       <button type="submit">♡</button>
       </form>

            <div class="thumb">
              <?php if (!empty($p["image"]) && file_exists($p["image"])): ?>
                <img src="<?= htmlspecialchars($p["image"]) ?>" alt="">
              <?php else: ?>
                <span style="color:#cfcfcf; font-size:12px;">IMG</span>
              <?php endif; ?>
            </div>

        


            <form method="post" action="bag_add.php" style="margin:0;">
              <input type="hidden" name="product_id" value="<?= $pid ?>">
              <button class="btn" type="submit">Add to bag</button>
            </form>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="actions" style="margin-top:18px;">
        <a href="homepage.php">Back to homepage</a>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>

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

            <!-- '' Categories Section: Links to different product categories '' -->
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
