<!-- top line creates coorect link to backend database connection -->
<?php include '../backend/config/db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up | LOFT & LIVING</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
  <link rel="stylesheet" href="../css/signup.style.css">

  <style>
    body {
      display: flex !important;
      flex-direction: column !important;
      min-height: 100vh;
      margin: 0;
      background-color: #EAE8E4;
    }

    .site-header {
      position: relative !important;
      width: 100%;
      background-color: #ffffff !important;
      z-index: 1000;
      padding: 15px 0;
    }

    .header-inner {
      display: flex !important;
      justify-content: space-between !important;
      align-items: center !important;
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
    }

    main.form-container {
      margin-top: 50px !important;
      margin-bottom: 50px !important;
      flex: 1;
      width: 100%;
      display: flex;
      justify-content: center;
    }

    .site-footer {
      margin-top: auto;
      width: 100%;
      background-color: #fff;
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

    /* Find Address below */
    /*Postcode row — input & button side by side */
    .postcode-row {
      display: flex;
      gap: 8px;
      align-items: center;
    }
    .postcode-row input {
      flex: 1;
      text-transform: uppercase;
      letter-spacing: 1px;
    }
    .find-address-btn {
      padding: 10px 14px;
      background: #1C1C1E;
      color: #fff;
      border: none;
      border-radius: 6px;
      font-family: 'Inter', sans-serif;
      font-size: 13px;
      font-weight: 600;
      white-space: nowrap;
      cursor: pointer;
      transition: background 0.2s;
      flex-shrink: 0;
    }
    .find-address-btn:hover  { background: #000; }
    .find-address-btn:disabled { background: #999; cursor: not-allowed; }

    /* Dropdown of matching addresses */
    .address-select-wrap {
      display: none;
      margin-top: 8px;
    }
    .address-select-wrap.visible { display: block; }

    .address-select-wrap select {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-family: 'Inter', sans-serif;
      font-size: 14px;
      background: #fff;
      cursor: pointer;
    }

    /* manual entry for address */
    .manual-toggle {
      display: inline-block;
      margin-top: 6px;
      font-size: 12px;
      color: #666;
      text-decoration: underline;
      cursor: pointer;
      background: none;
      border: none;
      padding: 0;
      font-family: 'Inter', sans-serif;
    }
    .manual-toggle:hover { color: #000; }

    /* manual address input > hidden, shows if lookup fails */
    .manual-address-group {
      display: none;
      margin-top: 10px;
    }
    .manual-address-group.visible { display: block; }
    .manual-address-group input {
      width: 100%;
      box-sizing: border-box;
    }

    /* confirmed address display*/
    .address-confirmed {
      display: none;
      margin-top: 8px;
      padding: 10px 12px;
      background: #f0f7f3;
      border: 1px solid #b2d4c0;
      border-radius: 6px;
      font-size: 13px;
      color: #1C1C1E;
      line-height: 1.5;
    }
    .address-confirmed.visible { display: flex; align-items: flex-start; gap: 8px; }
    .address-confirmed-text { flex: 1; }
    .address-change-btn {
      font-size: 12px;
      color: #666;
      background: none;
      border: none;
      cursor: pointer;
      text-decoration: underline;
      padding: 0;
      font-family: 'Inter', sans-serif;
      flex-shrink: 0;
    }
    .address-change-btn:hover { color: #000; }
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

      <!--address field replaced with find-address feature ── -->
      <div class="input-group">
        <span class="input-label">Address</span>
        <div class="postcode-row"> <!--postcode + lookup button -->
          <input
            type="text"
            id="postcodeInput"
            placeholder="Enter postcode e.g. NN4 8GR"
            maxlength="8"
            autocomplete="postal-code"
          >
          <button type="button" class="find-address-btn" id="findAddressBtn" onclick="findAddress()">
            Find Address
          </button>
        </div>
        <!-- dropdown of matching addresses-->
        <div class="address-select-wrap" id="addressSelectWrap">
          <select id="addressSelect" onchange="selectAddress()">
            <option value="">— Select your address —</option>
          </select>
        </div>
        <!-- Address confirmation-->
        <div class="address-confirmed" id="addressConfirmed">
          <span class="address-confirmed-text" id="addressConfirmedText"></span>
          <button type="button" class="address-change-btn" onclick="resetAddressLookup()">Change</button>
        </div>
        <!-- manual entry > if lookup fails-->
        <div class="manual-address-group" id="manualAddressGroup">
          <input
            type="text"
            id="manualAddress"
            placeholder="e.g. 26 Lion Ct, Northampton, NN4 8GR"
            autocomplete="street-address"
          >
        </div>
        <input type="hidden" id="address" name="address">
        <button type="button" class="manual-toggle" id="manualToggleBtn" onclick="toggleManual()">
          Enter address manually instead
        </button>
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
        <div class="error-icon" id="errorIcon">!</div>
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

<?php $footerPartialOnly = true; include 'footer.php'; ?>

<script>
  const ADDRESS_LOOKUP_URL = "/TEAM-13-/Draft/backend/routes/addressLookup.php";
  let usingManual = false;

  async function findAddress() {
    const rawPostcode = document.getElementById('postcodeInput').value.trim();
    if (!rawPostcode) {
      showPopup('Please enter a postcode first.');
      return;
    }

    //UK postcode format check 
    const postcode = rawPostcode.replace(/\s+/g, ' ').toUpperCase();
    if (!/^[A-Z]{1,2}[0-9][0-9A-Z]?\s?[0-9][A-Z]{2}$/.test(postcode)) {
      showPopup('Please enter a valid UK postcode (e.g. NN4 8GR).');
      return;
    }
    const btn = document.getElementById('findAddressBtn');
    btn.disabled = true;
    btn.textContent = 'Searching...';
    hidePopup();

    try {
      const res = await fetch(`https://api.postcodes.io/postcodes/${encodeURIComponent(postcode)}`);
      const data = await res.json();
      if (!res.ok || data.status !== 200) {
        showManualFallback('Postcode not found. Please enter your address manually.');
        return;
      }
      const town = data.result.admin_district || '';
      const county = data.result.admin_county || '';
      const area = [town, county].filter(Boolean).join(', ');
      document.getElementById('manualAddress').value = area + ', ' + postcode; //prefill manual field with area/postcode
      hidePopup();
      showManualFallback(`Postcode confirmed (${area}). Add your house number and street above.`, 'success');

      //populate dropdown with the returned addresses
      const select = document.getElementById('addressSelect');
      select.innerHTML = '<option value="">— Select your address —</option>';

    } catch (err) {
      console.error('Address lookup error:', err);
      showManualFallback('Address lookup failed. Please enter your address manually.');
    } finally {
      btn.disabled = false;
      btn.textContent = 'Find Address';
    }
  }

  function selectAddress() {
    const select = document.getElementById('addressSelect');
    const chosen = select.value;
    if (!chosen) return; 
    usingManual = false; //write the full chosen address into the hidden input that gets submitted
    document.getElementById('address').value = chosen;//show the confirmed and hide dropdown
    document.getElementById('addressConfirmedText').textContent = chosen;
    document.getElementById('addressConfirmed').classList.add('visible');
    document.getElementById('addressSelectWrap').classList.remove('visible');
    hidePopup();
  }

  function resetAddressLookup() {//clear everything so user can start fresh
    document.getElementById('address').value = '';
    document.getElementById('postcodeInput').value = '';
    document.getElementById('addressSelect').innerHTML = '<option value="">— Select your address —</option>';
    document.getElementById('addressSelectWrap').classList.remove('visible');
    document.getElementById('addressConfirmed').classList.remove('visible');
    document.getElementById('manualAddressGroup').classList.remove('visible');
    document.getElementById('manualAddress').value = '';
    usingManual = false;
    document.getElementById('manualToggleBtn').textContent = 'Enter address manually instead';
  }
  //show the error in the existing popup and reveal manual field if lookup fails
  function showManualFallback(message, type = 'error') {
    if (type !== 'success') showPopup(message);
    document.getElementById('manualAddressGroup').classList.add('visible');
    document.getElementById('addressSelectWrap').classList.remove('visible');
    usingManual = true;
    document.getElementById('manualToggleBtn').textContent = 'Use postcode lookup instead';
  }

  function toggleManual() {
    usingManual = !usingManual;
    const manualGroup = document.getElementById('manualAddressGroup');
    const toggleBtn = document.getElementById('manualToggleBtn');
    const selectWrap = document.getElementById('addressSelectWrap');
    const confirmed = document.getElementById('addressConfirmed');

    if (usingManual) {
      manualGroup.classList.add('visible');
      selectWrap.classList.remove('visible');
      confirmed.classList.remove('visible');
      document.getElementById('address').value = '';
      toggleBtn.textContent = 'Use postcode lookup instead';
    } else {
      manualGroup.classList.remove('visible');
      document.getElementById('manualAddress').value = '';
      document.getElementById('address').value = '';
      toggleBtn.textContent = 'Enter address manually instead';
    }
  }

  //enter key can trigger postcode lookup for better accessibility/UX
  document.getElementById('postcodeInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
      e.preventDefault();
      findAddress();
    }
  });
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
    const passwordField = document.getElementById("password");
    const confirmPasswordField = document.getElementById("confirmPassword");

    const name = nameField.value.trim();
    const surname = surnameField.value.trim();
    const email = emailField.value.trim();
    const phone = phoneField.value.trim();
    const password = passwordField.value;
    const confirmPassword = confirmPasswordField.value;

    if (usingManual) {//if manual entry, take that value as the address instead of (empty) hidden field
      const manualVal = document.getElementById('manualAddress').value.trim();
      document.getElementById('address').value = manualVal;
    }
    const address = document.getElementById('address').value.trim();

    if (!name)    { showPopup("Please enter your name.");    nameField.focus();    return; }
    if (!surname) { showPopup("Please enter your surname."); surnameField.focus(); return; }
    if (!email || !emailPattern.test(email)) { showPopup("Please enter a valid email address."); emailField.focus(); return; }
    if (!phone) { showPopup("Please enter your phone number."); phoneField.focus(); return; }
    if (!address) { showPopup("Please find or enter your address."); document.getElementById('postcodeInput').focus(); return; } //address required/validated via lookup/manual flow
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
</body>
</html>