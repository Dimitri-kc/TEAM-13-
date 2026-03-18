<?php
include '../backend/config/db_connect.php';
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Customer Management | Loft & Living</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Ibarra+Real+Nova:wght@600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="../css/header_footer_style.css?v=15">

  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: "Inter", sans-serif;
      background: #d9d6cf;
      color: #111;
      min-height: 100vh;
    }

    .page-wrap {
      width: 100%;
      display: flex;
      justify-content: center;
      padding: 24px 24px 40px;
    }

    .page-shell {
      width: 100%;
      max-width: 900px;
      background: #f7f7f5;
      padding: 28px 34px 28px;
      min-height: 620px;
    }

    .page-title {
      font-size: 22px;
      font-weight: 700;
      margin: 0 0 6px;
      font-family: "Inter", sans-serif;
      line-height: 1.1;
    }

    .page-sub {
      color: #7b7b7b;
      font-size: 12px;
      margin: 0 0 14px;
      line-height: 1.2;
    }

    .search-row {
      margin-bottom: 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .search-controls {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .search-row input,
    .search-row select,
    .add-customer-btn {
      height: 32px;
      border: 1px solid #9f9f9f;
      background: #fff;
      padding: 0 10px;
      font-size: 12px;
      outline: none;
      border-radius: 0;
    }

    .search-row input {
      width: 190px;
    }

    .search-row select {
      width: 130px;
      cursor: pointer;
    }

    .add-customer-btn {
      min-width: 120px;
      text-decoration: none;
      color: #111;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-weight: 500;
      cursor: pointer;
      transition: background 0.2s ease, color 0.2s ease, border-color 0.2s ease;
    }

    .add-customer-btn:hover {
      background: #000;
      color: #fff;
      border-color: #000;
    }

    .error-popup {
      display: none;
      margin-top: 10px;
      margin-bottom: 16px;
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

    .recent-card {
      background: #fbfbfb;
      border: 1px solid #e3e3e3;
      border-radius: 4px;
      padding: 18px;
      max-width: 100%;
    }

    .recent-heading {
      font-size: 18px;
      font-weight: 700;
      margin: 0 0 18px;
      color: #111;
    }

    .recent-list {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 16px;
    }

    .customer-item {
      background: #fff;
      border: 1px solid #e3e3e3;
      border-radius: 4px;
      padding: 16px;
      display: grid;
      grid-template-columns: 72px 1fr;
      gap: 16px;
      align-items: center;
    }

    .avatar-box {
      width: 72px;
      height: 72px;
      border-radius: 10px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-weight: 700;
      font-size: 20px;
      letter-spacing: .5px;
      text-transform: uppercase;
    }

    .customer-name {
      font-size: 13px;
      font-weight: 600;
      margin: 0 0 4px;
      color: #222;
      line-height: 1.2;
    }

    .customer-meta {
      font-size: 11px;
      color: #777;
      margin: 0 0 10px;
      line-height: 1.3;
      word-break: break-word;
    }

    .view-btn {
      width: 100px;
      height: 30px;
      border: none;
      border-radius: 4px;
      background: #2f2f2f;
      color: #fff;
      cursor: pointer;
      font-size: 12px;
      font-weight: 500;
    }

    .no-results {
      display: none;
      margin-top: 16px;
      color: #666;
      font-size: 14px;
    }

    @media (max-width: 900px) {
      .recent-list {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 640px) {
      .page-shell {
        padding: 24px 16px 20px;
      }

      .search-row {
        flex-direction: column;
        align-items: flex-start;
      }

      .search-controls {
        flex-direction: column;
        align-items: flex-start;
        width: 100%;
      }

      .search-row input,
      .search-row select,
      .add-customer-btn {
        width: 100%;
        max-width: 220px;
      }
    }
  </style>
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=4">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body>
  <?php $headerPartialOnly = true; include 'header.php'; ?>

  <main class="page-wrap">
    <section class="page-shell">
      <h1 class="page-title">Customer Management</h1>
      <p class="page-sub">View current list of users, organised by recently signed up</p>

      <div class="search-row">
        <div class="search-controls">
          <input type="text" id="searchInput" placeholder="Search">
          <select id="roleFilter">
            <option value="all">All</option>
            <option value="customer">Customers</option>
            <option value="admin">Admins</option>
          </select>
        </div>

        <a href="admin_add_customer.php" class="add-customer-btn">Add Customer</a>
      </div>

      <div id="errorPopup" class="error-popup">
        <div class="error-icon">!</div>
        <div class="error-text" id="errorText"></div>
      </div>

      <div class="recent-card">
        <h2 class="recent-heading">Last 10 Signed Up Users</h2>
        <div class="recent-list" id="recentList"></div>
        <p class="no-results" id="noResults">No users found.</p>
      </div>
    </section>
  </main>

  <?php $footerPartialOnly = true; include 'footer.php'; ?>

  <script>
    const API_URL = "/TEAM-13-/Draft/backend/routes/adminRoutes.php";
    const recentList = document.getElementById("recentList");
    const searchInput = document.getElementById("searchInput");
    const roleFilter = document.getElementById("roleFilter");
    const noResults = document.getElementById("noResults");

    let customers = [];

    const colors = [
      "#1f3f73",
      "#0f9d72",
      "#d97a00",
      "#7c3aed",
      "#2f66e0",
      "#1777b7",
      "#b45309",
      "#be185d"
    ];

    function showPopup(message) {
      const popup = document.getElementById("errorPopup");
      const text = document.getElementById("errorText");
      text.textContent = message;
      popup.style.display = "flex";
    }

    function hidePopup() {
      document.getElementById("errorPopup").style.display = "none";
    }

    function getInitials(name, surname) {
      const a = (name || "").trim().charAt(0) || "U";
      const b = (surname || "").trim().charAt(0) || "S";
      return (a + b).toUpperCase();
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

    function renderCustomers(list) {
      recentList.innerHTML = "";
      noResults.style.display = list.length ? "none" : "block";

      list.forEach((customer, index) => {
        const item = document.createElement("div");
        item.className = "customer-item";

        const fullName = `${customer.name || ""} ${customer.surname || ""}`.trim();

        item.innerHTML = `
          <div class="avatar-box" style="background:${colors[index % colors.length]}">
            ${getInitials(customer.name, customer.surname)}
          </div>
          <div>
            <p class="customer-name">${fullName || "Unnamed User"}</p>
            <p class="customer-meta">${customer.email || ""}</p>
            <button class="view-btn" data-id="${customer.id}">View</button>
          </div>
        `;

        recentList.appendChild(item);
      });

      document.querySelectorAll(".view-btn").forEach(button => {
        button.addEventListener("click", () => {
          const id = button.dataset.id;
          window.location.href = `admin_customer_details.php?user_id=${encodeURIComponent(id)}`;
        });
      });
    }

    function applySearch() {
      const query = searchInput.value.trim().toLowerCase();
      const selectedRole = roleFilter.value.toLowerCase();

      const filtered = customers.filter(customer => {
        const fullName = `${customer.name || ""} ${customer.surname || ""}`.trim().toLowerCase();
        const email = (customer.email || "").toLowerCase();
        const role = (customer.role || "").toLowerCase();

        const matchesSearch = fullName.includes(query) || email.includes(query);
        const matchesRole = selectedRole === "all" || role === selectedRole;

        return matchesSearch && matchesRole;
      });

      renderCustomers(filtered);
    }

    async function loadCustomers() {
      hidePopup();

      const payload = {
        action: "customers_list"
      };

      try {
        const res = await fetch(API_URL, {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify(payload)
        });

        const data = await readJsonSafely(res);

        if (data.success && Array.isArray(data.customers)) {
          customers = data.customers.map(customer => ({
            id: customer.id || customer.user_id || customer.user_ID || "",
            name: customer.name || customer.first_name || "",
            surname: customer.surname || customer.last_name || "",
            email: customer.email || "",
            role: customer.role || ""
          }));
          renderCustomers(customers);
          return;
        }

        showPopup(data.message || "Unable to load customers.");
      } catch (error) {
        showPopup("Server error. Please try again.");
      }
    }

    searchInput.addEventListener("input", applySearch);
    roleFilter.addEventListener("change", applySearch);
    loadCustomers();
  </script>
</body>
</html>
