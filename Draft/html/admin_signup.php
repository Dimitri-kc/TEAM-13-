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
    <title>Admin Sign Up | Loft & Living</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/signup.style.css">

    <style>
        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh;
            margin: 0;
            background-color: #EAE8E4;
            padding-top: 120px !important;
        }

        .site-header {
            position: fixed !important;
            top: 20px !important;
            left: 40px !important;
            right: 40px !important;
            z-index: 1000 !important;
            background: white !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08) !important;
            border-radius: 50px !important;
            height: 80px !important;
        }

        .header-inner {
            max-width: 1400px !important;
            margin: 0 auto !important;
            height: 100% !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            padding: 0 40px !important;
        }

        .header-left-tools { display: flex !important; align-items: center !important; gap: 25px !important; }
        .logo-wrapper { position: absolute !important; left: 50% !important; top: 50% !important; transform: translate(-50%, -50%) !important; }
        .main-logo { height: 50px !important; width: auto !important; max-width: 280px !important; object-fit: contain !important; display: block !important; filter: invert(1) !important; opacity: 0.95 !important; }
        .ui-icon { width: 20px !important; height: 20px !important; object-fit: contain !important; display: block !important; }

        .header-actions {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 20px;
        }

        html.dark-mode .site-header { background-color: #1a1a1a !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important; }
        html.dark-mode .ui-icon { filter: invert(1) !important; }
        html.dark-mode .main-logo { filter: invert(0) !important; }
        html.dark-mode body.page-background { background-color: #1a1a1a !important; color: #e0e0e0 !important; }
        html.dark-mode .form-box { background-color: #242424 !important; color: #e0e0e0 !important; }
        html.dark-mode .input-group input { background-color: #1a1a1a !important; border-color: #444 !important; color: #e0e0e0 !important; }
        html.dark-mode .site-footer { background-color: #1a1a1a !important; border-top: 1px solid #333 !important; }

        #basket-count { display: none !important; }

        .header-actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .header-actions img {
            display: block;
        }

        main.form-container {
            margin-top: 50px !important;
            margin-bottom: 50px !important;
            flex: 1;
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .site-footer {
            margin-top: auto;
            width: 100%;
            background-color: #fff;
        }

        .input-group {
            margin-bottom: 25px !important;
            display: flex;
            flex-direction: column;
        }

        .input-label {
            margin-bottom: 8px;
            font-weight: 500;
        }

        .helper-text {
            text-align: center;
            font-size: 0.95rem;
            margin-top: -10px;
            margin-bottom: 18px;
            opacity: 0.85;
        }

        .error-popup {
            display: none;
            margin-top: 10px;
            padding: 10px 12px;
            border-radius: 4px;
            background-color: #ffffff;
            border: 1px solid #f0b400;
            box-shadow: 0 2px 4px rgba(0,0,0,0.12);
            font-size: 0.9rem;
            line-height: 1.4;
            align-items: flex-start;
            gap: 8px;
        }

        .error-icon {
            width: 18px;
            height: 18px;
            border-radius: 3px;
            background-color: #ffa000;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .error-text {
            flex: 1;
        }
    </style>
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="page-background">
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
                    <img src="../images/header_footer_images/logo1.png" alt="Loft & Living" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon"></a>

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

                <a href="basket.php" class="basket-icon"><img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon"><span id="basket-count">0</span></a>
            </div>
        </div>

        <nav class="dropdown-panel" id="dropdown-nav">
            <ul class="nav-links">
                <li><a href="livingroom.php">Living Room</a></li>
                <li><a href="bathroom.php">Bathroom</a></li>
                <li><a href="bedroom.php">Bedroom</a></li>
                <li><a href="office.php">Office</a></li>
                <li><a href="kitchen.php">Kitchen</a></li>
                <li class="nav-divider"><a href="admin_login.php">Admin Login</a></li>
            </ul>
        </nav>
    </header>

    <main class="form-container">
        <section class="form-box">
            <h1 class="form-title">Admin Sign Up</h1>
            <p class="helper-text">Create an admin account to access the admin dashboard</p>

            <form id="adminSignupForm">
                <div class="input-group">
                    <span class="input-label">Name</span>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="input-group">
                    <span class="input-label">Surname</span>
                    <input type="text" id="surname" name="surname" required>
                </div>

                <div class="input-group">
                    <span class="input-label">Email</span>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="input-group">
                    <span class="input-label">Phone Number</span>
                    <input type="tel" id="phone" name="phone" required>
                </div>

                <div class="input-group">
                    <span class="input-label">Password</span>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="input-group">
                    <span class="input-label">Confirm Password</span>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>

                <div id="errorPopup" class="error-popup">
                    <div class="error-icon">!</div>
                    <div class="error-text" id="errorText"></div>
                </div>

                <button type="submit" class="main-button">Create Admin Account</button>

                <p class="form-footer">
                    Already have an admin account?
                    <a href="admin_login.php" class="link-primary">Sign in</a>
                </p>
            </form>
        </section>
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
                    <li><a href="admin_login.php">Admin Login</a></li>
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

    <script>
        const API_URL = "/Team-13-/Draft/backend/routes/userRoutes.php";
        const form = document.getElementById("adminSignupForm");
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function showPopup(message) {
            const popup = document.getElementById("errorPopup");
            const text = document.getElementById("errorText");
            text.textContent = message;
            popup.style.display = "flex";
        }

        function hidePopup() {
            document.getElementById("errorPopup").style.display = "none";
        }

        async function readJsonSafely(res) {
            const ct = (res.headers.get("content-type") || "").toLowerCase();
            const raw = await res.text();
            if (ct.includes("application/json")) {
                try {
                    return JSON.parse(raw);
                } catch {}
            }
            return { success: false, message: raw || "Server error. Please try again." };
        }

        form.addEventListener("submit", async function (e) {
            e.preventDefault();
            hidePopup();

            const nameField = document.getElementById("name");
            const surnameField = document.getElementById("surname");
            const emailField = document.getElementById("email");
            const phoneField = document.getElementById("phone");
            const passwordField = document.getElementById("password");
            const confirmPasswordField = document.getElementById("confirmPassword");

            const name = nameField.value.trim();
            const surname = surnameField.value.trim();
            const email = emailField.value.trim();
            const phone = phoneField.value.trim();
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;

            if (!name) { showPopup("Please enter your name."); nameField.focus(); return; }
            if (!surname) { showPopup("Please enter your surname."); surnameField.focus(); return; }
            if (!email || !emailPattern.test(email)) { showPopup("Please enter a valid email address."); emailField.focus(); return; }
            if (!phone) { showPopup("Please enter your phone number."); phoneField.focus(); return; }
            if (!password) { showPopup("Please enter a password."); passwordField.focus(); return; }
            if (!confirmPassword) { showPopup("Please confirm your password."); confirmPasswordField.focus(); return; }
            if (password !== confirmPassword) { showPopup("Passwords do not match."); confirmPasswordField.focus(); return; }

            const hasMinLength = password.length >= 8;
            const hasSpecialChar = /[^A-Za-z0-9]/.test(password);

            if (!hasMinLength || !hasSpecialChar) {
                showPopup("Password must be at least 8 characters and include a special character.");
                passwordField.focus();
                return;
            }

            const payload = {
                action: "register_admin",
                name,
                surname,
                email,
                phone,
                password
            };

            try {
                const res = await fetch(API_URL, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload)
                });

                const data = await readJsonSafely(res);

                if (data.success) {
                    sessionStorage.setItem("adminSignupSuccessMessage", "Admin account created successfully. Please sign in.");
                    window.location.href = data.redirect || "admin_login.php";
                    return;
                }

                showPopup(data.message || "Admin registration failed. Please try again.");
            } catch (err) {
                console.error(err);
                showPopup("Server error. Please try again.");
            }
        });
    </script>

    <script src="../javascript/header_footer_script.js"></script>
</body>
</html>


