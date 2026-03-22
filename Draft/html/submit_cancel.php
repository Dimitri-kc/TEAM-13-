<?php
include '../backend/config/db_connect.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_ID']) || !is_numeric($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_ID = (int) $_SESSION['user_ID'];
$order_ID = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
$reason = trim((string) ($_POST['reason'] ?? ''));

if ($order_ID <= 0 || $reason === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

$stmt = $conn->prepare("
    SELECT order_status
    FROM orders
    WHERE order_ID = ? AND user_ID = ?
    LIMIT 1
");

if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Unable to validate order']);
    exit;
}

$stmt->bind_param('ii', $order_ID, $user_ID);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    exit;
}

if (strtolower((string) ($order['order_status'] ?? '')) === 'cancelled') {
    echo json_encode(['status' => 'error', 'message' => 'Order is already cancelled']);
    exit;
}

$updateStmt = $conn->prepare("
    UPDATE orders
    SET order_status = 'Cancelled'
    WHERE order_ID = ? AND user_ID = ?
");

if ($updateStmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Unable to cancel order']);
    exit;
}

$updateStmt->bind_param('ii', $order_ID, $user_ID);
$success = $updateStmt->execute();
$updateStmt->close();

if (!$success) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to cancel order']);
    exit;
}

echo json_encode(['status' => 'success']);
?>
