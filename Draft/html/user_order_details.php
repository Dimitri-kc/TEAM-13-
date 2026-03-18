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

<style>

body{
font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;
margin:0px;
color:#1a1a1a;
}


.container{
display:flex;
gap:40px;
max-width:1000px;
margin:40px auto;
}


.left-section{
flex:1;
padding:28px;
border-radius:14px;
border:1px solid #e6e6e6;
font-family:"Times New Roman", Georgia, serif;
}

.left-section h2{
font-weight:400;
font-size:26px;
margin-bottom:10px;
}

.left-section p{
font-size:18px;
margin:6px 0;
}

.right-section{
flex:1;
background:#f5f5f5;
padding:32px;
border-radius:14px;
border:1px solid #e0e0e0;
}


.order-image{
width:300px;
height:300px;
background:#d3d3d3;
border-radius:10px;
margin-top:20px;
overflow:hidden;
}

.order-image img{
width:100%;
height:100%;
object-fit:cover;
}


.shipping-address-title{
font-size:22px;
font-weight:800;
margin-bottom:20px;
}

.form-row{
display:grid;
grid-template-columns:180px 1fr;
margin-bottom:18px;
align-items:center;
}

.form-row label{
font-weight:700;
}

.display-line{
font-weight:500;
}



@media(max-width:900px){



.form-row{
grid-template-columns:1fr;
}

}

.back-home {
    display: inline-block;
    margin: 20px 0 0px 140px; /* top 20px, right 0, bottom 20px, left 70px */
    font-size: 14px;
    text-decoration: none;
    color: #111;
    font-weight: 500;
    transition: 0.2s ease;
}

.back-home:hover {
    text-decoration: underline;
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

<a href="#" onclick="goBack(event)" class="back-home">← Go Back</a>

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

//Go back button
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
