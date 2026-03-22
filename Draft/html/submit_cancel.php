<?php
include '../backend/config/db_connect.php';
require_once '../backend/services/orderItemStatus.php';
session_start();

header('Content-Type: application/json');

ensureOrderItemStatusColumn($conn);

if (!isset($_SESSION['user_ID']) || !is_numeric($_SESSION['user_ID'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_ID = (int) $_SESSION['user_ID'];
$order_ID = isset($_POST['order_id']) ? (int) $_POST['order_id'] : 0;
$reason = trim((string) ($_POST['reason'] ?? ''));
$scope = trim((string) ($_POST['cancel_scope'] ?? 'entire'));
$selectedIds = $_POST['order_item_ids'] ?? [];

if ($order_ID <= 0 || $reason === '') {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

if (!in_array($scope, ['entire', 'items'], true)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid cancellation option']);
    exit;
}

if (!is_array($selectedIds)) {
    $selectedIds = [$selectedIds];
}

$selectedIds = array_values(array_unique(array_filter(array_map('intval', $selectedIds))));

$orderStmt = $conn->prepare("
    SELECT order_ID, order_status
    FROM orders
    WHERE order_ID = ? AND user_ID = ?
    LIMIT 1
");

if ($orderStmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Unable to validate order']);
    exit;
}

$orderStmt->bind_param('ii', $order_ID, $user_ID);
$orderStmt->execute();
$order = $orderStmt->get_result()->fetch_assoc();
$orderStmt->close();

if (!$order) {
    echo json_encode(['status' => 'error', 'message' => 'Order not found']);
    exit;
}

$currentStatus = strtolower(trim((string) ($order['order_status'] ?? '')));
if ($currentStatus === 'cancelled') {
    echo json_encode(['status' => 'error', 'message' => 'Order is already cancelled']);
    exit;
}

$itemsStmt = $conn->prepare("
    SELECT oi.order_item_ID, oi.product_ID, oi.quantity, oi.unit_price, oi.item_status
    FROM order_items oi
    WHERE oi.order_ID = ?
    ORDER BY oi.order_item_ID
");

if ($itemsStmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Unable to load order items']);
    exit;
}

$itemsStmt->bind_param('i', $order_ID);
$itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);
$itemsStmt->close();

if (empty($items)) {
    echo json_encode(['status' => 'error', 'message' => 'There are no items left to cancel']);
    exit;
}

$activeItems = array_values(array_filter(
    $items,
    static fn($item) => strtolower((string)($item['item_status'] ?? 'active')) !== 'cancelled'
));

if (empty($activeItems)) {
    echo json_encode(['status' => 'error', 'message' => 'There are no active items left to cancel']);
    exit;
}

$allItemIds = array_map(static fn($item) => (int) $item['order_item_ID'], $activeItems);

if ($scope === 'items') {
    if (empty($selectedIds)) {
        echo json_encode(['status' => 'error', 'message' => 'Select at least one item to cancel']);
        exit;
    }

    foreach ($selectedIds as $selectedId) {
        if (!in_array($selectedId, $allItemIds, true)) {
            echo json_encode(['status' => 'error', 'message' => 'One or more selected items are invalid']);
            exit;
        }
    }
} else {
    $selectedIds = $allItemIds;
}

$isEntireCancellation = count($selectedIds) === count($allItemIds);
$itemsToCancel = array_values(array_filter(
    $activeItems,
    static fn($item) => in_array((int) $item['order_item_ID'], $selectedIds, true)
));

try {
    $conn->begin_transaction();

    $stockStmt = $conn->prepare("
        UPDATE products
        SET stock = stock + ?
        WHERE product_ID = ?
    ");

    if ($stockStmt === false) {
        throw new Exception('Unable to restore stock');
    }

    foreach ($itemsToCancel as $item) {
        $quantity = (int) ($item['quantity'] ?? 0);
        $product_ID = (int) ($item['product_ID'] ?? 0);
        $stockStmt->bind_param('ii', $quantity, $product_ID);
        if (!$stockStmt->execute()) {
            throw new Exception('Failed to update product stock');
        }
    }

    $stockStmt->close();

    $placeholders = implode(',', array_fill(0, count($selectedIds), '?'));
    $cancelStmt = $conn->prepare("
        UPDATE order_items
        SET item_status = 'Cancelled'
        WHERE order_ID = ? AND order_item_ID IN ($placeholders)
    ");

    if ($cancelStmt === false) {
        throw new Exception('Unable to mark selected items as cancelled');
    }

    $types = 'i' . str_repeat('i', count($selectedIds));
    $params = array_merge([$order_ID], $selectedIds);
    $cancelStmt->bind_param($types, ...$params);
    if (!$cancelStmt->execute()) {
        throw new Exception('Failed to cancel selected items');
    }
    $cancelStmt->close();

    $remainingStmt = $conn->prepare("
        SELECT
            COALESCE(SUM(CASE WHEN item_status <> 'Cancelled' THEN quantity * unit_price ELSE 0 END), 0) AS subtotal,
            SUM(CASE WHEN item_status <> 'Cancelled' THEN 1 ELSE 0 END) AS active_item_count
        FROM order_items
        WHERE order_ID = ?
    ");

    if ($remainingStmt === false) {
        throw new Exception('Unable to recalculate order total');
    }

    $remainingStmt->bind_param('i', $order_ID);
    $remainingStmt->execute();
    $remaining = $remainingStmt->get_result()->fetch_assoc();
    $remainingStmt->close();

    $remainingSubtotal = (float) ($remaining['subtotal'] ?? 0);
    $remainingCount = (int) ($remaining['active_item_count'] ?? 0);
    $updatedStatus = $remainingCount > 0 ? 'Partially Cancelled' : 'Cancelled';
    $updatedTotal = $remainingCount > 0 ? round($remainingSubtotal * 1.10, 2) : 0.00;

    $updateStmt = $conn->prepare("
        UPDATE orders
        SET order_status = ?, total_price = ?
        WHERE order_ID = ? AND user_ID = ?
    ");

    if ($updateStmt === false) {
        throw new Exception('Unable to update order summary');
    }

    $updateStmt->bind_param('sdii', $updatedStatus, $updatedTotal, $order_ID, $user_ID);
    if (!$updateStmt->execute()) {
        throw new Exception('Failed to update order summary');
    }
    $updateStmt->close();

    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'order_status' => $updatedStatus,
        'message' => $updatedStatus === 'Cancelled'
            ? 'Order cancelled successfully'
            : 'Selected items cancelled successfully'
    ]);
} catch (Throwable $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
