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

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LOFT & LIVING - My Recent Orders</title>

<link rel="stylesheet" href="../css/header_footer_style.css?v=21">
<link rel="stylesheet" href="../css/user_order_history.css?v=2">
    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=12">
    <link rel="stylesheet" href="../css/reusable_header.css?v=11">
    <script src="../javascript/dark-mode.js"></script>

</head>

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
                $status = trim((string)($order['order_status'] ?? 'Pending'));
                $statusSlug = strtolower(str_replace(' ', '-', $status));
                $itemCount = 0;
                if (!empty($order['order_items_csv'])) {
                    $itemCount = count(array_filter(explode(',', (string)$order['order_items_csv'])));
                }
            ?>
            <div class="order-card">
                <div class="order-image-box">
                    <img src="<?= $imagePath ?>" alt="Product Image">
                </div>

                <div class="order-content">
                    <div class="order-heading-row">
                        <span class="order-date-label"><?= $date ?></span>
                        <span class="order-status-pill order-status-pill--<?= htmlspecialchars($statusSlug) ?>"><?= htmlspecialchars($status) ?></span>
                    </div>

                    <div class="order-meta-line">
                        <span>Order #UK<?= str_pad((string)$order['order_ID'], 5, '0', STR_PAD_LEFT) ?></span>
                        <span><?= $itemCount ?> <?= $itemCount === 1 ? 'item' : 'items' ?></span>
                    </div>

                    <div class="order-actions">
                        <a href="user_order_details.php?order_id=<?= $order['order_ID'] ?>" class="btn-action btn-view-order">View Order</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php $footerPartialOnly = true; include 'footer.php'; ?>
<script>
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
