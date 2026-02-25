<?php
include '../backend/config/db_connect.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isLoggedIn = !empty($_SESSION['user_ID']);
$headerName = $_SESSION['name'] ?? 'Guest';
?>
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
                <?php if ($isLoggedIn): ?>
                    <a href="user_dash.php">
                        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                    </a>
                <?php else: ?>
                    <a href="signin.php">
                        <img src="../images/header_footer_images/icon-user.png" alt="Sign in" class="ui-icon">
                    </a>
                <?php endif; ?>
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
                <li class="nav-divider">
                    <a href="<?php echo $isLoggedIn ? 'user_dash.php' : 'signin.php'; ?>">My Account</a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li><a href="signout.php">Sign out</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>
    <main style="height: 600px; padding: 50px; text-align: center; background-color: #f9f9f9;">
        <h1></h1>
    </main>

<!-- '' Site Footer: This section contains social media links and additional navigation '' -->
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