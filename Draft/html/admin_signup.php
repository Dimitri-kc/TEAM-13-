<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up | Loft & Living</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/header_footer_style.css">
    <link rel="stylesheet" href="../css/signup.style.css">

    <style>
        body {
            display: flex !important;
            flex-direction: column !important;
            min-height: 100vh;
            margin: 0;
            background-color: #EAE8E4;
        }

        .site-header {
            position: relative !important;
            width: 100%;
            background-color: #ffffff !important;
            z-index: 1000;
            padding: 15px 0;
        }

        .header-inner {
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-actions {
            display: flex !important;
            flex-direction: row !important;
            align-items: center !important;
            justify-content: flex-end !important;
            gap: 20px;
        }

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
</head>

<body class="page-background">
    <header class="site-header">
        <div class="header-inner">
            <button class="menu-btn" id="menu-toggle-btn">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
            </button>

            <div class="logo-wrapper">
                <a href="homepage.php">
                    <img src="../images/header_footer_images/logo.png" alt="Loft & Living" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="favourites.php"><img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon"></a>
                <a href="adminlogin.php"><img src="../images/header_footer_images/icon-user.png" alt="Admin Login" class="ui-icon"></a>
                <a href="basket.php"><img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon"></a>
            </div>
        </div>

        <nav class="dropdown-panel" id="dropdown-nav">
            <ul class="nav-links">
                <li><a href="livingroom.php">Living Room</a></li>
                <li><a href="bathroom.php">Bathroom</a></li>
                <li><a href="bedroom.php">Bedroom</a></li>
                <li><a href="office.php">Office</a></li>
                <li><a href="kitchen.php">Kitchen</a></li>
                <li class="nav-divider"><a href="adminlogin.php">Admin Login</a></li>
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
                    <a href="adminlogin.php" class="link-primary">Sign in</a>
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
                    <li><a href="adminlogin.php">Admin Login</a></li>
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
                    window.location.href = data.redirect || "adminlogin.php";
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


