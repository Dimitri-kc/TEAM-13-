<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include '../backend/config/db_connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up | Loft & Living</title>

    <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
    <link rel="stylesheet" href="../css/signup.style.css?v=2">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=5">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="auth-page admin-signup-page">
    <?php $headerPartialOnly = true; include 'header.php'; ?>

    <main class="form-container">
        <section class="form-box">
            <aside class="auth-hero">
                <div>
                    <div class="auth-kicker">Loft & Living Admin</div>
                    <h1 class="auth-hero-title">Create admin access.</h1>
                    <p class="auth-hero-copy">Register a new admin account to manage products, customers, orders, and site operations.</p>
                </div>

                <div class="auth-feature-list">
                    <div class="auth-feature">
                        <span class="auth-feature-label">Permissions</span>
                        <span class="auth-feature-value">Admin dashboard and management tools</span>
                    </div>
                    <div class="auth-feature">
                        <span class="auth-feature-label">Address Setup</span>
                        <span class="auth-feature-value">Use postcode lookup or enter details manually</span>
                    </div>
                </div>
            </aside>

            <div class="auth-panel">
                <div class="auth-panel-header">
                    <h1 class="form-title">Admin Sign Up</h1>
                    <p class="form-subtitle">Create an admin account to access the dashboard and management tools.</p>
                </div>

                <form id="adminSignupForm">
                    <div class="form-grid">
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

                        <div class="input-group input-group--full">
                            <span class="input-label">Address</span>
                            <div class="postcode-row">
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
                            <div class="address-select-wrap" id="addressSelectWrap">
                                <select id="addressSelect" onchange="selectAddress()">
                                 <option value="">— Select your address —</option>
                                </select>
                            </div>
                            <div class="address-confirmed" id="addressConfirmed">
                                <span class="address-confirmed-text" id="addressConfirmedText"></span>
                                <button type="button" class="address-change-btn" onclick="resetAddressLookup()">Change</button>
                            </div>
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
                    </div>

                    <div id="errorPopup" class="error-popup">
                        <div class="error-icon" id="errorIcon">!</div>
                        <div class="error-text" id="errorText"></div>
                    </div>

                    <button type="submit" class="main-button">Create Admin Account</button>

                    <p class="form-footer">
                        Already have an admin account?
                        <a href="admin_signin.php" class="link-primary">Sign in</a>
                    </p>
                </form>
            </div>
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

        const API_URL = "../backend/routes/userRoutes.php";
        const form = document.getElementById("adminSignupForm");
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
                try {
                    return JSON.parse(raw);
                } catch {}
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

            if (!name) { showPopup("Please enter your name."); nameField.focus(); return; }
            if (!surname) { showPopup("Please enter your surname."); surnameField.focus(); return; }
            if (!email || !emailPattern.test(email)) { showPopup("Please enter a valid email address."); emailField.focus(); return; }
            if (!phone) { showPopup("Please enter your phone number."); phoneField.focus(); return; }
            if (!address) { showPopup("Please find or enter your address."); document.getElementById('postcodeInput').focus(); return; } //address required/validated via lookup/manual flow
            if (!password) { showPopup("Please enter a password."); passwordField.focus(); return; }
            if (!confirmPassword) { showPopup("Please confirm your password."); confirmPasswordField.focus(); return; }
            if (password !== confirmPassword) { showPopup("Passwords do not match."); confirmPasswordField.focus(); return; }

            //updated password validation to match backend immediate feedback
            if (password.length < 8) { showPopup("Password must be at least 8 characters."); passwordField.focus(); return; }
            if (!/[A-Z]/.test(password)) { showPopup("Password must include an uppercase character."); passwordField.focus(); return; }
            if (!/[a-z]/.test(password)) { showPopup("Password must include a lowercase character."); passwordField.focus(); return; }
            if (!/[0-9]/.test(password)) { showPopup("Password must include a number."); passwordField.focus(); return; }
            if (!/[^A-Za-z0-9]/.test(password)) { showPopup("Password must include a special character."); passwordField.focus(); return; }

            const payload = {
                action: "register_admin",
                name,
                surname,
                email,
                phone,
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
                    sessionStorage.setItem("adminSignupSuccessMessage", "Admin account created successfully. Please sign in.");
                    window.location.href = "admin_signin.php";
                    return;
                }

                showPopup(data.message || "Admin registration failed. Please try again.");
            } catch (err) {
                console.error(err);
                showPopup("Server error. Please try again.");
            }
        });
    </script>
</body>
</html>
