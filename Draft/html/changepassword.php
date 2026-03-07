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
  <title>Change Password | LOFT & LIVING</title>

  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/header_footer_style.css?v=12">
  <link rel="stylesheet" href="../css/dark-mode.css?v=9">
  <link rel="stylesheet" href="../css/signup.style.css">

  <style>
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
    .header-inner {
      max-width: 1400px !important;
      margin: 0 auto !important;
      height: 100% !important;
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      padding: 0 40px !important;
    }
    .header-left-tools { display: flex !important; align-items: center !important; gap: 25px !important; }
    .logo-wrapper { position: absolute !important; left: 50% !important; top: 50% !important; transform: translate(-50%, -50%) !important; }
    .main-logo { height: 50px !important; width: auto !important; max-width: 280px !important; object-fit: contain !important; display: block !important; filter: invert(1) !important; opacity: 0.95 !important; }
    .ui-icon { width: 20px !important; height: 20px !important; object-fit: contain !important; display: block !important; }
    .header-actions { display: flex !important; align-items: center !important; gap: 25px !important; }
    main.form-container { margin-top: 20px !important; margin-bottom: 50px !important; flex: 1 !important; width: 100% !important; display: flex !important; justify-content: center !important; align-items: flex-start !important; padding: 0 !important; }
    .site-footer { position: relative !important; background: white !important; border-top: 1px solid #e0e0e0 !important; margin-top: 60px !important; padding: 60px 0 !important; width: 100% !important; }
    .footer-inner { max-width: 1200px !important; margin: 0 auto !important; padding: 0 40px !important; display: grid !important; grid-template-columns: 0.5fr 1fr 1fr 1fr !important; gap: 40px !important; }
    html.dark-mode .site-header { background-color: #1a1a1a !important; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important; }
    html.dark-mode .ui-icon { filter: invert(1) !important; }
    html.dark-mode .main-logo { filter: invert(0) !important; }
    html.dark-mode body.page-background { background-color: #1a1a1a !important; color: #e0e0e0 !important; }
    html.dark-mode .site-footer { background-color: #1a1a1a !important; border-top: 1px solid #333 !important; }
    html.dark-mode .form-box { background-color: #242424 !important; color: #e0e0e0 !important; }
    html.dark-mode .form-title, html.dark-mode .input-label, html.dark-mode .helper-text { color: #e0e0e0 !important; }
    html.dark-mode .input-group input { background-color: #1a1a1a !important; border-color: #444 !important; color: #e0e0e0 !important; }

    .helper-text { text-align: center; font-size: 0.95rem; margin-top: -6px; margin-bottom: 16px; opacity: 0.8; }
    .error-popup { display: none; margin-top: 12px; padding: 10px 12px; border-radius: 4px; background: #fff; border: 1px solid #f0b400; box-shadow: 0 2px 4px rgba(0,0,0,0.12); font-size: 0.9rem; line-height: 1.4; align-items: flex-start; gap: 8px; }
    .error-icon { width: 18px; height: 18px; border-radius: 3px; background: #ffa000; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
    .error-text { flex: 1; }
  </style>
  <script src="../javascript/dark-mode.js"></script>
</head>

<body class="page-background">

<header class="site-header">
  <div class="header-inner">
    <div class="header-left-tools">
      <button class="menu-btn" id="menu-toggle-btn" type="button" aria-label="Open menu">
        <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
      </button>
      <img src="../images/header_footer_images/icon-moon.png" alt="Dark Mode" class="ui-icon" id="moon-icon" data-light-src="../images/header_footer_images/icon-moon.png" data-dark-src="../images/header_footer_images/icon-moon2.png" style="margin-left: 8px; margin-right: 8px; vertical-align: middle; cursor: pointer;">
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
    <h1 class="form-title">CHANGE PASSWORD</h1>
    <p class="helper-text">You must change your password before continuing.</p>

    <form class="form-fields" id="changePasswordForm">
      <label class="input-group">
        <span class="input-label">New Password</span>
        <input type="password" name="newPassword" required>
      </label>

      <label class="input-group">
        <span class="input-label">Confirm New Password</span>
        <input type="password" name="confirmPassword" required>
      </label>

      <div id="errorPopup" class="error-popup">
        <div class="error-icon">!</div>
        <div class="error-text" id="errorText"></div>
      </div>

      <button type="submit" class="main-button">Update Password</button>
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
        <li><a href="Homepage.php">Homepage</a></li>
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
      <ul>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
    </div>
  </div>
</footer>

<script>
  const API_URL = "../backend/routes/userRoutes.php";
  const form = document.getElementById("changePasswordForm");

  function showPopup(message){
    document.getElementById("errorText").textContent = message;
    document.getElementById("errorPopup").style.display = "flex";
  }

  function hidePopup(){
    document.getElementById("errorPopup").style.display = "none";
  }

  async function readJsonSafely(res){
    const ct = (res.headers.get("content-type") || "").toLowerCase();
    const raw = await res.text();
    if (ct.includes("application/json")){
      try { return JSON.parse(raw); } catch {}
    }
    return { success:false, message: raw || "Server error. Please try again." };
  }

  form.addEventListener("submit", async (e) => {
    e.preventDefault();
    hidePopup();

    const newPassword = form.querySelector('input[name="newPassword"]').value;
    const confirmPassword = form.querySelector('input[name="confirmPassword"]').value;

    if (!newPassword || !confirmPassword) return showPopup("Please fill in both fields.");
    if (newPassword !== confirmPassword) return showPopup("Passwords do not match.");

    try{
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "change_password",
          newPassword,
          confirmPassword
        })
      });

      const data = await readJsonSafely(res);

      if (data.success){
        window.location.href = data.redirect || "Homepage.php";
        return;
      }

      showPopup(data.message || "Failed to update password.");
    } catch (err){
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  });
</script>

<script src="../javascript/header_footer_script.js"></script>
<script src="../javascript/global/basketIcon.js"></script>

</body>
</html>



