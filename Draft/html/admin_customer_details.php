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
  <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
  <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
  <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
  <link rel="stylesheet" href="../css/dark-mode.css?v=12">
  <link rel="stylesheet" href="../css/reusable_header.css?v=11">
  <link rel="stylesheet" href="../css/admin_customer_details.css?v=1">
  <script src="../javascript/dark-mode.js"></script>
</head>

<body class="admin-customer-details-page">
<?php $headerPartialOnly = true; include 'header.php'; ?>

<main class="form-container">
  <section class="customer-details-shell">
    <div class="page-topbar">
      <div class="page-topbar-copy">
        <h1 class="form-title">Customer Details</h1>
        <p class="helper-text">Review account information, update customer records, and manage account access from one place.</p>
      </div>

      <button
        type="button"
        class="return-btn"
        onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='admin_customer_management.php'; }"
      >
        Return to Previous Page
      </button>
    </div>

    <div class="details-grid">
      <section class="form-box">
        <div id="errorPopup" class="error-popup">
          <div class="error-icon">!</div>
          <div class="error-text" id="errorText"></div>
        </div>

        <form id="editForm">
          <div class="form-grid">
            <div class="input-group">
              <span class="input-label">Role</span>
              <select id="role">
                <option value="customer">Customer</option>
                <option value="admin">Admin</option>
              </select>
            </div>

            <div class="input-group">
              <span class="input-label">Email</span>
              <input type="email" id="email" required>
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
              <span class="input-label">Phone Number</span>
              <input type="tel" id="phone">
            </div>

            <div class="input-group input-group--full">
              <span class="input-label">Address</span>
              <input type="text" id="address">
            </div>
          </div>

          <button type="submit" class="main-button">Save Changes</button>
        </form>
      </section>

      <aside class="summary-card">
        <h2 class="summary-title">Account Controls</h2>
        <p class="summary-copy">Use these actions to review the customer’s orders, adjust account access, or remove the record entirely.</p>

        <div class="summary-list">
          <div class="summary-item">
            <span class="summary-label">Record</span>
            <span class="summary-value">Customer Profile</span>
          </div>
          <div class="summary-item">
            <span class="summary-label">Actions</span>
            <span class="summary-value">Orders, status changes, removal</span>
          </div>
        </div>

        <div class="action-row">
          <button type="button" class="secondary-button" id="ordersBtn">Order History</button>
          <button type="button" class="warning-button" id="deactivateBtn">Deactivate</button>
          <button type="button" class="danger-button" id="removeBtn">Remove</button>
        </div>
      </aside>
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
