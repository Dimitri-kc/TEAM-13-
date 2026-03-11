<?php
require_once '../backend/services/userFunctions.php';
require_admin_page('/TEAM-13-/Draft/html/signin.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Admin - Orders & Shipments</title>

<link rel="stylesheet" href="../css/header_footer_style.css">
<link rel="stylesheet" href="../css/category-css/livingroom-base.css">
<link rel="stylesheet" href="../css/category-css/livingroom-structure.css">
<link rel="stylesheet" href="../css/category-css/livingroom-reusable.css">
<link rel="stylesheet" href="../css/category-css/livingroom-page.css">

<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #fff;
    margin: 0;
    padding: 40px 20px;
    color: #1a1a1a;
  }

  .admin-container {
    max-width: 900px;
    margin: 0 auto;
  }

  h1 {
    font-weight: 700;
    text-align: left;
    font-size: 30px;
    margin-bottom: 4px;
  }

  p.subheader {
    color: #6c6c6c;
    font-weight: 400;
    font-size: 14px;
    margin-top: 0;
    margin-bottom: 24px;
  }

  .orders-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    margin-bottom: 50px;
  }

  .order-card {
    border: 1px solid #e2e2e2;
    border-radius: 6px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .order-card img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
    background: #eee;
    flex-shrink: 0;
  }

  .order-details {
    flex-grow: 1;
    font-size: 14px;
  }

  .order-status {
    font-weight: 600;
    font-size: 12px;
    color: #333;
    margin-bottom: 4px;
  }

  .order-number {
    font-weight: 700;
    font-size: 16px;
    margin: 0 0 4px 0;
  }

  .customer-name {
    margin: 0;
    font-weight: 500;
    color: #555;
  }

  .order-actions {
    display: flex;
    gap: 10px;
  }

  button {
    border-radius: 6px;
    border: none;
    padding: 6px 14px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s;
    white-space: nowrap;
  }

  .btn-view-edit {
    background-color: #ddd;
    color: #333;
  }

  .btn-view-edit:hover {
    background-color: #ccc;
  }

  .btn-cancel {
    background-color: #2C2C2C;
    color: white;
  }

  .btn-cancel:hover {
    background-color: #1a1a1a;
  }

  .edit-panel-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.45);
    z-index: 999;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }

  .edit-panel {
    background: #fff;
    width: 100%;
    max-width: 500px;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
  }

  .edit-panel h2 {
    margin-top: 0;
    margin-bottom: 18px;
    font-size: 24px;
  }

  .edit-field {
    margin-bottom: 16px;
  }

  .edit-field label {
    display: block;
    font-weight: 600;
    margin-bottom: 6px;
    font-size: 14px;
  }

  .edit-field input,
  .edit-field select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #d8d8d8;
    border-radius: 8px;
    font-size: 14px;
    box-sizing: border-box;
  }

  .edit-panel-actions {
    display: flex;
    gap: 10px;
    margin-top: 18px;
  }

  .btn-save {
    background: #2C2C2C;
    color: #fff;
  }

  .btn-save:hover {
    background: #1a1a1a;
  }

  .btn-close {
    background: #ddd;
    color: #333;
  }

  .btn-close:hover {
    background: #ccc;
  }

  @media (max-width: 600px) {
    .orders-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
</head>
<body data-category="livingroom">

<header class="site-header">
  <div class="header-inner">
    <button class="menu-btn" id="menu-toggle-btn">
      <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon" id="menu-icon-img" />
    </button>

    <div class="logo-wrapper">
      <a href="homepage.php">
        <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo" />
      </a>
    </div>

    <div class="header-actions">
      <a href="favourites.php">
        <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon" />
      </a>
      <a href="signin.php">
        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon" />
      </a>
      <a href="basket.php" class="basket-icon">
        <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon" />
        <span id="basket-count">0</span>
      </a>
    </div>
  </div>

  <nav class="dropdown-panel" id="dropdown-nav">
    <ul class="nav-links">
      <li><a href="livingroom.php">Living Room</a></li>
      <li><a href="bathroom.php">Bathroom</a></li>
      <li><a href="bedroom.php">Bedroom</a></li>
      <li><a href="office.php">Office</a></li>
      <li><a href="kitchen.php">Kitchen</a></li>
      <li class="nav-divider"><a href="signin.php">My Account</a></li>
    </ul>
  </nav>
</header>

<div class="admin-container">
  <h1>Orders and Shipments</h1>
  <p class="subheader">View recent customer orders and make edits or cancel</p>

  <div class="orders-grid">

    <div class="order-card"
         data-order-id="UK12345"
         data-customer-name="FirstName LastName"
         data-status="Pending"
         data-image="https://via.placeholder.com/80">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Pending</p>
        <p class="order-number">Order #UK12345</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit" type="button">View & Edit</button>
        <button class="btn-cancel" type="button">Cancel</button>
      </div>
    </div>

    <div class="order-card"
         data-order-id="UK12346"
         data-customer-name="FirstName LastName"
         data-status="Shipped"
         data-image="https://via.placeholder.com/80">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Shipped</p>
        <p class="order-number">Order #UK12346</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit" type="button">View & Edit</button>
        <button class="btn-cancel" type="button">Cancel</button>
      </div>
    </div>

    <div class="order-card"
         data-order-id="UK12347"
         data-customer-name="FirstName LastName"
         data-status="Pending"
         data-image="https://via.placeholder.com/80">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Pending</p>
        <p class="order-number">Order #UK12347</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit" type="button">View & Edit</button>
        <button class="btn-cancel" type="button">Cancel</button>
      </div>
    </div>

    <div class="order-card"
         data-order-id="UK12348"
         data-customer-name="FirstName LastName"
         data-status="Pending"
         data-image="https://via.placeholder.com/80">
      <img src="https://via.placeholder.com/80" alt="Product Image" />
      <div class="order-details">
        <p class="order-status">Order Status: Pending</p>
        <p class="order-number">Order #UK12348</p>
        <p class="customer-name">Customer: FirstName LastName</p>
      </div>
      <div class="order-actions">
        <button class="btn-view-edit" type="button">View & Edit</button>
        <button class="btn-cancel" type="button">Cancel</button>
      </div>
    </div>

  </div>
</div>

<div class="edit-panel-overlay" id="editPanelOverlay">
  <div class="edit-panel">
    <h2>View & Edit Order</h2>

    <div class="edit-field">
      <label for="editOrderId">Order Number</label>
      <input type="text" id="editOrderId" readonly>
    </div>

    <div class="edit-field">
      <label for="editCustomerName">Customer Name</label>
      <input type="text" id="editCustomerName" readonly>
    </div>

    <div class="edit-field">
      <label for="editOrderStatus">Order Status</label>
      <select id="editOrderStatus">
        <option value="Pending">Pending</option>
        <option value="Processing">Processing</option>
        <option value="Shipped">Shipped</option>
        <option value="Delivered">Delivered</option>
        <option value="Cancelled">Cancelled</option>
      </select>
    </div>

    <div class="edit-panel-actions">
      <button class="btn-save" type="button" id="saveOrderChanges">Save Changes</button>
      <button class="btn-close" type="button" id="closeEditPanel">Close</button>
    </div>
  </div>
</div>

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-section social-links">
      <a href="#">
        <img src="../images/header_footer_images/icon-twitter.png" alt="Twitter" class="social-icon" />
      </a>
      <a href="#">
        <img src="../images/header_footer_images/icon-instagram.png" alt="Instagram" class="social-icon" />
      </a>
    </div>

    <div class="footer-section">
      <h4>Navigation</h4>
      <ul>
        <li><a href="homepage.php">Homepage</a></li>
        <li><a href="signin.php">My Account</a></li>
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
  const editPanelOverlay = document.getElementById('editPanelOverlay');
  const editOrderId = document.getElementById('editOrderId');
  const editCustomerName = document.getElementById('editCustomerName');
  const editOrderStatus = document.getElementById('editOrderStatus');
  const closeEditPanel = document.getElementById('closeEditPanel');
  const saveOrderChanges = document.getElementById('saveOrderChanges');

  let currentOrderCard = null;

  document.querySelectorAll('.btn-view-edit').forEach((button) => {
    button.addEventListener('click', function () {
      const orderCard = this.closest('.order-card');
      currentOrderCard = orderCard;

      const orderId = orderCard.dataset.orderId;
      const customerName = orderCard.dataset.customerName;
      const status = orderCard.dataset.status;

      editOrderId.value = orderId;
      editCustomerName.value = customerName;
      editOrderStatus.value = status;

      editPanelOverlay.style.display = 'flex';
    });
  });

  closeEditPanel.addEventListener('click', function () {
    editPanelOverlay.style.display = 'none';
    currentOrderCard = null;
  });

  editPanelOverlay.addEventListener('click', function (event) {
    if (event.target === editPanelOverlay) {
      editPanelOverlay.style.display = 'none';
      currentOrderCard = null;
    }
  });

  saveOrderChanges.addEventListener('click', async function () {
    if (!currentOrderCard) return;

    const newStatus = editOrderStatus.value;
    const orderId = currentOrderCard.dataset.orderId;

    try {
      await fetch('/backend/update_order_status.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({
          orderId: orderId,
          status: newStatus
        })
      });

      currentOrderCard.dataset.status = newStatus;

      const statusText = currentOrderCard.querySelector('.order-status');
      statusText.textContent = 'Order Status: ' + newStatus;

      editPanelOverlay.style.display = 'none';
      currentOrderCard = null;

    } catch (error) {
      console.error("Failed to update order", error);
    }
  });

  document.querySelectorAll('.btn-cancel').forEach((button) => {
    button.addEventListener('click', function () {
      const orderCard = this.closest('.order-card');
      orderCard.dataset.status = 'Cancelled';

      const statusText = orderCard.querySelector('.order-status');
      statusText.textContent = 'Order Status: Cancelled';
    });
  });
</script>

</body>
</html>