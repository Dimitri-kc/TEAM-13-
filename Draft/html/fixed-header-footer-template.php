<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Optional: Include database connection if needed for your page
// include '../backend/config/db_connect.php';

$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Title</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">

    <style>
        /* Fixed Header and Footer Styles */
        body {
            margin: 0;
            padding: 0;
            background-color: #F4F1EC;
            color: #2B2B2B;
            font-family: "Ibarra Real Nova", serif;
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

        .header-left-tools {
            display: flex;
            align-items: center;
            gap: 25px;
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

        .site-footer {
            position: relative;
            background: white;
            border-top: 1px solid #e0e0e0;
            margin-top: 60px;
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

        .main-logo:hover {
            opacity: 1;
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
        .profile-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            z-index: 2000;
        }

        .profile-btn {
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
        }

        .profile-dropdown {
            position: absolute;
            top: 40px;
            right: 0;
            width: 260px;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 18px;
            display: none;
            z-index: 3000;
            font-family: "Ibarra Real Nova", serif;
        }

        .profile-dropdown.open { display: block; }

        .profile-welcome {
            font-size: 14px;
            font-weight: 700;
            color: #2B2B2B;
            margin-bottom: 14px;
            font-family: "Ibarra Real Nova", serif !important;
        }

        .profile-link {
            display: block;
            font-size: 14px;
            color: #2B2B2B;
            padding: 10px 0;
            text-decoration: none;
            font-family: "Ibarra Real Nova", serif !important;
        }

        .profile-link + .profile-link {
            border-top: 1px solid #E5E1DB;
        }

        .profile-link-danger {
            color: #b00020;
        }

        /* Page content styles */
        .page-content {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 700;
            color: #2B2B2B;
            margin-bottom: 20px;
        }

        p {
            font-size: 16px;
            line-height: 1.6;
            color: #2B2B2B;
        }

        /* Dark mode overrides for fixed elements */
        html.dark-mode .site-header {
            background-color: #1a1a1a;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }

        html.dark-mode .site-footer {
            background-color: #1a1a1a;
            border-top: 1px solid #333;
        }
            background-color: #242424 !important;
            border-color: #333 !important;
        }

        html.dark-mode .profile-welcome {
            color: #e0e0e0 !important;
        }

        html.dark-mode .profile-link {
            color: #e0e0e0 !important;
        }

        html.dark-mode .profile-link + .profile-link {
            border-top: 1px solid #444 !important;
        }

        html.dark-mode body {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }

        html.dark-mode h1 {
            color: #e0e0e0;
        }

        html.dark-mode .ui-icon {
            filter: invert(1);
        }

        html.dark-mode .main-logo {
            filter: invert(0);
        }

        html.dark-mode .social-icon {
            filter: invert(1);
        }

        html.dark-mode .main-logo {
            filter: invert(0);
        }

        .social-icon {
            filter: invert(1) brightness(0.8) !important;
        }
    </style>
    <script src="../javascript/dark-mode.js"></script>
</head>

<body>

<header class="site-header">
    <div class="header-inner">

        <!-- LEFT: menu + dark mode + search -->
        <div class="header-left-tools">
            <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
            </button>
            <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
            <!-- Replace search pill with search icon -->
            <a class="mini-search" href="search.php" aria-label="Search" data-search-trigger="modal">
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
                    <?php
                        if (session_status() === PHP_SESSION_NONE) session_start();
                        $isLoggedIn = !empty($_SESSION['user_ID']);
                        $headerName = $_SESSION['name'] ?? 'Guest';
                    ?>

                    <?php if ($isLoggedIn): ?>
                        <div class="profile-welcome">Welcome, <?php echo htmlspecialchars($headerName); ?></div>
                    <?php else: ?>
                        <div class="profile-welcome">Welcome to Loft & Living</div>
                    <?php endif; ?>

                    <?php if (!$isLoggedIn): ?>
                        <a class="profile-link" href="signin.php">Sign in</a>
                        <a class="profile-link" href="signup.php">Sign Up</a>
                    <?php endif; ?>

                    <a class="profile-link" href="user_dash.php">My account</a>

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
            <li class="nav-divider"><a href="signin.php">My Account</a></li>
        </ul>
    </nav>
</header>

<!-- Main Content -->
<main class="page-content">
    <h1>Welcome to Your Page</h1>
    <p>This is a template page with a fixed header and footer based on the homepage design. The header stays visible as you scroll, and the footer remains at the bottom of the screen.</p>
    
    <p>You can replace this content with your actual page content. The header includes:</p>
    <ul style="font-size: 16px; line-height: 1.8;">
        <li>Menu button with dropdown navigation</li>
        <li>Dark mode toggle</li>
        <li>Search functionality</li>
        <li>Favourites link</li>
        <li>User profile dropdown</li>
        <li>Shopping basket with item count</li>
    </ul>

    <p>The footer includes navigation links and social media icons.</p>

    <p style="margin-top: 40px;">To use this template on other pages:</p>
    <ol style="font-size: 16px; line-height: 1.8;">
        <li>Copy this file and rename it for your new page</li>
        <li>Keep the header and footer sections intact</li>
        <li>Replace the content inside the &lt;main class="page-content"&gt; tags</li>
        <li>Update the page title in the &lt;title&gt; tag</li>
    </ol>

    <p style="margin-top: 60px; padding: 20px; background-color: #E5E1DB; border-radius: 8px;">
        <strong>Scroll down to see the fixed footer remain at the bottom of the screen.</strong>
    </p>

    <div style="height: 200px;"></div>
</main>

<!-- Footer -->
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
<script src="../javascript/global/search-modal.js?v=4"></script>

</body>
</html>
