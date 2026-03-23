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
  <link rel="stylesheet" href="../css/header_footer_style.css?v=21">
  <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
  <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
  <link rel="stylesheet" href="../css/dark-mode.css?v=12">
  <link rel="stylesheet" href="../css/reusable_header.css?v=11">
  <link rel="stylesheet" href="../css/admin_customer_management.css?v=3">
  <script src="../javascript/dark-mode.js"></script>
</head>

<body class="admin-customer-management-page">
  <?php $headerPartialOnly = true; include 'header.php'; ?>

  <main class="page-wrap">
    <section class="page-shell">
      <div class="page-header">
        <div class="page-header-copy">
          <h1 class="page-title">Customer Management</h1>
          <p class="page-sub">View current users, search quickly, and move into customer records without changing the existing admin workflow.</p>
        </div>

        <button
          type="button"
          class="return-btn"
          onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href='admin_dash.php'; }"
        >
          Return to Previous Page
        </button>
      </div>

      <div class="search-row page-toolbar">
        <div class="search-controls">
          <input type="text" id="searchInput" placeholder="Search">
          <select id="roleFilter">
            <option value="all">All</option>
            <option value="customer">Customers</option>
            <option value="admin">Admins</option>
          </select>
          <select id="limitSelect">
            <option value="10">Show 10</option>
            <option value="25">Show 25</option>
            <option value="50">Show 50</option>
            <option value="100">Show 100</option>
            <option value="0">Show All</option>
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
          <div class="customer-content">
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

      const limitVal = parseInt(document.getElementById("limitSelect").value);
      const heading = document.querySelector(".recent-heading");

      if (limitVal === 0) {
          heading.textContent = "All Signed Up Users";
      } else {
          heading.textContent = `Last ${limitVal} Signed Up Users`;
      }
      
      const payload = {
        action: "customers_list",
        limit: limitVal === 0 ? 999 : limitVal  // 0 = show all, send high number
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
    document.getElementById("limitSelect").addEventListener("change", loadCustomers);
    loadCustomers();
  </script>
</body>
</html>
