<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_ID']) || !is_numeric($_SESSION['user_ID'])) {
    header("Location: signin.php?redirect=orders");
    exit();
}

$user_ID = (int)$_SESSION['user_ID'];

$stmt = $conn->prepare("
    SELECT 
        o.order_ID, 
        o.order_status, 
        o.order_date,
        (
            SELECT p.image
            FROM order_items oi
            JOIN products p ON oi.product_ID = p.product_ID
            WHERE oi.order_ID = o.order_ID
            LIMIT 1
        ) AS product_image,
        (
            SELECT GROUP_CONCAT(CONCAT(oi.product_ID, ':', oi.quantity) SEPARATOR ',')
            FROM order_items oi
            WHERE oi.order_ID = o.order_ID
        ) AS order_items_csv
    FROM orders o
    WHERE o.user_ID = ?
    ORDER BY o.order_date DESC
");

$stmt->bind_param("i", $user_ID);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LOFT & LIVING - My Recent Orders</title>

<link rel="stylesheet" href="../css/header_footer_style.css">

<style>
body {
    font-family: 'Inter', Arial, sans-serif;
    background: #fff;
    margin: 0;
    color: #1a1a1a;
}


.user-container {
    max-width: 1100px;
    margin: 50px auto 100px auto; 
    padding: 0 40px;
}

h1 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 6px;
}

.subheader {
    color: #888;
    font-size: 14px;
    margin-bottom: 35px;
}

.orders-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

.order-card {
    border: 1px solid #f0f0f0;
    border-radius: 8px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 30px;
    transition: 0.2s ease;
}

.order-card:hover {
    box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}

.order-image-box {
    width: 120px;
    height: 120px;
    background-color: #f5f5f5;
    border-radius: 6px;
    overflow: hidden;
}

.order-image-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.order-content {
    flex-grow: 1;
}

.order-date-label {
    font-size: 18px;
    font-weight: 700;
    margin-bottom: 16px;
    display: block;
}

.order-actions {
    display: flex;
    gap: 12px;
}

.btn-action {
    padding: 10px 18px;
    font-size: 12px;
    border-radius: 6px;
    cursor: pointer;
    text-decoration: none;
    font-weight: 600;
    border: none;
    transition: 0.2s ease;
}

.btn-add-bag {
    background-color: #e8e8e8;
    color: #1a1a1a;
}

.btn-add-bag:hover {
    background-color: #ddd;
}

.btn-view-order {
    background-color: #2b2b2b;
    color: #fff;
}

.btn-view-order:hover {
    background-color: #000;
}


.custom-footer {
    border-top: 1px solid #f0f0f0;
    padding: 60px 0;
}

.footer-wrapper {
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 40px;
    display: grid;
    grid-template-columns: 120px 1fr 1fr 1fr;
    gap: 80px;
    align-items: start;
}

.footer-social {
    display: flex;
    gap: 16px;
    align-items: flex-start;
}

.footer-social img {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

.footer-column h4 {
    font-size: 14px;
    font-weight: 700;
    margin-bottom: 20px;
}

.footer-column ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-column li {
    margin-bottom: 12px;
}

.footer-column a {
    text-decoration: none;
    color: #666;
    font-size: 13px;
}

.footer-column a:hover {
    color: #000;
}
/* RETURNS MODAL — matches review modal styling */
.return-modal {
    display: none;
    position: fixed;
    z-index: 9999;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
}

.return-modal-content {
    background: #fff;
    width: 88%;
    max-width: 300px;
    margin: 3% auto;
    padding: 10px 14px;
    border-radius: 14px;
    border: 1px solid #E5E1DB;
    box-shadow: 0 8px 28px rgba(0,0,0,0.15);
}

.close-return {
    float: right;
    font-size: 20px;
    cursor: pointer;
    opacity: 0.6;
    transition: opacity 0.2s;
}
.close-return:hover { opacity: 1; }

#returnForm label {
    font-size: 12px;
    font-weight: 600;
    color: #2B2B2B;
    margin-bottom: 2px;
    display: block;
}

#returnForm select,
#returnForm textarea,
#returnForm input {
    width: 100%;
    margin-bottom: 6px;
    padding: 6px 8px;
    border-radius: 8px;
    border: 1px solid #E5E1DB;
    font-size: 13px;
    background: #FFFFFF;
    color: #2B2B2B;
}

#returnForm textarea {
    height: 55px;
    resize: vertical;
}

.submit-return-btn {
    width: 100%;
    padding: 8px;
    background: #B8AFA4;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    margin-top: 4px;
    transition: background 0.2s;
}
/* SUCCESS MODAL */
.success-modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
}

.success-modal-content {
    background: #fff;
    width: 88%;
    max-width: 250px;
    margin: 15% auto;
    padding: 20px;
    border-radius: 14px;
    border: 1px solid #E5E1DB;
    box-shadow: 0 8px 28px rgba(0,0,0,0.15);
    text-align: center;
}

.success-modal-content p {
    font-size: 16px;
    font-weight: 600;
    color: #2B2B2B;
    margin: 0;
}

.success-modal-content button {
    margin-top: 15px;
    padding: 8px 16px;
    background: #B8AFA4;
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
}
.success-modal-content button:hover { background: #C8B79C; }
    .orders-grid {
        grid-template-columns: 1fr;
    }

    .footer-wrapper {
        grid-template-columns: 1fr 1fr;
        gap: 40px;
    }

</style>
</head>


<div id="returnModal" class="return-modal">
  <div class="return-modal-content">
    <span class="close-return">&times;</span>

    <h2 style="font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 10px;">
      Return Item
    </h2>

    <form id="returnForm">

      <label for="returnItem">Select Item</label>
      <select id="returnItem" required>
          <option value="">Loading...</option>
      </select>

      <input type="hidden" id="returnOrderId">


      <label for="returnReason">Reason for Return</label>
      <select id="returnReason" required>
        <option value="">Select a reason</option>
        <option value="damaged">Item arrived damaged</option>
        <option value="wrong">Wrong item received</option>
        <option value="size">Incorrect size</option>
        <option value="other">Other</option>
      </select>

      <label for="returnDetails">Additional Details</label>
      <textarea id="returnDetails"></textarea>

      <label for="returnName">Your Name (optional)</label>
      <input type="text" id="returnName">

      <button type="submit" class="submit-return-btn">Submit Return</button>
    </form>
  </div>
</div>

<div id="successModal" class="success-modal">
  <div class="success-modal-content">
    <p>Your return request has been submitted.</p>
    <button onclick="document.getElementById('successModal').style.display='none'">OK</button>
  </div>
</div>

<body>

<header class="site-header">
  <div class="header-inner">
    <button class="menu-btn">
        <img src="../images/header_footer_images/icon-menu.png" alt="Menu" class="ui-icon">
    </button>

    <div class="logo-wrapper">
      <a href="homepage.php">
        <img src="../images/header_footer_images/logo.png" alt="LOFT & LIVING" class="main-logo">
      </a>
    </div>

    <div class="header-actions">
      <a href="favourites.php">
        <img src="../images/header_footer_images/icon-heart.png" alt="Favourites" class="ui-icon">
      </a>
      <a href="signin.php">
        <img src="../images/header_footer_images/icon-user.png" alt="My Account" class="ui-icon">
      </a>
      <a href="basket.php" class="basket-icon">
          <img src="../images/header_footer_images/icon-basket.png" alt="Basket" class="ui-icon">
          <span id="basket-count">0</span>
      </a>
    </div>
  </div>
</header>

<div class="user-container">
    <h1>My Recent Orders</h1>
    <p class="subheader">View your recent orders and add to your bag if you want to purchase it again</p>

    <div class="orders-grid">
        <?php if (empty($orders)): ?>
            <p>You haven't placed any orders yet.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $date = date("jS F Y", strtotime($order['order_date']));
                $imagePath = !empty($order['product_image'])
                    ? htmlspecialchars($order['product_image'])
                    : '../images/basket-images/placeholder.png';
            ?>
            <div class="order-card">
                <div class="order-image-box">
                    <img src="<?= $imagePath ?>" alt="Product Image">
                </div>

                <div class="order-content">
                    <span class="order-date-label"><?= $date ?></span>

                    <div class="order-actions">
<button data-return data-order-id="<?= $order['order_ID'] ?>">
    Return Item
</button>



                        <a href="user_order_details.php?order_id=<?= $order['order_ID'] ?>" class="btn-action btn-view-order">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<footer class="custom-footer">
  <div class="footer-wrapper">

    <div class="footer-social">
      <a href="#"><img src="../images/header_footer_images/icon-twitter.png" alt="X"></a>
      <a href="#"><img src="../images/header_footer_images/icon-instagram.png" alt="Instagram"></a>
    </div>

    <div class="footer-column">
      <h4>Navigation</h4>
      <ul>
        <li><a href="homepage.php">Homepage</a></li>
        <li><a href="signin.php">My Account</a></li>
        <li><a href="favourites.php">Favourites</a></li>
        <li><a href="basket.php">Basket</a></li>
      </ul>
    </div>

    <div class="footer-column">
      <h4>Categories</h4>
      <ul>
        <li><a href="livingroom.php">Living Room</a></li>
        <li><a href="office.php">Offices</a></li>
        <li><a href="kitchen.php">Kitchen</a></li>
        <li><a href="bathroom.php">Bathrooms</a></li>
        <li><a href="bedroom.php">Bedrooms</a></li>
      </ul>
    </div>

    <div class="footer-column">
      <h4>More...</h4>
      <ul>
        <li><a href="contact.php">Contact Us</a></li>
        <li><a href="about.php">About Us</a></li>
      </ul>
    </div>

  </div>
</footer>


<script>
const returnModal = document.getElementById("returnModal");
const closeReturn = document.querySelector(".close-return");
const returnItemSelect = document.getElementById("returnItem");

document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-return]");
    if (!btn) return;

    const orderId = btn.dataset.orderId;
    document.getElementById("returnOrderId").value = orderId;

    // Load items for this order
    const response = await fetch("get_order_items.php?order_id=" + orderId);
    const items = await response.json();

    // Populate dropdown
    returnItemSelect.innerHTML = '<option value="">Select an item</option>';
    items.forEach(item => {
        const opt = document.createElement("option");
        opt.value = item.order_item_ID;
        opt.textContent = item.name;
        returnItemSelect.appendChild(opt);
    });

    returnModal.style.display = "block";
});

closeReturn.onclick = () => {
    returnModal.style.display = "none";
};

window.onclick = (event) => {
    if (event.target === returnModal) {
        returnModal.style.display = "none";
    }
    if (event.target === document.getElementById("successModal")) {
        document.getElementById("successModal").style.display = "none";
    }
};

document.getElementById("returnForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("order_id", document.getElementById("returnOrderId").value);
    formData.append("order_item_id", document.getElementById("returnItem").value);
    formData.append("reason", document.getElementById("returnReason").value);
    formData.append("details", document.getElementById("returnDetails").value);
    formData.append("name", document.getElementById("returnName").value);

    try {
        const response = await fetch("submit_return.php", {
            method: "POST",
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.status === "success") {
            document.getElementById("successModal").style.display = "block";
            returnModal.style.display = "none";
            this.reset();
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        alert("An error occurred: " + error.message);
    }
});
</script>
</body>
</html>