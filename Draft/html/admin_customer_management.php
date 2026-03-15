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

  <link rel="stylesheet" href="../css/header_footer_style.css">

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

    .site-footer {
      width: 100%;
      background-color: #fff;
      margin-top: auto;
      padding: 24px 0 32px;
    }

    .footer-inner {
      width: 90%;
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 120px 1fr 1fr 1fr;
      gap: 26px;
      align-items: start;
    }

    .footer-section h4 {
      font-size: 13px;
      margin: 0 0 12px;
      font-weight: 600;
    }

    .footer-section ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .footer-section ul li {
      margin-bottom: 9px;
      font-size: 12px;
    }

    .footer-section ul li a {
      text-decoration: none;
      color: #333;
    }

    .social-links {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      padding-top: 2px;
    }

    .social-icon {
      width: 18px;
      height: 18px;
      object-fit: contain;
    }

    @media (max-width: 900px) {
      .footer-inner {
        grid-template-columns: 1fr 1fr;
      }

      .recent-list {
        grid-template-columns: 1fr;
      }
    }

    @media (max-width: 640px) {
      .page-shell {
        padding: 24px 16px 20px;
      }

      .footer-inner {
        grid-template-columns: 1fr;
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
</head>

<body>
  <header class="site-header">
    <div class="header-inner">
      <button class="menu-btn" id="menu-toggle-btn">
        <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img">
      </button>

      <div class="logo-wrapper">
        <a href="homepage.php">
          <img src="../images/header_footer_images/logo.png" alt="Loft & Living" class="main-logo">
        </a>
      </div>

      <div class="header-actions">
        <a href="basket.php"><img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon"></a>
        <a href="adminlogin.php"><img src="../images/header_footer_images/icon-user.png" alt="Admin Login" class="ui-icon"></a>
      </div>
    </div>

    <nav class="dropdown-panel" id="dropdown-nav">
      <ul class="nav-links">
        <li><a href="livingroom.php">Living Room</a></li>
        <li><a href="bathroom.php">Bathroom</a></li>
        <li><a href="bedroom.php">Bedroom</a></li>
        <li><a href="office.php">Office</a></li>
        <li><a href="kitchen.php">Kitchen</a></li>
        <li class="nav-divider"><a href="adminlogin.php">Admin Login</a></li>
      </ul>
    </nav>
  </header>

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

  <footer class="site-footer">
    <div class="footer-inner">
      <div class="footer-section social-links">
        <a href="#"><img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon"></a>
        <a href="#"><img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon"></a>
      </div>

      <div class="footer-section">
        <h4>Navigation</h4>
        <ul>
          <li><a href="homepage.php">Homepage</a></li>
          <li><a href="adminlogin.php">Admin Login</a></li>
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

  <script src="../javascript/header_footer_script.js"></script>
</body>
</html>