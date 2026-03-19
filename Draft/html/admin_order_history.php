<?php include '../backend/config/db_connect.php'; 
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Order History | Loft & Living</title>
  <link rel="stylesheet" href="../css/header_footer_style.css?v=15">
  <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
  <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
  <link rel="stylesheet" href="../css/dark-mode.css?v=12">
  <link rel="stylesheet" href="../css/reusable_header.css?v=5">
  <link rel="stylesheet" href="../css/admin_order_history.css?v=1">
  <script src="../javascript/dark-mode.js"></script>
</head>

<body class="admin-order-history-page">
<?php $headerPartialOnly = true; include 'header.php'; ?>

<main class="form-container">
  <section class="order-history-shell">
    <div class="page-topbar">
      <div class="page-topbar-copy">
        <h1 class="form-title">Order History</h1>
        <p class="helper-text" id="customerLine">View customer orders</p>
      </div>

      <button type="button" class="back-button" id="backBtn">Back to Customer Details</button>
    </div>

    <section class="content-card">
      <div id="errorPopup" class="error-popup">
        <div class="error-icon">!</div>
        <div class="error-text" id="errorText"></div>
      </div>

      <div class="orders-list" id="ordersList"></div>
      <div class="no-orders" id="emptyState" style="display:none;">No orders found for this customer.</div>
    </section>
  </section>
</main>

<?php $footerPartialOnly = true; include 'footer.php'; ?>

<script>
  const API_URL = "/TEAM-13-/Draft/backend/routes/adminRoutes.php";

  const params = new URLSearchParams(window.location.search);
  const customerId = params.get("user_id") || params.get("id");

  const ordersList = document.getElementById("ordersList");
  const emptyState = document.getElementById("emptyState");
  const customerLine = document.getElementById("customerLine");
  const backBtn = document.getElementById("backBtn");

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

  function renderOrders(orders) {
    ordersList.innerHTML = "";

    if (!orders || !orders.length) {
      emptyState.style.display = "block";
      return;
    }

    emptyState.style.display = "none";

    orders.forEach(order => {
      const card = document.createElement("div");
      card.className = "order-card";
      card.innerHTML = `
        <h3>Order #${order.order_id ?? "-"}</h3>
        <div class="order-grid">
          <div class="order-field">
            <span class="order-label">Date</span>
            <span class="order-value">${order.order_date ?? "-"}</span>
          </div>
          <div class="order-field">
            <span class="order-label">Status</span>
            <span class="order-value">${order.status ?? "-"}</span>
          </div>
          <div class="order-field">
            <span class="order-label">Total</span>
            <span class="order-value">${order.total_amount ?? "-"}</span>
          </div>
        </div>
      `;//removed payment method as data not stored in DB
      ordersList.appendChild(card);
    });
  }

  async function fetchCustomerLabel(customerId) {
  const res = await fetch(API_URL, { //fetch customer details to get name for customer line label
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      action: "customer_details",
      customer_ID: Number(customerId)
    })
  });

  const data = await readJsonSafely(res);
  if (!data?.success || !data?.customer) return `Order history for user ID: ${customerId}`;

  const c = data.customer;
  const fullName = `${c.name ?? ""} ${c.surname ?? ""}`.trim(); //show name with user id
  return fullName
    ? `Order history for ${fullName} (ID: ${customerId})`
    : `Order history for user ID: ${customerId}`;
}

  async function loadOrderHistory() {
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
          action: "customer_orders",
          customer_ID: customerId
        })
      });

      const data = await readJsonSafely(res);

      if (!data.success) {
        showPopup(data.message || "Failed to load orders.");
        return;
      }

      customerLine.textContent = await fetchCustomerLabel(customerId);

      renderOrders(data.orders || []);
    } catch (err) {
      console.error(err);
      showPopup("Server error. Please try again.");
    }
  }

  backBtn.addEventListener("click", () => {
    if (!customerId) {
      showPopup("Missing customer id.");
      return;
    }
    window.location.href = `admin_customer_details.php?user_id=${customerId}`;
  });

  loadOrderHistory();
</script>
</body>
</html>
