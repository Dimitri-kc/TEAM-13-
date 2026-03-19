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
    <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <link rel="stylesheet" href="../css/admin_add_customer.css?v=1">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body class="admin-add-customer-page">
    <?php $headerPartialOnly = true; include 'header.php'; ?>

    <main class="page-wrap">
        <section class="page-shell">
            <div class="page-header">
                <div class="page-header-copy">
                    <h1 class="page-title">Add Customer</h1>
                    <p class="page-sub">Create a new customer account without leaving the admin flow, while keeping the same validation and backend logic already in place.</p>
                </div>
                <a href="admin_customer_management.php" class="back-btn">Back to Customer Management</a>
            </div>

            <div class="layout-grid">
                <section class="form-card">
                    <div id="messagePopup" class="message-popup">
                        <div class="message-icon">!</div>
                        <div class="message-text" id="messageText"></div>
                    </div>

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

                            <div class="field-group field-group--full">
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
                </section>

                <aside class="info-card">
                    <h2 class="info-title">Before You Create</h2>
                    <p class="info-copy">This creates a standard customer account. Password validation and account creation still use the existing admin route and checks.</p>

                    <div class="info-list">
                        <div class="info-item">
                            <span class="info-label">Role</span>
                            <span class="info-value">Customer</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">Password Rules</span>
                            <span class="info-value">8+ characters and one special character</span>
                        </div>

                        <div class="info-item">
                            <span class="info-label">After Creation</span>
                            <span class="info-value">Redirects back to customer management</span>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </main>

    <?php $footerPartialOnly = true; include 'footer.php'; ?>
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
</body>
</html>
