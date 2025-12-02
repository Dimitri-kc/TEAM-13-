<?php
// orderItemsController.php - Handles order item API requests
header('Content-Type: application/json');

include_once __DIR__ . '/../models/orderItemModel.php';

$orderItemModel = new OrderItemModel();
$action = $_GET['action'] ?? '';

/*
 * INSERT ORDER ITEM
 * URL: orderItemsController.php?action=insert
 */
if ($action === 'insert') {

    $order_ID   = $_POST['order_ID'] ?? 0;
    $product_ID = $_POST['product_ID'] ?? 0;
    $unit_price = $_POST['unit_price'] ?? 0.00;

    $success = $orderItemModel->addOrderItem($order_ID, $product_ID, $unit_price);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order item added successfully" : "Failed to add item"
    ]);
}

/*
 * FETCH ITEMS BY ORDER ID
 * URL: orderItemsController.php?action=fetch&order_ID=12
 */
elseif ($action === 'fetch') {

    $order_ID = $_GET['order_ID'] ?? 0;

    $items = $orderItemModel->getItemsByOrder($order_ID);
    echo json_encode($items);
}

/*
 * DELETE ORDER ITEM
 * URL: orderItemsController.php?action=delete
 */
elseif ($action === 'delete') {

    $order_item_ID = $_POST['order_item_ID'] ?? 0;

    $success = $orderItemModel->deleteOrderItem($order_item_ID);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order item deleted successfully" : "Failed to delete item"
    ]);
}

/*
 * UPDATE ORDER ITEM (unit price)
 * URL: orderItemsController.php?action=update
 */
elseif ($action === 'update') {

    $order_item_ID = $_POST['order_item_ID'] ?? 0;
    $unit_price    = $_POST['unit_price'] ?? 0.00;

    $success = $orderItemModel->updateOrderItem($order_item_ID, $unit_price);

    echo json_encode([
        "status"  => $success ? "success" : "error",
        "message" => $success ? "Order item updated" : "Failed to update item"
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
