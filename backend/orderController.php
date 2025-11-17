<?php
include 'db_connect.php';  // connect to database

header('Content-Type: application/json');

// Check for action type (insert or fetch)
$action = $_GET['action'] ?? '';

if ($action === 'insert') {
    // collect data from POST
    $user_ID = $_POST['user_ID'] ?? 0;
    $total_price = $_POST['total_price'] ?? 0;
    $address = $_POST['address'] ?? '';

    $stmt = $conn->prepare(
        "INSERT INTO orders (user_ID, total_price, address) VALUES (?, ?, ?)"
    );
    $stmt->bind_param("ids", $user_ID, $total_price, $address);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "Order added successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => $conn->error]);
    }

    $stmt->close();
}

elseif ($action === 'fetch') {
    $result = $conn->query("SELECT * FROM orders ORDER BY order_date DESC");
    $orders = [];
    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
    echo json_encode($orders);
}

else {
    echo json_encode(["status" => "error", "message" => "Invalid action"]);
}

$conn->close();
?>
