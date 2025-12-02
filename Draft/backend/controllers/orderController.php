<?php
// Order controller – handles order actions
header('Content-Type: application/json');

include_once __DIR__ . '/../models/orderModel.php';

$orderModel = new OrderModel();
$action = $_GET['action'] ?? '';

/* Add a new order */
if ($action === 'insert') {

    $user_ID     = $_POST['user_ID'] ?? 0;
    $total_price = $_POST['total_price'] ?? 0.00;
    $address     = $_POST['address'] ?? '';

    $success = $orderModel->addOrder($user_ID, $total_price, $address);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order added successfully" : "Failed to add order"
    ]);
}

/* Get all orders (admin) */
elseif ($action === 'fetch') {

    echo json_encode($orderModel->getAllOrders());
}

/* Get orders for a specific user */
elseif ($action === 'user_orders') {

    $user_ID = $_GET['user_ID'] ?? 0;
    echo json_encode($orderModel->getOrdersByUser($user_ID));
}

/* Update order status */
elseif ($action === 'update_status') {

    $order_ID     = $_POST['order_ID'] ?? 0;
    $order_status = $_POST['order_status'] ?? 'Pending';

    $success = $orderModel->updateOrderStatus($order_ID, $order_status);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order status updated" : "Failed to update status"
    ]);
}

/* Delete an order */
elseif ($action === 'delete') {

    $order_ID = $_POST['order_ID'] ?? 0;

    $success = $orderModel->deleteOrder($order_ID);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order deleted successfully" : "Failed to delete order"
    ]);
}

/* Invalid action */
else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid or missing action"
    ]);
}
?>
