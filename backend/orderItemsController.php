<?php
include 'db_connect.php';

header('Content-Type: application/json');


$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'insert') {
   
    $order_ID   = $_GET['order_ID'];
    $product_ID = $_GET['product_ID'];
    $unit_price = $_GET['unit_price'];
    $quantity   = $_GET['quantity'];

    $sql = "INSERT INTO order_items (order_ID, product_ID, unit_price, quantity)
            VALUES ('$order_ID', '$product_ID', '$unit_price', '$quantity')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "success", "message" => "Order item added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }
}

elseif ($action === 'fetch') {
   
    $order_ID = $_GET['order_ID'];

    $sql = "SELECT * FROM order_items WHERE order_ID = '$order_ID'";
    $result = $conn->query($sql);

    $items = [];
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    echo json_encode($items);
}

else {
    echo json_encode(["status" => "error", "message" => "Invalid or missing action"]);
}

$conn->close();
?>
