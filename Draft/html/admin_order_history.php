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

    .helper-text {
      text-align: center;
      font-size: 0.95rem;
      margin-top: -10px;
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

    .secondary-button {
      width: 100%;
      height: 44px;
      border: 1px solid #d9d9d9;
      border-radius: 4px;
      cursor: pointer;
      font-weight: 600;
      background: #fff;
      color: #111;
      margin-bottom: 18px;
    }

    .orders-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .order-card {
      border: 1px solid #e2e2e2;
      border-radius: 6px;
      padding: 16px;
      background: #fafafa;
    }

    .order-card h3 {
      margin: 0 0 12px;
      font-size: 16px;
      font-weight: 600;
    }

    .order-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px;
    }

    .order-field {
      display: flex;
      flex-direction: column;
    }

    .order-label {
      font-size: 13px;
      font-weight: 600;
      margin-bottom: 4px;
      color: #111;
    }

    .order-value {
      font-size: 14px;
      color: #444;
      word-break: break-word;
    }

    .no-orders {
      text-align: center;
      font-size: 0.95rem;
      opacity: 0.85;
      margin-top: 10px;
    }

    @media (max-width: 760px) {
      .order-grid {
        grid-template-columns: 1fr;
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
    <h1 class="form-title">Order History</h1>
    <p class="helper-text" id="customerLine">View customer orders</p>

    <div id="errorPopup" class="error-popup">
      <div class="error-icon">!</div>
      <div class="error-text" id="errorText"></div>
    </div>

    <button type="button" class="secondary-button" id="backBtn">Back to Customer Details</button>

    <div class="orders-list" id="ordersList"></div>
    <div class="no-orders" id="emptyState" style="display:none;">No orders found for this customer.</div>
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