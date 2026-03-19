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
    <link rel="stylesheet" href="../css/header_footer_style.css?v=15">

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
            display: flex;
            flex-direction: column;
        }

        .page-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            padding: 110px 20px 40px;
            flex: 1;
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

        @media (max-width: 640px) {
            .page-shell {
                padding: 20px 14px 22px;
            }

            .form-card {
                padding: 16px;
            }
        }
    </style>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>
<body>
    <?php $headerPartialOnly = true; include 'header.php'; ?>

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
