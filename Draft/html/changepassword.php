<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password | LOFT & LIVING</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

 
  <link rel="stylesheet" href="../css/header_footer_style.css?v=14">
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

    /* styling for error popup success */
    .error-popup.success {
      border-color: #2e7d32;
      background: #f1fff3;
    }
    .error-popup.success .error-icon {
      background: #2e7d32;
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
    <h1 class="form-title" style="white-space: nowrap;">CHANGE PASSWORD</h1>
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