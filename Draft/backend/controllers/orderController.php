<?php
// orderController.php - Handles order-related API requests
header('Content-Type: application/json');

include_once __DIR__ . '/../models/orderModel.php';

$orderModel = new OrderModel();

$action = $_GET['action'] ?? '';

/*
 * INSERT ORDER
 * URL: orderController.php?action=insert
 */
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

/*
 * FETCH ALL ORDERS (ADMIN)
 * URL: orderController.php?action=fetch
 */
elseif ($action === 'fetch') {

    $orders = $orderModel->getAllOrders();
    echo json_encode($orders);
}

/*
 * FETCH ORDERS BY USER
 * URL: orderController.php?action=user_orders&user_ID=5
 */
elseif ($action === 'user_orders') {

    $user_ID = $_GET['user_ID'] ?? 0;

    $orders = $orderModel->getOrdersByUser($user_ID);
    echo json_encode($orders);
}

/*
 * UPDATE ORDER STATUS
 * URL: orderController.php?action=update_status
 */
elseif ($action === 'update_status') {

    $order_ID     = $_POST['order_ID'] ?? 0;
    $order_status = $_POST['order_status'] ?? 'Pending';

    $success = $orderModel->updateOrderStatus($order_ID, $order_status);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order status updated" : "Failed to update status"
    ]);
}

/*
 * DELETE ORDER
 * URL: orderController.php?action=delete
 */
elseif ($action === 'delete') {

    $order_ID = $_POST['order_ID'] ?? 0;

    $success = $orderModel->deleteOrder($order_ID);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order deleted successfully" : "Failed to delete order"
    ]);
}


/*
 * INVALID ACTION
 */
else {
    echo json_encode([
        "status"  => "error",
        "message" => "Invalid or missing action"
    ]);
}
?>
