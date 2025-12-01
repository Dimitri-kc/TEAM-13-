<?php
include_once __DIR__ . '/db_connect.php';
header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'insert') {
    $order_ID = $_POST['order_ID'] ?? 0;
    $product_ID = $_POST['product_ID'] ?? 0;
    $unit_price = $_POST['unit_price'] ?? 0.00;

    $stmt = $conn->prepare("INSERT INTO order_items (order_ID, product_ID, unit_price) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $order_ID, $product_ID, $unit_price);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order item added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $stmt->close();
}

elseif ($action === 'fetch') {
    $order_ID = $_GET['order_ID'] ?? 0;

    $stmt = $conn->prepare("SELECT * FROM order_items WHERE order_ID = ?");
    $stmt->bind_param("i", $order_ID);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode($items);
    $stmt->close();
}

else {
    echo json_encode(["status" => "error", "message" => "Invalid or missing action"]);
}

$conn->close();
?>
