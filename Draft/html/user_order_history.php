<?php
include '../backend/config/db_connect.php';
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

$returnedOrderIDs = [];
if (!empty($orders)) {
    $ids = array_column($orders, 'order_ID');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $rStmt = $conn->prepare("SELECT order_ID FROM returns WHERE order_ID IN ($placeholders)");
    $rStmt->bind_param(str_repeat('i', count($ids)), ...$ids);
    $rStmt->execute();
    $rResult = $rStmt->get_result();
    while ($row = $rResult->fetch_assoc()) {
        $returnedOrderIDs[] = $row['order_ID'];
    }
    $rStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LOFT & LIVING - My Recent Orders</title>

<link rel="stylesheet" href="../css/header_footer_style.css?v=21">
<link rel="stylesheet" href="../css/user_order_history.css?v=1">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>

    <style>
    .btn-returned {
        opacity: 0.5;
        cursor: not-allowed;
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

<div id="cancelModal" class="return-modal">
  <div class="return-modal-content">
    <span class="close-cancel">&times;</span>

    <h2 style="font-family: 'Playfair Display', serif; font-size: 20px; margin-bottom: 10px;">
      Cancel Order
    </h2>

    <form id="cancelForm">
      <input type="hidden" id="cancelOrderId">

      <label for="cancelReason">Reason for Cancellation</label>
      <select id="cancelReason" required>
        <option value="">Select a reason</option>
        <option value="changed_mind">Changed my mind</option>
        <option value="ordered_by_mistake">Ordered by mistake</option>
        <option value="delivery_time">Delivery time is too long</option>
        <option value="other">Other</option>
      </select>

      <label for="cancelDetails">Additional Details</label>
      <textarea id="cancelDetails"></textarea>

      <label for="cancelName">Your Name (optional)</label>
      <input type="text" id="cancelName">

      <button type="submit" class="submit-return-btn">Submit Cancellation</button>
    </form>
  </div>
</div>

<div id="successModal" class="success-modal">
  <div class="success-modal-content">
    <p id="successMessage">Your return request has been submitted.</p>
    <button onclick="document.getElementById('successModal').style.display='none'">OK</button>
  </div>
</div>

<body class="order-history-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<div class="user-container">

    <a href="#" onclick="goBack(event)" class="back-home">← Go Back</a>

    <h1 class="order-history-title">My Recent Orders</h1>
    <p class="subheader">View your recent orders and revisit anything you have bought before.</p>

    <div class="orders-grid">
        <?php if (empty($orders)): ?>
            <p class="orders-empty">You haven't placed any orders yet.</p>
        <?php else: ?>
            <?php foreach ($orders as $order): 
                $date = date("jS F Y", strtotime($order['order_date']));
                $imagePath = !empty($order['product_image'])
                    ? htmlspecialchars($order['product_image'])
                    : '../images/basket-images/placeholder.png';
                $isCancelled = strtolower((string)($order['order_status'] ?? '')) === 'cancelled';
                $isReturned = in_array($order['order_ID'], $returnedOrderIDs);
            ?>
            <div class="order-card">
                <div class="order-image-box">
                    <img src="<?= $imagePath ?>" alt="Product Image">
                </div>

                <div class="order-content">
                    <span class="order-date-label"><?= $date ?></span>

                    <div class="order-actions">
<?php if ($isCancelled): ?>
    <button class="btn-action btn-cancelled" disabled>Cancelled</button>
<?php elseif ($isReturned): ?>
    <button class="btn-action btn-returned" disabled>Returned</button>
<?php else: ?>
    <button class="btn-action btn-return-order" data-return data-order-id="<?= $order['order_ID'] ?>">Return Item</button>
    <button class="btn-action btn-cancel-order" data-cancel data-order-id="<?= $order['order_ID'] ?>">Cancel Order</button>
<?php endif; ?>



                        <a href="user_order_details.php?order_id=<?= $order['order_ID'] ?>" class="btn-action btn-view-order">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php $footerPartialOnly = true; include 'footer.php'; ?>


<script>
const returnModal = document.getElementById("returnModal");
const cancelModal = document.getElementById("cancelModal");
const closeReturn = document.querySelector(".close-return");
const closeCancel = document.querySelector(".close-cancel");
const returnItemSelect = document.getElementById("returnItem");
const successModal = document.getElementById("successModal");
const successMessage = document.getElementById("successMessage");

document.addEventListener("click", async (e) => {
    const btn = e.target.closest("[data-return]");
    if (btn) {
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
        return;
    }

    const cancelBtn = e.target.closest("[data-cancel]");
    if (!cancelBtn) return;

    document.getElementById("cancelOrderId").value = cancelBtn.dataset.orderId;
    cancelModal.style.display = "block";
});

closeReturn.onclick = () => {
    returnModal.style.display = "none";
};

closeCancel.onclick = () => {
    cancelModal.style.display = "none";
};

window.onclick = (event) => {
    if (event.target === returnModal) {
        returnModal.style.display = "none";
    }
    if (event.target === cancelModal) {
        cancelModal.style.display = "none";
    }
    if (event.target === successModal) {
        successModal.style.display = "none";
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
            successMessage.textContent = "Your return request has been submitted.";
            successModal.style.display = "block";
            returnModal.style.display = "none";
            this.reset();
            const orderId = document.getElementById("returnOrderId").value;
            const btn = document.querySelector(`[data-return][data-order-id="${orderId}"]`);
            const cancelBtn = document.querySelector(`[data-cancel][data-order-id="${orderId}"]`);
            if (btn) {
                btn.textContent = "Returned";
                btn.disabled = true;
                btn.classList.remove("btn-return-order");
                btn.classList.add("btn-returned");
                btn.removeAttribute("data-return");
            }
            if (cancelBtn) {
                cancelBtn.remove();
            }
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        alert("An error occurred: " + error.message);
    }
});

document.getElementById("cancelForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const formData = new FormData();
    formData.append("order_id", document.getElementById("cancelOrderId").value);
    formData.append("reason", document.getElementById("cancelReason").value);
    formData.append("details", document.getElementById("cancelDetails").value);
    formData.append("name", document.getElementById("cancelName").value);

    try {
        const response = await fetch("submit_cancel.php", {
            method: "POST",
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();

        if (result.status === "success") {
            successMessage.textContent = "Your cancellation request has been submitted.";
            successModal.style.display = "block";
            cancelModal.style.display = "none";
            this.reset();

            const orderId = document.getElementById("cancelOrderId").value;
            const cancelBtn = document.querySelector(`[data-cancel][data-order-id="${orderId}"]`);
            const returnBtn = document.querySelector(`[data-return][data-order-id="${orderId}"]`);

            if (cancelBtn) {
                cancelBtn.textContent = "Cancelled";
                cancelBtn.disabled = true;
                cancelBtn.classList.remove("btn-cancel-order");
                cancelBtn.classList.add("btn-cancelled");
                cancelBtn.removeAttribute("data-cancel");
            }

            if (returnBtn) {
                returnBtn.remove();
            }
        } else {
            alert("Error: " + result.message);
        }
    } catch (error) {
        alert("An error occurred: " + error.message);
    }
});

function goBack(e) {
    e.preventDefault();
    if (document.referrer && document.referrer !== window.location.href) {
        history.back();
    } else {
        window.location.href = "homepage.php";
    }
}
</script>
</body>
</html>
