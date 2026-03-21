<?php
include 'db_connect.php';

$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        oi.order_item_ID,
        oi.product_ID,
        p.name
    FROM order_items oi
    JOIN products p ON oi.product_ID = p.product_ID
    WHERE oi.order_ID = ?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}

echo json_encode($items);
?>