<!-- top line creates coorect link to backend database connection -->
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
  <title>Sign Up | LOFT & LIVING</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
  <link rel="stylesheet" href="../css/dark-mode.css?v=9">
  <link rel="stylesheet" href="../css/signup.style.css">

  <style>
    /* Override signup.style.css conflicts */
    body.page-background {
      display: flex !important;
      flex-direction: column !important;
      min-height: 100vh !important;
      margin: 0 !important;
      padding: 0 !important;
      background-color: #F4F1EC !important;
      color: #2B2B2B !important;
      font-family: "Ibarra Real Nova", serif !important;
      padding-top: 120px !important;
      align-items: stretch !important;
      justify-content: flex-start !important;
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

    .header-left-tools {
      display: flex !important;
      align-items: center !important;
      gap: 25px !important;
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

    .logo-wrapper {
      position: absolute !important;
      left: 50% !important;
      top: 50% !important;
      transform: translate(-50%, -50%) !important;
    }

    .main-logo {
      height: 50px !important;
      width: auto !important;
      max-width: 280px !important;
      object-fit: contain !important;
      display: block !important;
      filter: invert(1) !important;
      opacity: 0.95 !important;
      transition: opacity 0.2s ease !important;
    }

    .main-logo:hover {
      opacity: 1 !important;
    }

    .ui-icon {
      width: 20px !important;
      height: 20px !important;
      object-fit: contain !important;
      display: block !important;
    }

    .header-actions {
      display: flex !important;
      align-items: center !important;
      gap: 25px !important;
    }

    .profile-wrapper {
      position: relative !important;
      display: inline-flex !important;
      align-items: center !important;
      z-index: 2000 !important;
    }

    .profile-btn {
      background: transparent !important;
      border: none !important;
      cursor: pointer !important;
      padding: 0 !important;
      display: inline-flex !important;
      align-items: center !important;
    }

    .profile-dropdown {
      position: absolute !important;
      top: 40px !important;
      right: 0 !important;
      width: 260px !important;
      background: #fff !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
      border: 1px solid #e0e0e0 !important;
      border-radius: 8px !important;
      padding: 18px !important;
      display: none !important;
      z-index: 3000 !important;
      font-family: "Ibarra Real Nova", serif !important;
    }

    .profile-dropdown.open { display: block !important; }

    .profile-welcome {
      font-size: 14px !important;
      font-weight: 700 !important;
      color: #2B2B2B !important;
      margin-bottom: 14px !important;
      font-family: "Ibarra Real Nova", serif !important;
    }

    .profile-link {
      display: block !important;
      font-size: 14px !important;
      color: #2B2B2B !important;
      padding: 10px 0 !important;
      text-decoration: none !important;
      font-family: "Ibarra Real Nova", serif !important;
    }

    .profile-link + .profile-link {
      border-top: 1px solid #E5E1DB !important;
    }

    /* Dropdown menu positioning */
    .dropdown-panel {
      position: absolute !important;
      top: 100px !important;
      left: 10px !important;
      width: 260px !important;
      background: #fff !important;
      box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important;
      border: 1px solid #e0e0e0 !important;
      border-radius: 8px !important;
      padding: 18px !important;
      display: none !important;
      z-index: 999 !important;
      font-family: "Ibarra Real Nova", serif !important;
    }

    .dropdown-panel.open {
      display: block !important;
    }

    .nav-links {
      margin: 0 !important;
      padding: 0 !important;
      list-style: none !important;
    }

    .nav-links li {
      margin: 0 !important;
    }

    .nav-links a {
      display: block !important;
      font-size: 14px !important;
      color: #2B2B2B !important;
      padding: 10px 0 !important;
      text-decoration: none !important;
      font-weight: 400 !important;
      font-family: "Ibarra Real Nova", serif !important;
    }

    .nav-links li + li a {
      border-top: 1px solid #E5E1DB !important;
    }

    .nav-divider {
      margin-top: 14px !important;
      padding-top: 0 !important;
      border-top: 1px solid #E5E1DB !important;
    }

    main.form-container {
      margin-top: 20px !important;
      margin-bottom: 50px !important;
      flex: 1 !important;
      width: 100% !important;
      display: flex !important;
      justify-content: center !important;
      align-items: flex-start !important;
      padding: 0 !important;
    }

    .site-footer {
      position: relative !important;
      background: white !important;
      border-top: 1px solid #e0e0e0 !important;
      margin-top: 60px !important;
      padding: 60px 0 !important;
      width: 100% !important;
    }

    .footer-inner {
      max-width: 1200px !important;
      margin: 0 auto !important;
      padding: 0 40px !important;
      display: grid !important;
      grid-template-columns: 0.5fr 1fr 1fr 1fr !important;
      gap: 40px !important;
    }

    .footer-section h4 {
      font-size: 14px !important;
      font-weight: 700 !important;
      margin-bottom: 16px !important;
      font-family: "Ibarra Real Nova", serif !important;
      color: #2B2B2B !important;
    }

    .footer-section ul {
      list-style: none !important;
      padding: 0 !important;
      margin: 0 !important;
    }

    .footer-section li {
      margin-bottom: 8px !important;
    }

    .footer-section a {
      font-size: 14px !important;
      color: #2B2B2B !important;
      text-decoration: none !important;
      font-family: "Ibarra Real Nova", serif !important;
    }

    .social-links {
      display: flex !important;
      gap: 16px !important;
      align-items: flex-start !important;
    }

    .social-icon {
      width: 20px !important;
      height: 20px !important;
      object-fit: contain !important;
      filter: invert(1) brightness(0.8) !important;
    }

    .basket-icon {
      position: relative !important;
      display: inline-flex !important;
      align-items: center !important;
    }

    #basket-count {
      position: absolute !important;
      top: -8px !important;
      right: -8px !important;
      background: #2B2B2B !important;
      color: white !important;
      border-radius: 50% !important;
      width: 18px !important;
      height: 18px !important;
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      font-size: 11px !important;
      font-weight: 600 !important;
    }

    .menu-btn {
      background: transparent !important;
      border: none !important;
      cursor: pointer !important;
      padding: 0 !important;
      display: inline-flex !important;
      align-items: center !important;
    }

    .mini-search {
      display: inline-flex !important;
      align-items: center !important;
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

    /* Dark mode overrides */
    html.dark-mode .site-header {
      background-color: #1a1a1a !important;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
    }

    html.dark-mode .site-footer {
      background-color: #1a1a1a !important;
      border-top: 1px solid #333 !important;
    }

    html.dark-mode .dropdown-panel {
      background-color: #242424 !important;
      border-color: #333 !important;
    }

    html.dark-mode .nav-links a {
      color: #e0e0e0 !important;
    }

    html.dark-mode .nav-links li + li a {
      border-top: 1px solid #444 !important;
    }

    html.dark-mode .nav-divider {
      border-top: 1px solid #444 !important;
    }

    html.dark-mode .profile-dropdown {
      background-color: #242424 !important;
      border-color: #333 !important;
    }

    html.dark-mode .profile-welcome {
      color: #e0e0e0 !important;
    }

    html.dark-mode .profile-link {
      color: #e0e0e0 !important;
    }

    html.dark-mode .profile-link + .profile-link {
      border-top: 1px solid #444 !important;
    }

    html.dark-mode body.page-background {
      background-color: #1a1a1a !important;
      color: #e0e0e0 !important;
    }

    html.dark-mode .ui-icon {
      filter: invert(1) !important;
    }

    html.dark-mode .main-logo {
      filter: invert(0) !important;
    }

    html.dark-mode .social-icon {
      filter: invert(1) !important;
    }

    html.dark-mode .form-box {
      background-color: #242424 !important;
      color: #e0e0e0 !important;
    }

    html.dark-mode .form-title {
      color: #e0e0e0 !important;
    }

    html.dark-mode .input-label {
      color: #e0e0e0 !important;
    }

    html.dark-mode .input-group input {
      background-color: #1a1a1a !important;
      border-color: #444 !important;
      color: #e0e0e0 !important;
    }

    html.dark-mode #basket-count {
      background: #e0e0e0 !important;
      color: #1a1a1a !important;
    }

    html.dark-mode .footer-section h4 {
      color: #e0e0e0 !important;
    }

    html.dark-mode .footer-section a {
      color: #e0e0e0 !important;
    }
  </style>
  <script src="../javascript/dark-mode.js"></script>
</head>

<body class="page-background">

<header class="site-header">
    <div class="header-inner">

        <!-- LEFT: menu + dark mode + search -->
        <div class="header-left-tools">
            <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
                <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
            </button>
            <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
            <!-- Replace search pill with search icon -->
            <a class="mini-search" href="search.php" aria-label="Search">
                <img src="../images/header_footer_images/icon-search.png" alt="Search" class="ui-icon" id="search-icon" style="vertical-align: middle;">
            </a>
        </div>

        <div class="logo-wrapper">
            <a href="homepage.php">
                <img src="../images/header_footer_images/logo1.png" alt="LOFT &amp; LIVING" class="main-logo">
            </a>
        </div>

        <div class="header-actions">
            <a href="favourites.php">
                <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
            </a>

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

            <a href="basket.php" class="basket-icon">
                <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
                <span id="basket-count">0</span>
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

        </ul>
    </nav>
</header>

<main class="form-container">
  <section class="form-box">
    <h1 class="form-title">NEW HERE?</h1>

    <form id="signupForm">
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
        <span class="input-label">Address</span>
        <input type="text" id="address" name="address" required>
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

      <button type="submit" class="main-button">Submit</button>

      <p class="form-footer">
        Already have an account?
        <a href="signin.php" class="link-primary">Sign in</a>
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
        <li><a href="signin.php">My Account</a></li>
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
      <ul><li><a href="contact.php">Contact Us</a></li><li><a href="about.php">About Us</a></li></ul>
    </div>
  </div>
</footer>

<script>
  const API_URL = "/TEAM-13-/Draft/backend/routes/userRoutes.php";
  const form = document.getElementById("signupForm");
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
      try { return JSON.parse(raw); } catch {}
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
    const addressField = document.getElementById("address");
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirmPassword");

    const name = nameField.value.trim();
    const surname = surnameField.value.trim();
    const email = emailField.value.trim();
    const phone = phoneField.value.trim();
    const address = addressField.value.trim();
    const password = passwordField.value;
    const confirmPassword = confirmPasswordField.value;

    if (!name) { showPopup("Please enter your name."); nameField.focus(); return; }
    if (!surname) { showPopup("Please enter your surname."); surnameField.focus(); return; }
    if (!email || !emailPattern.test(email)) { showPopup("Please enter a valid email address."); emailField.focus(); return; }
    if (!phone) { showPopup("Please enter your phone number."); phoneField.focus(); return; }
    if (!address) { showPopup("Please enter your address."); addressField.focus(); return; }
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
      action: "register",
      name,
      surname,
      email,
      phone,
      address,
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
        window.location.href = data.redirect || "signin.php";
        return;
      }

      showPopup(data.message || "Registration failed. Please try again.");
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  });
</script>

<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>
</body>
</html>

