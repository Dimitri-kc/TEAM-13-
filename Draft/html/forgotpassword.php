<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Forgot Password | LOFT & LIVING</title>

  <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
  <link rel="stylesheet" href="../css/signup.style.css?v=3">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="auth-page forgot-password-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<main class="form-container">
  <section class="auth-shell auth-shell--compact">
    <aside class="auth-hero">
      <div>
        <div class="auth-kicker">Loft & Living</div>
        <h1 class="auth-hero-title">Reset your password.</h1>
        <p class="auth-hero-copy">Enter your email address and we’ll send you a secure link so you can set a new password.</p>
      </div>

      <div class="auth-feature-list">
        <div class="auth-feature">
          <span class="auth-feature-label">Security</span>
          <span class="auth-feature-value">Password reset link sent to your email</span>
        </div>
        <div class="auth-feature">
          <span class="auth-feature-label">Access</span>
          <span class="auth-feature-value">Return to your account once your password is updated</span>
        </div>
      </div>
    </aside>

    <div class="auth-panel">
      <div class="auth-panel-header">
        <h1 class="form-title">Forgot Password</h1>
        <p class="helper-text">Enter your email and we will send you a reset link.</p>
      </div>

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

        <p class="form-footer">
          <a href="signin.php" class="link-primary">Back to Sign In</a>
        </p>
      </form>
    </div>
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



