<?php
include '../backend/config/db_connect.php';
session_start();

header("Content-Type: application/json");

if (!isset($_SESSION['user_ID'])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit;
}

$user_id = (int)$_SESSION['user_ID'];

$order_id = $_POST['order_id'] ?? null;
$order_item_id = $_POST['order_item_id'] ?? null;
$reason = $_POST['reason'] ?? null;
$details = $_POST['details'] ?? null;
$name = $_POST['name'] ?? null;

if (!$order_id || !$order_item_id || !$reason) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

$ownershipStmt = $conn->prepare("
    SELECT oi.order_item_ID
    FROM orders o
    JOIN order_items oi ON oi.order_ID = o.order_ID
    WHERE o.order_ID = ? AND o.user_ID = ? AND oi.order_item_ID = ?
    LIMIT 1
");
if ($ownershipStmt === false) {
    echo json_encode(["status" => "error", "message" => "Unable to validate order item"]);
    exit;
}
$ownershipStmt->bind_param("iii", $order_id, $user_id, $order_item_id);
$ownershipStmt->execute();
$ownershipResult = $ownershipStmt->get_result()->fetch_assoc();
$ownershipStmt->close();

if (!$ownershipResult) {
    echo json_encode(["status" => "error", "message" => "Invalid order item"]);
    exit;
}

// Insert into returns table
$stmt = $conn->prepare("
    INSERT INTO returns (order_ID, user_id, reason, status, created_at)
    VALUES (?, ?, ?, 'Requested', NOW())
");
if ($stmt === false) {
    echo json_encode(["status" => "error", "message" => "Prepare failed for returns: " . $conn->error]);
    exit;
}
$stmt->bind_param("iis", $order_id, $user_id, $reason);
if (!$stmt->execute()) {
    echo json_encode(["status" => "error", "message" => "Execute failed for returns: " . $stmt->error]);
    exit;
}

$return_id = $stmt->insert_id;
$stmt->close();

// Insert into return_items table
$stmt2 = $conn->prepare("
    INSERT INTO return_items (return_id, order_item_ID, quantity, reason)
    VALUES (?, ?, 1, ?)
");
if ($stmt2 === false) {
    echo json_encode(["status" => "error", "message" => "Prepare failed for return_items: " . $conn->error]);
    exit;
}
$stmt2->bind_param("iis", $return_id, $order_item_id, $details);
if (!$stmt2->execute()) {
    echo json_encode(["status" => "error", "message" => "Execute failed for return_items: " . $stmt2->error]);
    exit;
}
$stmt2->close();

// Get product_ID from order_items to update stock
$stmt3 = $conn->prepare("SELECT product_ID FROM order_items WHERE order_item_ID = ?");
if ($stmt3 === false) {
    echo json_encode(["status" => "error", "message" => "Prepare failed for select product_ID: " . $conn->error]);
    exit;
}
$stmt3->bind_param("i", $order_item_id);
if (!$stmt3->execute()) {
    echo json_encode(["status" => "error", "message" => "Execute failed for select product_ID: " . $stmt3->error]);
    exit;
}
$result = $stmt3->get_result();
$row = $result->fetch_assoc();
if (!$row) {
    echo json_encode(["status" => "error", "message" => "No product found for order_item_ID"]);
    exit;
}
$product_id = $row['product_ID'];
$stmt3->close();

// Update stock in products table
$stmt4 = $conn->prepare("UPDATE products SET stock = stock + 1 WHERE product_ID = ?");
if ($stmt4 === false) {
    echo json_encode(["status" => "error", "message" => "Prepare failed for update stock: " . $conn->error]);
    exit;
}
$stmt4->bind_param("i", $product_id);
if (!$stmt4->execute()) {
    echo json_encode(["status" => "error", "message" => "Execute failed for update stock: " . $stmt4->error]);
    exit;
}
$stmt4->close();

$conn->close();

echo json_encode(["status" => "success"]);
?>
