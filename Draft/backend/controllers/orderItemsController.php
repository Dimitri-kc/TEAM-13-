<?php
// orderItemsController.php
header('Content-Type: application/json');

require_once __DIR__ . '/orderItemsModel.php';

class OrderItemsController {

    private $model;

    public function __construct() {
        $this->model = new OrderItem();
    }

    // POST: add item to order
    public function insert() {

        $order_ID   = $_POST['order_ID'] ?? null;
        $product_ID = $_POST['product_ID'] ?? null;
        $unit_price = $_POST['unit_price'] ?? null;

        if (!$order_ID || !$product_ID || !$unit_price) {
            echo json_encode([
                "status" => "error",
                "message" => "Missing required fields"
            ]);
            return;
        }

        $success = $this->model->addOrderItem(
            $order_ID,
            $product_ID,
            $unit_price
        );

        echo json_encode(
            $success
                ? ["status" => "success", "message" => "Order item added"]
                : ["status" => "error", "message" => "Failed to add order item"]
        );
    }

    // GET: fetch items by order
    public function fetchByOrder() {

        $order_ID = $_GET['order_ID'] ?? null;

        if (!$order_ID) {
            echo json_encode([
                "status" => "error",
                "message" => "Order ID required"
            ]);
            return;
        }

        $items = $this->model->getItemsByOrder($order_ID);

        echo json_encode([
            "status" => "success",
            "data" => $items
        ]);
    }
}
