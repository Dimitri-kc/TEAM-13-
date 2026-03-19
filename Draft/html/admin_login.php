<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login | LOFT & LIVING</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/header_footer_style.css?v=16">
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

    .small-links {
      margin-top: 14px;
      display: flex;
      justify-content: center;
      gap: 18px;
      font-size: 0.95rem;
      opacity: 0.85;
      flex-wrap: wrap;
    }

    .error-popup {
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

    .error-icon {
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

    .error-text {
      flex: 1;
    }
  </style>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="page-background">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<main class="form-container">
  <section class="form-box">
    <h1 class="form-title">ADMIN LOGIN</h1>
    <p class="helper-text">Sign in with your admin credentials to access the dashboard.</p>

    <form class="form-fields" id="adminLoginForm" autocomplete="on">
      <label class="input-group">
        <span class="input-label">Email</span>
        <input type="email" name="email" required />
      </label>

      <label class="input-group">
        <span class="input-label">Password</span>
        <input type="password" name="password" required />
      </label>

      <div id="errorPopup" class="error-popup">
        <div class="error-icon">!</div>
        <div class="error-text" id="errorText"></div>
      </div>

      <button type="submit" class="main-button">Login</button>

      <div class="small-links">
        <a href="signin.php">Customer login</a>
        <a href="forgotpassword.php">Forgot password</a>
      </div>
    </form>
  </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>

<script>
  const API_URL = "/TEAM-13-/Draft/backend/routes/userRoutes.php";
  const form = document.getElementById("adminLoginForm");

  function showPopup(message) {
    document.getElementById("errorText").textContent = message;
    document.getElementById("errorPopup").style.display = "flex";
  }

  function hidePopup() {
    document.getElementById("errorPopup").style.display = "none";
  }

  async function readJsonSafely(res) {
    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text();
    if (ct.includes("application/json")) {
      try { return JSON.parse(raw); } catch {}
    }
    return { success: false, message: raw || "Server error. Please try again." };
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hidePopup();

    const email = form.querySelector('input[name="email"]').value.trim();
    const password = form.querySelector('input[name="password"]').value;

    if (!email || !password) {
      showPopup("Please enter your email and password.");
      return;
    }

    const payload = {
      action: "admin_login",
      email,
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
        window.location.href = data.redirect || "admin_dash.php";
        return;
      }

      showPopup(data.message || "Login failed. Please check your details.");
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  });
</script>

</body>
</html>

