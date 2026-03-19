<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password | LOFT & LIVING</title>
  <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
  <link rel="stylesheet" href="../css/signup.style.css?v=2">
  <link rel="stylesheet" href="../css/changepassword.css?v=2">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=13">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="auth-page change-password-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<main class="auth-wrap">
  <section class="auth-shell auth-shell--compact change-password-shell">
    <aside class="auth-hero change-password-hero">
      <div>
        <div class="auth-kicker">Account Security</div>
        <h1 class="auth-hero-title">Set a fresh password.</h1>
        <p class="auth-hero-copy">Update your password before continuing so your account stays secure and ready to use across the site.</p>
      </div>

      <div class="auth-feature-list">
        <div class="auth-feature">
          <span class="auth-feature-label">Requirement</span>
          <span class="auth-feature-value">At least 8 characters with upper, lower, number and symbol</span>
        </div>
        <div class="auth-feature">
          <span class="auth-feature-label">After update</span>
          <span class="auth-feature-value">You will be sent back to the right page automatically</span>
        </div>
      </div>
    </aside>

    <div class="auth-panel change-password-panel">
      <div class="auth-heading">
        <h2>Change password</h2>
        <p>You must update your password before continuing.</p>
      </div>

      <div id="errorPopup" class="error-popup">
        <div class="error-icon">!</div>
        <div class="error-text" id="errorText"></div>
      </div>

      <div class="auth-card">
        <form id="changePasswordForm" class="change-password-form">
          <label class="input-group">
            <span class="input-label">New Password</span>
            <input type="password" name="newPassword" placeholder="Enter a new password" required>
          </label>

          <label class="input-group">
            <span class="input-label">Confirm New Password</span>
            <input type="password" name="confirmPassword" placeholder="Confirm your new password" required>
          </label>

          <div class="password-guidance">
            Use a strong password with upper and lowercase letters, a number, and a special character.
          </div>

          <button type="submit" class="main-button">Update Password</button>
        </form>
      </div>
    </div>
  </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>

<script>
  const API_URL = "../backend/routes/userRoutes.php";
  const form = document.getElementById("changePasswordForm");

  function showPopup(message, type = "error"){
    document.getElementById("errorText").textContent = message;
    document.getElementById("errorPopup").style.display = "flex";
    document.getElementById("errorPopup").classList.toggle("success", type === "success");
  }

  function hidePopup(){
    document.getElementById("errorPopup").classList.remove("success");
    document.getElementById("errorPopup").style.display = "none";
  }

  //get safe returnTo path for redirect after password change
  function getSafeReturnTo() {
    try {
      if (!document.referrer) return "";
      const ref = new URL(document.referrer);
      if (ref.origin !== window.location.origin) return ""; //only allow same-origin redirects
      if (ref.pathname.toLowerCase().includes("changepassword.php")) return ""; //prevent redirect loops back to change password page
      return ref.pathname.split("/").pop() + ref.search + ref.hash; //return path + query + hash for redirect e.g. "profile.php?tab=orders#section2"
    } catch {
      return ""; //if any error occurs (invalid URL etc.) return empty string to prevent redirect
    }
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
    const returnTo = getSafeReturnTo();//get safe returnTo path for redirect

    if (!newPassword || !confirmPassword) return showPopup("Please fill in both fields.");
    if (newPassword !== confirmPassword) return showPopup("Passwords do not match.");

    //added client-side validation with specific error messages for user convenience / instant feedback
    if (newPassword.length < 8) return showPopup("Password must be at least 8 characters.");
    if (!/[A-Z]/.test(newPassword)) return showPopup("Password must include an uppercase character.");
    if (!/[a-z]/.test(newPassword)) return showPopup("Password must include a lowercase character.");
    if (!/[0-9]/.test(newPassword)) return showPopup("Password must include a number.");
    if (!/[^A-Za-z0-9]/.test(newPassword)) return showPopup("Password must include a special character.");

    try{
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "change_password",
          newPassword,
          confirmPassword,
          returnTo //include returnTo in request for server to determine safe redirect
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

</body>
</html>
