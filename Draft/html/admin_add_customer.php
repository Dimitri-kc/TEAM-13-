<?php include '../backend/config/db_connect.php'; 
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Customer | Loft & Living</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/header_footer_style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Inter", sans-serif;
            background: #d9d6cf;
            color: #111;
            min-height: 100vh;
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

        .page-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 24px 20px 40px;
        }

        .page-shell {
            width: 100%;
            max-width: 760px;
            background: #f7f7f5;
            padding: 24px 24px 28px;
            min-height: auto;
        }

        .top-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 6px;
            line-height: 1.1;
        }

        .page-sub {
            color: #7b7b7b;
            font-size: 12px;
            margin: 0;
            line-height: 1.2;
        }

        .back-btn {
            height: 32px;
            padding: 0 16px;
            border: 1px solid #9f9f9f;
            background: #fff;
            color: #111;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .back-btn:hover {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .message-popup {
            display: none;
            margin-bottom: 16px;
            padding: 10px 12px;
            border-radius: 4px;
            background-color: #ffffff;
            border: 1px solid #f0b400;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
            font-size: 0.9rem;
            line-height: 1.4;
            align-items: flex-start;
            gap: 8px;
        }

        .message-popup.success {
            border: 1px solid #2e9f5b;
        }

        .message-icon {
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

        .message-popup.success .message-icon {
            background-color: #2e9f5b;
        }

        .message-text {
            flex: 1;
        }

        .toast {
            position: fixed;
            bottom: 28px;
            right: 28px;
            background: #2f2f2f;
            color: #fff;
            font-size: 13px;
            font-weight: 500;
            padding: 10px 18px;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
            z-index: 9999;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }
        
        .form-card {
            background: #fbfbfb;
            border: 1px solid #e3e3e3;
            border-radius: 4px;
            padding: 20px;
            max-width: 640px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 18px;
        }

        .field-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .field-label {
            font-size: 12px;
            font-weight: 600;
            color: #222;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .field-input,
        .field-textarea {
            width: 100%;
            border: 1px solid #c8c8c8;
            background: #fff;
            font-size: 12px;
            color: #111;
            outline: none;
            border-radius: 0;
            font-family: "Inter", sans-serif;
        }

        .field-input {
            height: 42px;
            padding: 0 12px;
        }

        .field-textarea {
            min-height: 56px;
            padding: 12px;
            resize: vertical;
        }

        .action-row {
            display: flex;
            gap: 10px;
            margin-top: 22px;
            flex-wrap: wrap;
        }

        .primary-btn,
        .secondary-btn {
            height: 34px;
            padding: 0 18px;
            border: 1px solid #9f9f9f;
            background: #fff;
            color: #111;
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
        }

        .primary-btn:hover,
        .secondary-btn:hover {
            background: #000;
            color: #fff;
            border-color: #000;
        }

        .primary-btn:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .site-footer {
            width: 100%;
            background-color: #fff;
            margin-top: auto;
            padding: 24px 0 32px;
        }

        .footer-inner {
            width: 90%;
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 120px 1fr 1fr 1fr;
            gap: 26px;
            align-items: start;
        }

        .footer-section h4 {
            font-size: 13px;
            margin: 0 0 12px;
            font-weight: 600;
        }

        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-section ul li {
            margin-bottom: 9px;
            font-size: 12px;
        }

        .footer-section ul li a {
            text-decoration: none;
            color: #333;
        }

        .social-links {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding-top: 2px;
        }

        .social-icon {
            width: 18px;
            height: 18px;
            object-fit: contain;
        }

        @media (max-width: 900px) {
            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                padding: 20px 14px 22px;
            }

            .form-card {
                padding: 16px;
            }

            .footer-inner {
                grid-template-columns: 1fr;
            }
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
                    <img src="../images/header_footer_images/logo.png" alt="Loft & Living" class="main-logo">
                </a>
            </div>

            <div class="header-actions">
                <a href="basket.php">
                    <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
                </a>
                <a href="adminlogin.php">
                    <img src="../images/header_footer_images/icon-user.png" alt="Admin Login" class="ui-icon">
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
                <li class="nav-divider"><a href="adminlogin.php">Admin Login</a></li>
            </ul>
        </nav>
    </header>

    <main class="page-wrap">
        <section class="page-shell">
            <div class="top-row">
                <div>
                    <h1 class="page-title">Add Customer</h1>
                    <p class="page-sub">Create a new customer account from the customer management page</p>
                </div>
                <a href="admin_customer_management.php" class="back-btn">Back</a>
            </div>

            <div id="messagePopup" class="message-popup">
                <div class="message-icon">!</div>
                <div class="message-text" id="messageText"></div>
            </div>

            <div class="form-card">
                <form id="addCustomerForm">
                    <div class="form-grid">
                        <div class="field-group">
                            <label class="field-label" for="name">Name</label>
                            <input class="field-input" type="text" id="name" name="name" required>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="surname">Surname</label>
                            <input class="field-input" type="text" id="surname" name="surname" required>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="email">Email</label>
                            <input class="field-input" type="email" id="email" name="email" required>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="phone">Phone Number</label>
                            <input class="field-input" type="text" id="phone" name="phone">
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="address">Address</label>
                            <input class="field-input" type="text" id="address" name="address">
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="password">Password</label>
                            <input class="field-input" type="password" id="password" name="password" required>
                        </div>

                        <div class="field-group">
                            <label class="field-label" for="confirm_password">Confirm Password</label>
                            <input class="field-input" type="password" id="confirm_password" name="confirm_password" required>
                        </div>
                    </div>

                    <div class="action-row">
                        <button type="submit" class="primary-btn" id="submitBtn">Add Customer</button>
                        <a href="admin_customer_management.php" class="secondary-btn">Cancel</a>
                    </div>
                </form>
            </div>
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
    <div id="toast" class="toast">New user added</div>
    <script>
        const API_URL = "/TEAM-13-/Draft/backend/routes/adminRoutes.php";
        const form = document.getElementById("addCustomerForm");
        const submitBtn = document.getElementById("submitBtn");
        const messagePopup = document.getElementById("messagePopup");
        const messageText = document.getElementById("messageText");
        const passwordField = document.getElementById("password");

        function showMessage(message, success = false) {
            messageText.textContent = message;
            messagePopup.classList.toggle("success", success);
            messagePopup.style.display = "flex";
        }

        function hideMessage() {
            messagePopup.style.display = "none";
            messagePopup.classList.remove("success");
        }
        
        function showToast() {
            const toast = document.getElementById("toast");
            toast.classList.add("show");
            setTimeout(() => toast.classList.remove("show"), 1500);
        }

        async function readJsonSafely(response) {
            const contentType = (response.headers.get("content-type") || "").toLowerCase();
            const rawText = await response.text();

            if (contentType.includes("application/json")) {
                try {
                    return JSON.parse(rawText);
                } catch (error) {
                }
            }

            return {
                success: false,
                message: rawText || "Server error. Please try again."
            };
        }

        form.addEventListener("submit", async function (e) {
            e.preventDefault();
            hideMessage();

            const name = document.getElementById("name").value.trim();
            const surname = document.getElementById("surname").value.trim();
            const email = document.getElementById("email").value.trim();
            const phone = document.getElementById("phone").value.trim();
            const address = document.getElementById("address").value.trim();
            const password = passwordField.value;
            const confirmPassword = document.getElementById("confirm_password").value;

            if (!name || !surname || !email || !password || !confirmPassword) {
                showMessage("Please fill in all required fields.");
                return;
            }

            if (password !== confirmPassword) {
                showMessage("Passwords do not match.");
                return;
            }

            const hasMinLength = password.length >= 8;
            const hasSpecialChar = /[^A-Za-z0-9]/.test(password);

            if (!hasMinLength || !hasSpecialChar) {
                showMessage("Password must be at least 8 characters and include a special character.");
                passwordField.focus();
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = "Adding...";

            const payload = {
                action: "create_customer",
                role: "customer",
                name: name,
                surname: surname,
                email: email,
                phone: phone,
                address: address,
                password: password
            };

            try {
                const response = await fetch(API_URL, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await readJsonSafely(response);

                if (data.success) {
                    showToast();
                    form.reset();

                    setTimeout(function () {
                        window.location.href = "admin_customer_management.php";
                    }, 2000);
                } else {
                    showMessage(data.message || "Unable to create customer.");
                }
            } catch (error) {
                showMessage("Server error. Please try again.");
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = "Add Customer";
            }
        });
    </script>

    <script src="../javascript/header_footer_script.js"></script>
</body>
</html>