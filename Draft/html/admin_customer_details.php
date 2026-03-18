<?php include '../backend/config/db_connect.php'; 
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Details | Loft & Living</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/header_footer_style.css?v=14">
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

    .header-actions {
      display: flex !important;
      flex-direction: row !important;
      align-items: center !important;
      justify-content: flex-end !important;
      gap: 20px;
    }

    .header-actions a {
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }

    .header-actions img {
      display: block;
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

    .form-title {
      white-space: nowrap;
      font-size: 36px;
      line-height: 1.1;
      text-align: center;
      margin: 0;
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

    .helper-text {
      text-align: center;
      font-size: 0.95rem;
      margin-top: 10px;
      margin-bottom: 18px;
      opacity: 0.85;
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

    .action-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 12px;
      margin-top: 12px;
    }

    .secondary-button,
    .warning-button,
    .danger-button {
      width: 100%;
      height: 46px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: 600;
      font-size: 15px;
      margin: 0;
      border: 1px solid #b8b8b8;
      background: #f3f3f3;
      color: #111;
      transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .secondary-button:hover {
      background: #dcdcdc;
      border-color: #a8a8a8;
      color: #111;
    }

    .warning-button:hover {
      background: #f0b400;
      border-color: #f0b400;
      color: #111;
    }

    .danger-button:hover {
      background: #c62828;
      border-color: #c62828;
      color: #fff;
    }

    @media (max-width: 760px) {
      .action-row {
        grid-template-columns: 1fr;
      }

      .form-title {
        white-space: normal;
      }
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
    <h1 class="form-title">Customer Details</h1>
    <p class="helper-text">View and manage customer information</p>

    <div id="errorPopup" class="error-popup">
      <div class="error-icon">!</div>
      <div class="error-text" id="errorText"></div>
    </div>

    <form id="editForm">
      <div class="input-group">
        <span class="input-label">Role</span>
        <select id="role">
          <option value="customer">Customer</option>
          <option value="admin">Admin</option>
        </select>
      </div>

      <div class="input-group">
        <span class="input-label">Name</span>
        <input type="text" id="name" required>
      </div>

      <div class="input-group">
        <span class="input-label">Surname</span>
        <input type="text" id="surname" required>
      </div>

      <div class="input-group">
        <span class="input-label">Email</span>
        <input type="email" id="email" required>
      </div>

      <div class="input-group">
        <span class="input-label">Phone Number</span>
        <input type="tel" id="phone">
      </div>

      <div class="input-group">
        <span class="input-label">Address</span>
        <input type="text" id="address">
      </div>

      <button type="submit" class="main-button">Edit</button>
    </form>

    <div class="action-row">
      <button type="button" class="secondary-button" id="ordersBtn">Order History</button>
      <button type="button" class="warning-button" id="deactivateBtn">Deactivate</button>
      <button type="button" class="danger-button" id="removeBtn">Remove</button>
    </div>
  </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>

<script>
  const API_URL = "/TEAM-13-/Draft/backend/routes/adminRoutes.php";
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  const params = new URLSearchParams(window.location.search);
  const customerId = params.get("user_id") || params.get("id");

  const deactivateBtn = document.getElementById("deactivateBtn");
  const ordersBtn = document.getElementById("ordersBtn");
  let currentStatus = "active";

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

  function setStatusUI(status) {
    currentStatus = (status || "active").toLowerCase();
    deactivateBtn.textContent = currentStatus === "inactive" ? "Reactivate" : "Deactivate";
  }

  async function loadCustomer() {
    hidePopup();

    if (!customerId) {
      showPopup("Missing customer id.");
      return;
    }

    try {
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "customer_details",
          customer_ID: customerId
        })
      });

      const data = await readJsonSafely(res);

      if (!data.success || !data.customer) {
        showPopup(data.message || "Customer not found.");
        return;
      }

      const c = data.customer;

      document.getElementById("role").value = c.role ?? "customer";
      document.getElementById("name").value = c.name ?? "";
      document.getElementById("surname").value = c.surname ?? "";
      document.getElementById("email").value = c.email ?? "";
      document.getElementById("phone").value = c.phone ?? "";
      document.getElementById("address").value = c.address ?? "";

      setStatusUI(c.status ?? "active");
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  }

  document.getElementById("editForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    hidePopup();

    if (!customerId) {
      showPopup("Missing customer id.");
      return;
    }

    const nameField = document.getElementById("name");
    const surnameField = document.getElementById("surname");
    const emailField = document.getElementById("email");

    const name = nameField.value.trim();
    const surname = surnameField.value.trim();
    const email = emailField.value.trim();
    const phone = document.getElementById("phone").value.trim();
    const address = document.getElementById("address").value.trim();

    if (!name) { showPopup("Please enter the customer's name."); nameField.focus(); return; }
    if (!surname) { showPopup("Please enter the customer's surname."); surnameField.focus(); return; }
    if (!email || !emailPattern.test(email)) { showPopup("Please enter a valid email address."); emailField.focus(); return; }

    try {
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "update_customer",
          customer_ID: customerId,
          role: document.getElementById("role").value,
          name,
          surname,
          email,
          phone,
          address
        })
      });

      const data = await readJsonSafely(res);

      if (!data.success) {
        showPopup(data.message || "Update failed. Please try again.");
      }
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  });

  deactivateBtn.addEventListener("click", async () => {
    hidePopup();

    if (!customerId) {
      showPopup("Missing customer id.");
      return;
    }

    const mode = currentStatus === "inactive" ? "reactivate" : "deactivate";

    try {
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "deactivate_customer",
          customer_ID: customerId,
          mode
        })
      });

      const data = await readJsonSafely(res);

      if (data.success) {
        setStatusUI(data.new_status || (mode === "deactivate" ? "inactive" : "active"));
        return;
      }

      showPopup(data.message || "Action failed. Please try again.");
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  });

  document.getElementById("removeBtn").addEventListener("click", async () => {
    hidePopup();

    if (!customerId) {
      showPopup("Missing customer id.");
      return;
    }

    const confirmed = confirm("Remove this customer?");
    if (!confirmed) return;

    try {
      const res = await fetch(API_URL, {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          action: "deactivate_customer",
          customer_ID: customerId,
          mode: "remove"
        })
      });

      const data = await readJsonSafely(res);

      if (data.success) {
        window.location.href = "admin_customer_management.php";
        return;
      }

      showPopup(data.message || "Remove failed. Please try again.");
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  });

  ordersBtn.addEventListener("click", () => {
    if (!customerId) {
      showPopup("Missing customer id.");
      return;
    }
    window.location.href = `admin_order_history.php?user_id=${customerId}`;
  });

  loadCustomer();
</script>
</body>
</html>