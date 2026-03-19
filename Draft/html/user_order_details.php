<?php
include '../backend/config/db_connect.php';
session_start();

if (!isset($_SESSION['user_ID'])) {
    header("Location: signin.php");
    exit();
}

$user_ID = (int)$_SESSION['user_ID'];
$order_ID = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if ($order_ID <= 0) {
    die("Invalid order.");
}

$stmt = $conn->prepare("
SELECT *
FROM orders
WHERE order_ID = ? AND user_ID = ?
");

$stmt->bind_param("ii", $order_ID, $user_ID);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    die("Order not found.");
}

$user_stmt = $conn->prepare("
SELECT name
FROM users
WHERE user_ID = ?
");

$user_stmt->bind_param("i", $user_ID);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();

$user_name = $user['name'] ?? 'Customer';

$item_stmt = $conn->prepare("
SELECT p.image
FROM order_items oi
JOIN products p ON oi.product_ID = p.product_ID
WHERE oi.order_ID = ?
LIMIT 1
");

$item_stmt->bind_param("i", $order_ID);
$item_stmt->execute();
$item = $item_stmt->get_result()->fetch_assoc();

$image = $item['image'] ?? '';

$addressParts = array_map('trim', explode(",", $order['address']));

$line1 = $addressParts[0] ?? '';
$line2 = $addressParts[1] ?? '';
$city = $addressParts[2] ?? '';
$county = $addressParts[3] ?? '';
$postcode = $addressParts[4] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Order Details</title>

<link rel="stylesheet" href="../css/header_footer_style.css?v=15">
<link rel="stylesheet" href="../css/user_order_details.css?v=1">

    <link rel="stylesheet" href="https://use.typekit.net/lll5xwi.css">
    <link rel="stylesheet" href="https://use.typekit.net/ehd2wqk.css">
    <link rel="stylesheet" href="../css/dark-mode.css?v=9">
    <link rel="stylesheet" href="../css/reusable_header.css?v=5">
    <script src="../javascript/dark-mode.js"></script>
</head>

<body class="order-details-page">

<?php $headerPartialOnly = true; include 'header.php'; ?>

<div class="back-home-wrap">
    <a href="#" onclick="goBack(event)" class="back-home">← Go Back</a>
</div>

    <div class="container">

<div class="left-section">

<h2>Order #UK<?= str_pad($order['order_ID'], 5, "0", STR_PAD_LEFT) ?></h2>

<p>Customer: <?= htmlspecialchars($user_name) ?></p>

<p>Status: Delivered</p>

<p>Date: <?= date("Y-m-d", strtotime($order['order_date'])) ?></p>

<div class="order-image">

<?php if($image): ?>
<img src="<?= htmlspecialchars($image) ?>">
<?php endif; ?>

</div>

</div>


<div class="right-section">

<h3 class="shipping-address-title">Shipping Address:</h3>

<div class="form-row">
<label> Name</label>
<div class="display-line">
<?= htmlspecialchars($user_name) ?>
</div>
</div>

<div class="form-row">
<label>Address Line 1</label>
<div class="display-line">
<?= htmlspecialchars($line1) ?>
</div>
</div>

<div class="form-row">
<label>Address Line 2</label>
<div class="display-line">
<?= htmlspecialchars($line2) ?>
</div>
</div>

<div class="form-row">
<label>Postcode</label>
<div class="display-line">
<?= htmlspecialchars($postcode) ?>
</div>
</div>

<div class="form-row">
<label>County</label>
<div class="display-line">
<?= htmlspecialchars($county) ?>
</div>
</div>

<div class="form-row">
<label>Country</label>
<div class="display-line">
United Kingdom
</div>
</div>

</div>

</div>


<?php $footerPartialOnly = true; include 'footer.php'; ?>
<script>
function goBack(e) {
    e.preventDefault();

    if (document.referrer && document.referrer !== window.location.href) {
        history.back();
    } else {
        window.location.href = "orders.php"; 
    }
}
</script>
</body>
</html>
