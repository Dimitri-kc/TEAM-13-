<?php
// signin.php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard (no need to see sign in page)
if (!empty($_SESSION['user_ID'])) {
    header("Location: admin_dash.php");
    exit;
}

$isLoggedIn = !empty($_SESSION['user_ID']);
$headerName = $_SESSION['name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Sign In | LOFT &amp; LIVING BIRMINGHAM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
    <link rel="stylesheet" href="../css/signup.style.css?v=3">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="auth-page admin-signin-page">
    <?php $headerPartialOnly = true; include 'header.php'; ?>

    <main class="auth-wrap">
        <section class="auth-shell auth-shell--compact">
            <aside class="auth-hero">
                <div>
                    <div class="auth-kicker">Loft & Living Admin</div>
                    <h1 class="auth-hero-title">Admin access.</h1>
                    <p class="auth-hero-copy">Sign in to manage products, customers, orders, and reporting from the admin dashboard.</p>
                </div>

                <div class="auth-feature-list">
                    <div class="auth-feature">
                        <span class="auth-feature-label">Control</span>
                        <span class="auth-feature-value">Inventory, customers, orders and reporting</span>
                    </div>
                    <div class="auth-feature">
                        <span class="auth-feature-label">Access</span>
                        <span class="auth-feature-value">Restricted admin credentials only</span>
                    </div>
                </div>
            </aside>

            <div class="auth-panel">
                <div class="auth-heading">
                    <h2>Admin Sign In</h2>
                    <p>Access the admin dashboard, manage products, and review customer data.</p>
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
                            <a href="admin_signup.php">Create an account</a>
                            <a href="forgotpassword.php">Forgot password?</a>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

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
                action: "admin_login",
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
                    window.location.href = "admin_dash.php";
                    return;
                }

                showError(data.message || "Login failed. Please check your email and password.");
                signinBtn.disabled = false;

            } catch (error) {
                showError("Login failed. Please check your connection and try again.");
                signinBtn.disabled = false;
            }
        });

    </script>
</body>
</html>
