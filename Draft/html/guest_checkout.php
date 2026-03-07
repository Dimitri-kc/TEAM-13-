<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../backend/config/db_connect.php';

$isLoggedIn = !empty($_SESSION['user_ID']);
$userName   = $_SESSION['name'] ?? '';
$headerName = ($userName !== '') ? $userName : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Checkout | LOFT & LIVING</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/checkout.css" />

    <style>
        body { padding-top: 120px; }
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
        .header-left-tools { display: flex; align-items: center; gap: 25px; }
        .logo-wrapper { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
        .main-logo { height: 50px !important; width: auto !important; max-width: 280px; object-fit: contain; display: block; filter: invert(1); opacity: 0.95; }
        .ui-icon { width: 20px; height: 20px; object-fit: contain; display: block; }
        .header-actions { display: flex; align-items: center; gap: 25px; }
        html.dark-mode .site-header { background-color: #1a1a1a; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); }
        html.dark-mode .ui-icon { filter: invert(1); }
        html.dark-mode .main-logo { filter: invert(0); }
        html.dark-mode body { background-color: #1a1a1a; color: #e0e0e0; }
        html.dark-mode .site-footer { background-color: #1a1a1a; border-top: 1px solid #333; }
        #basket-count { display: none !important; }
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
    <header class="checkout-header">
        <h1>GUEST CHECKOUT</h1>
    </header>

    <main class="checkout-layout">

        <aside class="product-column">
            <div class="product-item">
                <img src="../images/basket-images/sofa.jpg" alt="Venice Cream Sofa"/>
                <div class="product-text">
                    <p>Venice Cream Sofa</p>
                    <p class="price">£295</p>
                    <p class="quantity">Quantity: 1</p>
                </div>
            </div>
        </aside>

        <section class="details-column">
            
     <section class="review-box">
 
</section>


                    <div class="card-fields">
                         <h2>Email</h2>
    <input type="email" placeholder="Email Address" required />
    <h2>Full Name</h2>
    <input type="text" placeholder="Full Name" required />
    <h2>Address (Using Google Lookup)</h2>
    <input type="text" placeholder="Address Line 1" required />


                        <h2>Card Details</h2>

                        <input type="text" placeholder="Card Number (16 Digits)" maxlength="16" required />
                        <input type="text" placeholder="Expiry Date (MM/YY)" required />
                        <input type="text" placeholder="CVV (3 Digits)" maxlength="3" required />
                        <button type="submit">Submit</button>

                        <div class="pay-buttons">
                            <img src="../images/basket-images/applepay.png" alt="Apple Pay" class="pay-btn">
                            <img src="../images/basket-images/googlepay.png" alt="Google Pay" class="pay-btn">
                        </div>
                    </div>

            <div class="delivery-section">
                <p>Ready for Loft & Living in Your Home?</p>
                <p>Your Order will be dispatched using Standard Delivery </p>
                <p>Estimated Delivery: 9th December 2025</p>

                <button class="checkout-btn" onclick="window.location.href='orderconfirmation.php'">Checkout</button>
            </div>

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