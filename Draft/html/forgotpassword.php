<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | LOFT & LIVING</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
  <link rel="stylesheet" href="../css/signup.style.css">

  <style>
    html, body {
      width: 100%;
      margin: 0;
      padding: 0;
    }

    body.page-background {
      display: flex !important;
      flex-direction: column !important;
      justify-content: flex-start !important;
      align-items: stretch !important;
      min-height: 100vh !important;
      width: 100% !important;
      margin: 0 !important;
    }

    .site-header,
    .site-footer {
      width: 100% !important;
      max-width: none !important;
      flex-shrink: 0;
      align-self: stretch !important;
    }

    main.form-container {
      flex: 1;
      width: 100% !important;
      max-width: none !important;
      display: flex !important;
      justify-content: center !important;
      align-items: center !important;
      padding: 60px 16px;
      margin: 0 !important;
      box-sizing: border-box;
      align-self: stretch !important;
    }

    .helper-text {
      text-align: center;
      font-size: 0.95rem;
      margin-top: -6px;
      margin-bottom: 16px;
      opacity: 0.8;
    }

    .message-popup {
      display: none;
      margin-top: 12px;
      padding: 10px 12px;
      border-radius: 4px;
      background: #fff;
      border: 1px solid #f0b400;
      box-shadow: 0 2px 4px rgba(0,0,0,0.12);
      font-size: 0.9rem;
      line-height: 1.4;
      align-items: flex-start;
      gap: 8px;
    }

    .message-popup.success {
      border-color: #4caf50;
    }

    .message-icon {
      width: 18px;
      height: 18px;
      border-radius: 3px;
      background: #ffa000;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 12px;
      font-weight: 700;
      flex-shrink: 0;
    }

    .message-popup.success .message-icon {
      background: #4caf50;
    }

    .message-text {
      flex: 1;
    }

    .back-link-wrap {
      margin-top: 14px;
      text-align: center;
    }
  </style>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="page-background">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<main class="form-container">
  <section class="form-box">
    <h1 class="form-title">FORGOT PASSWORD</h1>
    <p class="helper-text">Enter your email and we will send you a reset link.</p>

    <form class="form-fields" id="forgotPasswordForm">
      <label class="input-group">
        <span class="input-label">Email</span>
        <input type="email" name="email" placeholder="you@example.com" required>
      </label>

      <div id="messagePopup" class="message-popup">
        <div class="message-icon" id="messageIcon">!</div>
        <div class="message-text" id="messageText"></div>
      </div>

      <button type="submit" class="main-button">Send Reset Link</button>

      <div class="back-link-wrap">
        <a href="signin.php">Back to Sign In</a>
      </div>
    </form>
  </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>

<script>
  const API_URL = "../backend/routes/userRoutes.php";
  const form = document.getElementById("forgotPasswordForm");

  function showMessage(message, isSuccess = false) {
    const popup = document.getElementById("messagePopup");
    const icon = document.getElementById("messageIcon");
    const text = document.getElementById("messageText");

    text.textContent = message;
    popup.classList.toggle("success", isSuccess);
    icon.textContent = isSuccess ? "✓" : "!";
    popup.style.display = "flex";
  }

  function hideMessage() {
    document.getElementById("messagePopup").style.display = "none";
  }

  async function readJsonSafely(res) {
    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text();

    if (ct.includes("application/json")) {
      try { return JSON.parse(raw); } catch (e) {}
    }

    return { success: false, message: raw || "Server error. Please try again." };
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hideMessage();

    const email = form.querySelector('input[name="email"]').value.trim();

    if (!email) {
      showMessage("Please enter your email address.");
      return;
    }

    try {
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "forgot_password",
          email
        })
      });

      const data = await readJsonSafely(res);

      if (data.success) {
        showMessage(data.message || "Reset link sent successfully.", true);
        form.reset();
        return;
      }

      showMessage(data.message || "Could not send reset link.");
    } catch (err) {
      console.error(err);
      showMessage("Server error. Please try again.");
    }
  });
</script>
</body>
</html>




