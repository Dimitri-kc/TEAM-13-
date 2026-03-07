<?php
// user_dash.php
// User dashboard page - shows account shortcuts once a user is logged in

// Start session to access logged-in user information (set during login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user isn't logged in, redirect them to sign in
if (empty($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit;
}

// Pull user details from session (already set in UserController login method)
$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'customer';
$isLoggedIn = !empty($_SESSION['user_ID']);
$headerName = ($userName !== '') ? $userName : 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account | LOFT &amp; LIVING</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">

    <style>
        body { 
            padding-top: 120px;
            font-family: "Ibarra Real Nova", serif;
            background-color: #F4F1EC;
            color: #2B2B2B;
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
        html.dark-mode .site-header { background-color: #1a1a1a; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3); }
        html.dark-mode .ui-icon { filter: invert(1); }
        html.dark-mode .main-logo { filter: invert(0); }
        html.dark-mode body { background-color: #1a1a1a; color: #e0e0e0; }
        html.dark-mode .site-footer { background-color: #1a1a1a; border-top: 1px solid #333; }
        html.dark-mode .dash-card { background-color: #242424; border-color: #444; }
        html.dark-mode .dash-card h3 { color: #e0e0e0; }
        html.dark-mode .dashboard-wrap { background: #1a1a1a; }

        /* =============================== Dashboard page specific styling =============================== */

        .dashboard-wrap {
            background: #F4F1EC;
            padding: 60px 24px 80px;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .dashboard-heading {
            margin-bottom: 26px;
        }

        .dashboard-heading h2 {
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 6px 0;
        }

        .dashboard-heading p {
            margin: 0;
            color: #777;
            font-size: 14px;
        }

        /* Grid layout for dashboard cards */
        .dash-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 22px;
        }

        .dash-card {
            border: 1px solid #e9e9e9;
            border-radius: 10px;
            background: #fff;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
            transition: transform 120ms ease, box-shadow 120ms ease;
        }

        .dash-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.06);
        }

        .dash-card-inner {
            padding: 18px;
        }

        .card-top {
            display: grid;
            grid-template-columns: 92px 1fr;
            gap: 16px;
            align-items: center;
        }

        /* Dashboard images (dash1 - dash5) */
        .card-media img {
            width: 92px;
            height: 92px;
            object-fit: cover;
            border-radius: 8px;
            display: block;
        }

        .dash-card h3 {
            margin: 6px 0 6px 0;
            font-size: 16px;
            font-weight: 700;
        }

        .dash-card p {
            margin: 0;
            font-size: 13px;
            color: #777;
            line-height: 1.35;
            max-width: 320px;
        }

        .span-2 { grid-column: span 2; }
        .span-3 { grid-column: span 3; }

        @media (max-width: 980px) {
            .dash-grid { grid-template-columns: repeat(2, 1fr); }
            .span-2, .span-3 { grid-column: span 1; }
        }

        @media (max-width: 560px) {
            .dash-grid { grid-template-columns: 1fr; }
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
                        <div class="profile-welcome">Welcome, <?php echo htmlspecialchars($headerName); ?></div>
                        <a class="profile-link" href="user_dash.php">My Account</a>
                        <a class="profile-link" href="user_order_history.php">My Orders</a>
                        <a class="profile-link" href="signout.php">Sign out</a>
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

    <main class="dashboard-wrap">
        <div class="dashboard-container">

            <div class="dashboard-heading">
                <h2>Welcome, <?php echo htmlspecialchars($userName); ?></h2>
                <p>My Account</p>
            </div>

            <div class="dash-grid">
                <a class="dash-card span-2" href="favourites.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash1.png" alt="My Favourites">
                            </div>
                            <div>
                                <h3>My Favourites</h3>
                                <p>See an item you like? Come back to it later at any time</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-2" href="user_order_history.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash2.png" alt="My Recent Orders">
                            </div>
                            <div>
                                <h3>My Recent Orders</h3>
                                <p>Take a look at previous orders you've made</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-2" href="addresses.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash3.png" alt="My Addresses">
                            </div>
                            <div>
                                <h3>My Addresses</h3>
                                <p>View any saved addresses and make any changes</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-3" href="account_settings.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash4.png" alt="My Account Settings">
                            </div>
                            <div>
                                <h3>My Account Settings</h3>
                                <p>Make any changes to your account name, email address or password</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-3" href="privacy.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/dash5.png" alt="Privacy">
                            </div>
                            <div>
                                <h3>Privacy</h3>
                                <p>Make any changes to your privacy settings here</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        </div>
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
                    <li><a href="user_dash.php">My Account</a></li>
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
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const profileToggleBtn = document.getElementById('user-icon-btn');
            const profileDropdown = document.getElementById('user-dropdown');
            const profileWrapper = document.getElementById('user-icon-wrap');

            if (!profileToggleBtn || !profileDropdown || !profileWrapper) return;

            profileToggleBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!profileWrapper.contains(e.target)) {
                    profileDropdown.classList.remove('open');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    profileDropdown.classList.remove('open');
                }
            });
        });
    </script>

</body>
</html>