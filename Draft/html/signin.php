<?php
// signin.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard (no need to see sign in page)
if (!empty($_SESSION['user_ID'])) {
    header("Location: user_dash.php");
    exit;
}

$isLoggedIn = !empty($_SESSION['user_ID']);
$headerName = $_SESSION['name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign In | LOFT &amp; LIVING BIRMINGHAM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- global styles + header/footer -->
    <link rel="stylesheet" href="../css/header_footer_style.css?v=14">

    <style>

        .auth-wrap {
            background: #ffffff;
            padding: 60px 24px 80px;
        }

        .auth-container {
            max-width: 520px;
            margin: 0 auto;
        }

        .auth-heading h2 {
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .auth-heading p {
            color: #777;
            font-size: 14px;
            margin-bottom: 18px;
        }

        .auth-card {
            border: 1px solid #e9e9e9;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
        }

        .form-group {
            margin-bottom: 14px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 12px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            font-size: 14px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #cfcfcf;
        }

        .auth-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 6px;
        }

        .btn-primary {
            border: none;
            background: #000;
            color: #fff;
            padding: 12px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 700;
            width: 100%;
        }

        .btn-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .auth-links {
            margin-top: 14px;
            font-size: 13px;
            color: #555;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
        }

        .auth-links a {
            color: #333;
            text-decoration: underline;
        }

        /* Popup styling (for logout success + login errors) */

        .popup {
            display: none;
            margin-bottom: 14px;
            padding: 10px 12px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e0e0e0;
            box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            font-size: 13px;
            line-height: 1.4;
            gap: 10px;
            align-items: flex-start;
        }

        .popup-icon {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 700;
            flex-shrink: 0;
            color: #fff;
        }

        .popup-text {
            color: #111;
        }

        .popup-success {
            border-color: #22c55e;
        }
        .popup-success .popup-icon {
            background: #22c55e;
        }

        .popup-error {
            border-color: #ef4444;
        }
        .popup-error .popup-icon {
            background: #ef4444;
        }

        .profile-wrapper {
            position: relative;
            display: inline-flex;
            align-items: center;
            z-index: 2000;
        }

        .profile-btn {
            background: none;
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
            border: 1px solid #e0e0e0;
            padding: 18px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            display: none;
            z-index: 3000;
        }

        .profile-dropdown.open {
            display: block;
        }

        .profile-welcome {
            font-size: 14px;
            font-weight: 700;
            color: #000;
            margin-bottom: 14px;
        }

        .profile-link {
            display: block;
            font-size: 14px;
            color: #444;
            padding: 10px 0;
        }

        .profile-link + .profile-link {
            border-top: 1px solid #eee;
        }

        .profile-link-danger {
            color: #b00020;
        }
    </style>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body>

    <!-- ===============================
         Header
         =============================== -->

    <?php $headerPartialOnly = true; include 'header.php'; ?>

    <!-- ===============================
         Main
         =============================== -->

    <main class="auth-wrap">
        <div class="auth-container">
            <div class="auth-heading">
                <h2>Sign in</h2>
                <p>Access your account, orders and saved items.</p>
            </div>

            <div id="successPopup" class="popup popup-success">
                <div class="popup-icon">✓</div>
                <div class="popup-text" id="successText"></div>
            </div>

            <div id="errorPopup" class="popup popup-error">
                <div class="popup-icon">!</div>
                <div class="popup-text" id="errorText"></div>
            </div>

            <div class="auth-card">
                <form id="signinForm" autocomplete="on">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" placeholder="Enter your password" required>
                    </div>

                    <div class="auth-actions">
                        <button class="btn-primary" id="signinBtn" type="submit">Sign in</button>
                    </div>

                    <div class="auth-links">
                        <a href="signup.php">Create an account</a>
                        <a href="forgotpassword.php">Forgot password?</a>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- ===============================
         Footer
         =============================== -->

    <?php $footerPartialOnly = true; include 'footer.php'; ?>

    <script>
        
        // Endpoint for login - matches userRoutes.php

        const LOGIN_ENDPOINT = "../backend/routes/userRoutes.php";

        const form = document.getElementById("signinForm");
        const signinBtn = document.getElementById("signinBtn");

        const errorPopup = document.getElementById("errorPopup");
        const errorText = document.getElementById("errorText");

        const successPopup = document.getElementById("successPopup");
        const successText = document.getElementById("successText");

        function showError(message) {
            errorText.textContent = message;
            errorPopup.style.display = "flex";
        }

        function hideError() {
            errorPopup.style.display = "none";
        }

        function showSuccess(message) {
            successText.textContent = message;
            successPopup.style.display = "flex";
        }

        function hideSuccess() {
            successPopup.style.display = "none";
        }

        // Show "Successfully signed out" if redirected from logout.php

        (function handleLogoutMessage() {
            const params = new URLSearchParams(window.location.search);
            if (params.get("logout") === "1") {
                hideError();
                showSuccess("Successfully signed out.");
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        })();

        // Handle login submit
        form.addEventListener("submit", async (e) => {
            e.preventDefault();
            hideError();
            hideSuccess();

            signinBtn.disabled = true;

            // Gather form data
            const payload = {
                action: "login",
                email: document.getElementById("email").value.trim(),
                password: document.getElementById("password").value.trim()
            };

            try {
                const response = await fetch(LOGIN_ENDPOINT, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(payload),
                    credentials: "same-origin"
                });

                const text = await response.text();
                let data;

                try {
                    data = JSON.parse(text);
                } catch (err) {
                    showError("Login failed. Server returned an unexpected response.");
                    signinBtn.disabled = false;
                    return;
                }

                if (data.success) {
                    // If backend sends a redirect URL (e.g. to change password), go there instead of dashboard
                    if (data.redirect && data.redirect.toLowerCase().includes("changepassword.php")) {
                        window.location.href = data.redirect;
                        return;
                    }

                    // Always go to dashboard after login
                   window.history.back();
                    return;
                }

                showError(data.message || "Login failed. Please check your email and password.");
                signinBtn.disabled = false;

            } catch (error) {
                showError("Login failed. Please check your connection and try again.");
                signinBtn.disabled = false;
            }
        });

        // Profile dropdown handling
        document.addEventListener("DOMContentLoaded", () => {
            const profileToggleBtn = dozzcument.getElementById("profile-toggle-btn");
            const profileDropdown = document.getElementById("profile-dropdown");
            const profileWrapper = document.getElementById("profile-wrapper");

            if (!profileToggleBtn || !profileDropdown || !profileWrapper) return;

            profileToggleBtn.addEventListener("click", (e) => {
                e.stopPropagation();
                profileDropdown.classList.toggle("open");
            });

            document.addEventListener("click", (e) => {
                if (!profileWrapper.contains(e.target)) {
                    profileDropdown.classList.remove("open");
                }
            });

            document.addEventListener("keydown", (e) => {
                if (e.key === "Escape") {
                    profileDropdown.classList.remove("open");
                }
            });
        });
    </script>
</body>
</html>