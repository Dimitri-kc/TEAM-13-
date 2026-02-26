<?php
// user_dash.php
// User dashboard page - shows account shortcuts once a user is logged in

// Start session to access logged-in user information (set during login)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If the user isn't logged in, redirect them to sign in
if (empty($_SESSION['user_ID'])) {
    header("Location: admin_login.php");
    exit;
}

// Pull user details from session (already set in UserController login method)
$userName = $_SESSION['name'] ?? 'User';
$userRole = $_SESSION['role'] ?? 'customer';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account | LOFT &amp; LIVING BIRMINGHAM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Main site header/footer styling -->
    <link rel="stylesheet" href="../css/header_footer_style.css">

    <style>
        /* ===============================
           Dashboard page specific styling
           =============================== */

        .dashboard-wrap {
            background: #ffffff;
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


        .user-icon-wrap {
            position: relative;
            display: inline-flex;
            align-items: center;
        }

        .user-icon-btn {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
            display: inline-flex;
            align-items: center;
        }

        .user-dropdown {
            position: absolute;
            top: 40px;
            right: 0;
            width: 260px;
            background: #fff;
            border: 1px solid #e0e0e0;
            padding: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: none;
            z-index: 3000; /* keeps it above other header elements */
        }

        .user-dropdown.open {
            display: block;
        }

        .user-dropdown-greeting {
            font-size: 14px;
            font-weight: 700;
            color: #000;
            margin-bottom: 14px;
        }

        .user-dropdown-btn {
            display: block;
            font-size: 14px;
            color: #444;
            padding: 10px 0;
        }

        .user-dropdown-btn + .user-dropdown-btn {
            border-top: 1px solid #eee;
        }

        .user-dropdown-btn.signout {
            color: #b00020;
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
                    <img src="../images/header_footer_images/logo.png" alt="LOFT &amp; LIVING" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.php">
                    <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
                </a>

                <!-- Profile icon dropdown (FIXED) -->
                <div class="user-icon-wrap" id="user-icon-wrap">
                    <button class="user-icon-btn" id="user-icon-btn" aria-label="My Account" type="button">
                        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
                    </button>

                    <div class="user-dropdown" id="user-dropdown">
                        <div class="user-dropdown-greeting">
                            Welcome, <?php echo htmlspecialchars($userName); ?>
                        </div>

                        
                        <a href="signin.php" class="user-dropdown-btn">Sign in</a>
                        <a class="profile-link" href="signup.php">Sign Up</a>
                        <a href="user_dash.php" class="user-dropdown-btn">My account</a>

                        <!-- Sign out should go to signout.php (destroys session) -->
                        <a href="signout.php" class="user-dropdown-btn signout">Sign out</a>
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
                <li class="nav-divider"><a href="user_dash.php">My Account</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard-wrap">
        <div class="dashboard-container">

            <div class="dashboard-heading">
                <h2>Welcome to the Admin Dashboard, <?php echo htmlspecialchars($userName); ?></h2>
                <p>My Account</p>
            </div>

            <div class="dash-grid">
                <a class="dash-card span-2" href="favourites.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash1.png" alt="My Favourites">
                            </div>
                            <div>
                                <h3>Orders & Shipments</h3>
                                <p>View and manage all orders and shipments</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-2" href="orders.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash2.png" alt="My Recent Orders">
                            </div>
                            <div>
                                <h3>Real-Time Reports</h3>
                                <p>View real-time reports and analytics</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-2" href="admin_homepage.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash3.png" alt="My Addresses">
                            </div>
                            <div>
                                <h3>View as Customer Mode</h3>
                                <p>View the website as a customer would see it and access all customer features</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-3" href="account_settings.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash4.png" alt="My Account Settings">
                            </div>
                            <div>
                                <h3>Customer Management</h3>
                                <p>Manage customer accounts and user permissions</p>
                            </div>
                        </div>
                    </div>
                </a>

                <a class="dash-card span-3" href="privacy.php">
                    <div class="dash-card-inner">
                        <div class="card-top">
                            <div class="card-media">
                                <img src="../images/dashboard/admin_dash5.png" alt="Privacy">
                            </div>
                            <div>
                                <h3>Product Inventory</h3>
                                <p>Manage product inventory and stock levels</p>
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
            const userBtn = document.getElementById('user-icon-btn');
            const userDropdown = document.getElementById('user-dropdown');
            const userWrap = document.getElementById('user-icon-wrap');

            if (!userBtn || !userDropdown || !userWrap) return;

            userBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (!userWrap.contains(e.target)) {
                    userDropdown.classList.remove('open');
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    userDropdown.classList.remove('open');
                }
            });
        });
    </script>

</body>
</html>